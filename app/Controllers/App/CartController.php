<?php

namespace App\Controllers\App;

use App\Controllers\BaseController;
use App\Models\CartModel;
use App\Models\ExploreContentModel;
use App\Models\ChapterModel;
use App\Models\WorkImageModel;
use App\Models\CreditModel;
use App\Models\TransactionModel;
use App\Models\UserModel;
use App\Models\UserProfileModel;
use App\Models\CreatorProfileModel;
use App\Services\NotificationService;
use Dompdf\Dompdf;
use Dompdf\Options;

class CartController extends BaseController
{
    protected CartModel $cartModel;
    protected ExploreContentModel $contentModel;

    public function __construct()
    {
        $this->cartModel    = new CartModel();
        $this->contentModel = new ExploreContentModel();
    }

    // ─────────────────────────────────────────────
    // GET /me/cart
    // ─────────────────────────────────────────────
    public function index()
    {
        $userId = session()->get('userId');
        if (!$userId) return redirect()->to('login');

        $userModel           = new UserModel();
        $userProfileModel    = new UserProfileModel();
        $creatorProfileModel = new CreatorProfileModel();
        $creditModel         = new CreditModel();

        $cartItems  = $this->cartModel->getCartWithWorks($userId);
        $userCredit = $creditModel->find($userId);
        $balance    = $userCredit ? (int)$userCredit['balance'] : 0;

        // Hitung total harga karya berbayar
        $totalPrice = 0;
        foreach ($cartItems as $item) {
            if ($item['is_paid'] && !$this->isOwnCartItem($item, $userId)) {
                $totalPrice += $this->getPurchasePrice($item);
            }
        }

        // Ambil session download yang baru saja di-checkout
        $downloadReady = session()->getFlashdata('cart_downloads') ?? [];

        $userProfile = $userProfileModel->find($userId);

        $data = [
            'cartItems'      => $cartItems,
            'totalPrice'     => $totalPrice,
            'balance'        => $balance,
            'shortfall'      => max(0, $totalPrice - $balance),
            'downloadReady'  => $downloadReady,
            'user'           => $userModel->find($userId),
            'username'       => session()->get('username'),
            'profile'        => [
                'profile_image' => $userProfile['profile_image'] ?? null,
                'display_name'  => $userProfile['display_name'] ?? session()->get('username'),
            ],
            'creatorProfile' => $creatorProfileModel->find($userId),
            'isLoggedIn'     => true,
            'activePage'     => 'cart',
        ];

        return view('me/cart', $data);
    }

    // ─────────────────────────────────────────────
    // POST /cart/add/(:num)  — AJAX
    // ─────────────────────────────────────────────
    public function add(int $workId)
    {
        $userId = session()->get('userId');
        if (!$userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Silakan login terlebih dahulu.'])->setStatusCode(401);
        }

        $work = $this->contentModel->find($workId);
        if (!$work) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Karya tidak ditemukan.'])->setStatusCode(404);
        }

        // Hanya karya downloadable yang bisa masuk cart
        $downloadable = ['image', 'text', 'novel', 'light_novel', 'comic'];
        if (!in_array($work['content_type'], $downloadable)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tipe karya ini tidak bisa diunduh.']);
        }

        if ((int)($work['allow_downloads'] ?? 1) !== 1) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Kreator menonaktifkan unduhan untuk karya ini.']);
        }

        if ($this->cartModel->inCart($userId, $workId)) {
            return $this->response->setJSON([
                'status'  => 'already',
                'message' => 'Sudah ada di keranjang.',
                'count'   => count($this->cartModel->getWorkIds($userId)),
            ]);
        }

        $this->cartModel->insert([
            'user_id'    => $userId,
            'work_id'    => $workId,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Ditambahkan ke keranjang!',
            'count'   => count($this->cartModel->getWorkIds($userId)),
        ]);
    }

    // ─────────────────────────────────────────────
    // POST /cart/remove/(:num)  — AJAX
    // ─────────────────────────────────────────────
    public function remove(int $workId)
    {
        $userId = session()->get('userId');
        if (!$userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Silakan login terlebih dahulu.'])->setStatusCode(401);
        }

        $this->cartModel->where('user_id', $userId)->where('work_id', $workId)->delete();

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Item dihapus dari keranjang.',
            'count'   => count($this->cartModel->getWorkIds($userId)),
        ]);
    }

    // ─────────────────────────────────────────────
    // POST /cart/checkout
    // ─────────────────────────────────────────────
    public function checkout()
    {
        $userId = session()->get('userId');
        if (!$userId) return redirect()->to('login');

        $cartItems = $this->cartModel->getCartWithWorks($userId);
        if (empty($cartItems)) {
            return redirect()->to('me/cart')->with('error', 'Keranjang Anda kosong.');
        }

        foreach ($cartItems as $item) {
            if ((int)($item['allow_downloads'] ?? 1) !== 1) {
                return redirect()->to('me/cart')->with('error', 'Sebagian karya di keranjang sudah tidak diizinkan untuk diunduh oleh kreator.');
            }
        }

        $creditModel      = new CreditModel();
        $transactionModel = new TransactionModel();

        $userCredit  = $creditModel->find($userId);
        $balance     = $userCredit ? (int)$userCredit['balance'] : 0;

        // Hitung total karya berbayar
        $totalPrice = 0;
        foreach ($cartItems as $item) {
            if ($item['is_paid'] && !$this->isOwnCartItem($item, $userId)) $totalPrice += $this->getPurchasePrice($item);
        }

        if ($balance < $totalPrice) {
            return redirect()->to('topup')->with('error', 'Saldo CC tidak mencukupi. Kekurangan: ' . number_format($totalPrice - $balance) . ' CC. Silakan top up terlebih dahulu.');
        }

        // Proses pembayaran dalam transaction database
        $db = \Config\Database::connect();
        $db->transStart();

        $newBalance = $balance;

        foreach ($cartItems as $item) {
            if (!$item['is_paid']) continue;
            if ($this->isOwnCartItem($item, $userId)) continue;

            $price = $this->getPurchasePrice($item);
            $newBalance -= $price;

            // Reward kreator
            $work = $this->contentModel->find($item['work_id']);
            if ($work) {
                $creatorCredit = $creditModel->find($work['creator_id']);
                if ($creatorCredit) {
                    $creditModel->update($work['creator_id'], [
                        'balance' => ((int)$creatorCredit['balance']) + $price,
                    ]);
                } else {
                    $creditModel->insert(['user_id' => $work['creator_id'], 'balance' => $price]);
                }
                // Catat transaksi kreator
                $_buyerUser = (new UserModel())->find($userId);
                $buyerUsername = '@' . ($_buyerUser['username'] ?? 'user_id:' . $userId);
                $_creatorUser = (new UserModel())->find($work['creator_id']);
                $creatorUsername = '@' . ($_creatorUser['username'] ?? 'user_id:' . $work['creator_id']);
                $transactionModel->record(
                    $work['creator_id'], $price, 'in', 'download',
                    $item['work_id'], 'Karya diunduh oleh ' . $buyerUsername . ': ' . $item['title']
                );
            }

            // Catat transaksi pembeli
            $transactionModel->record(
                $userId, $price, 'out', 'download',
                $item['work_id'], 'Download karya ' . $creatorUsername . ': ' . $item['title']
            );
        }

        // Update saldo pembeli
        if ($totalPrice > 0) {
            $creditModel->update($userId, ['balance' => $newBalance]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to('me/cart')->with('error', 'Gagal memproses pembayaran. Silakan coba lagi.');
        }

        // Kirim notifikasi per item ke masing-masing kreator
        $notifService = new NotificationService();
        $_buyerUser = (new UserModel())->find($userId);
        $buyerUsername = $_buyerUser['username'] ?? $userId;
        foreach ($cartItems as $item) {
            if (!$item['is_paid']) continue;
            if ($this->isOwnCartItem($item, $userId)) continue;
            $work = $this->contentModel->find($item['work_id']);
            if ($work && $work['creator_id'] !== $userId) {
                $notifService->notifyPurchase(
                    $work['creator_id'],
                    $buyerUsername,
                    $item['title'],
                    (int)$item['work_id']
                );
            }
        }

        // Simpan daftar work_id ke session biasa (tidak flash) supaya bisa diakses di download-all
        $downloadWorkIds = array_column($cartItems, 'work_id');
        session()->set('pending_downloads', $downloadWorkIds);

        // Kosongkan cart
        $this->cartModel->clearCart($userId);

        return redirect()->to('cart/download-all');
    }

    // ─────────────────────────────────────────────
    // GET /cart/download-all
    // Halaman auto-download setelah checkout
    // ─────────────────────────────────────────────
    public function downloadAll()
    {
        $userId = session()->get('userId');
        if (!$userId) return redirect()->to('login');

        $workIds = session()->get('pending_downloads') ?? [];

        if (empty($workIds)) {
            return redirect()->to('me/cart')->with('error', 'Tidak ada file yang perlu diunduh.');
        }

        // Bersihkan session setelah diambil
        session()->remove('pending_downloads');

        // Ambil info karya untuk ditampilkan
        $items = [];
        foreach ($workIds as $wid) {
            $work = $this->contentModel->findById((int)$wid);
            if ($work) {
                if ((int)($work['allow_downloads'] ?? 1) !== 1) {
                    continue;
                }
                $t = $work['content_type'];
                $items[] = [
                    'id'          => $wid,
                    'title'       => $work['title'],
                    'format'      => ($t === 'image') ? 'ZIP' : 'PDF',
                    'download_url'=> base_url('cart/download/' . $wid),
                ];
            }
        }

        $userModel = new UserModel();
        $creatorProfileModel = new CreatorProfileModel();

        $data = [
            'items'          => $items,
            'user'           => $userModel->find($userId),
            'username'       => session()->get('username'),
            'creatorProfile' => $creatorProfileModel->find($userId),
            'isLoggedIn'     => true,
            'activePage'     => 'cart',
        ];

        return view('me/cart_download', $data);
    }

    // ─────────────────────────────────────────────
    // GET /cart/download/(:num)
    // Download file setelah checkout berhasil
    // ─────────────────────────────────────────────
    public function download(int $workId)
    {
        $userId = session()->get('userId');
        if (!$userId) return redirect()->to('login');

        $work = $this->contentModel->findById($workId);
        if (!$work) {
            return redirect()->to('me/cart')->with('error', 'Karya tidak ditemukan.');
        }

        // Hanya karya downloadable
        $downloadable = ['image', 'text', 'novel', 'light_novel', 'comic'];
        if (!in_array($work['content_type'], $downloadable)) {
            return redirect()->to('me/cart')->with('error', 'Tipe karya ini tidak dapat diunduh.');
        }

        if ((int)($work['allow_downloads'] ?? 1) !== 1) {
            return redirect()->to('me/cart')->with('error', 'Kreator menonaktifkan unduhan untuk karya ini.');
        }

        $isOwner = ((string)($work['creator_id'] ?? '') === (string)$userId);

        // Untuk karya berbayar: validasi dari riwayat transaksi
        if ($work['is_paid'] && !$isOwner) {
            $transactionModel = new TransactionModel();
            $hasPurchased = $transactionModel
                ->where('user_id', $userId)
                ->where('reference_id', $workId)
                ->where('category', 'download')
                ->where('type', 'out')
                ->countAllResults() > 0;

            if (!$hasPurchased) {
                return $this->response
                    ->setStatusCode(403)
                    ->setBody('Akses ditolak. Silakan lakukan checkout terlebih dahulu.');
            }
        }

        // Lepas session lock sebelum proses file berat
        // agar request berikutnya (iframe/fetch lain) tidak deadlock nunggu session
        session()->close();

        if ($work['content_type'] === 'image') {
            return $this->_downloadImagesAsZip($work, $userId);
        } else {
            return $this->_downloadChaptersAsPdf($work, $userId);
        }
    }

    // ─── Private: ZIP untuk karya gambar ─────────
    private function _downloadImagesAsZip(array $work, string $buyerUserId = '')
    {
        $imageModel = new WorkImageModel();
        $images     = $imageModel->getByWork($work['id']);

        if (empty($images)) {
            return redirect()->to('me/cart')->with('error', 'Tidak ada gambar yang bisa diunduh.');
        }

        $zip     = new \ZipArchive();
        $zipName = 'NusaShare_' . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $work['title']) . '.zip';
        $zipDir  = WRITEPATH . 'downloads/';
        if (!is_dir($zipDir)) mkdir($zipDir, 0777, true);
        $zipPath = $zipDir . $zipName;
        $password = $this->getDownloadPassword($buyerUserId);

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            $zip->setPassword($password);
            $encryptionFailed = false;

            foreach ($images as $index => $img) {
                $path = $img['file_path'];
                if (filter_var($path, FILTER_VALIDATE_URL)) {
                    try {
                        $client   = \Config\Services::curlrequest();
                        $response = $client->get($path, ['timeout' => 10, 'http_errors' => false, 'verify' => false]);
                        if ($response->getStatusCode() === 200) {
                            $entryName = 'image_' . ($index + 1) . '.jpg';
                            $zip->addFromString($entryName, $response->getBody());
                            $encryptionFailed = !$this->protectZipEntry($zip, $entryName) || $encryptionFailed;
                        }
                    } catch (\Exception $e) { /* skip */ }
                } else {
                    $fullPath = ROOTPATH . 'public/' . rawurldecode($path);
                    if (file_exists($fullPath)) {
                        $ext = pathinfo($fullPath, PATHINFO_EXTENSION) ?: 'jpg';
                        $entryName = 'image_' . ($index + 1) . '.' . $ext;
                        $zip->addFile($fullPath, $entryName);
                        $encryptionFailed = !$this->protectZipEntry($zip, $entryName) || $encryptionFailed;
                    }
                }
            }
            $zip->close();

            if ($encryptionFailed) {
                @unlink($zipPath);
                return redirect()->to('me/cart')->with('error', 'Gagal memberi password pada file ZIP. Pastikan ekstensi ZIP server mendukung enkripsi.');
            }

            if (file_exists($zipPath)) {
                return $this->response->download($zipPath, null)->setFileName($zipName);
            }
        }

        return redirect()->to('me/cart')->with('error', 'Gagal membuat file ZIP.');
    }

    private function getDownloadPassword(string $buyerUserId): string
    {
        return $buyerUserId !== '' ? $buyerUserId : 'NusaShare';
    }

    private function protectZipEntry(\ZipArchive $zip, string $entryName): bool
    {
        if (!method_exists($zip, 'setEncryptionName')) {
            return false;
        }

        $method = defined('\ZipArchive::EM_AES_256')
            ? \ZipArchive::EM_AES_256
            : \ZipArchive::EM_TRAD_PKWARE;

        return $zip->setEncryptionName($entryName, $method);
    }

    private function protectDompdf(Dompdf $dompdf, string $password): void
    {
        $canvas = $dompdf->getCanvas();
        if (method_exists($canvas, 'get_cpdf')) {
            $ownerPassword = hash('sha256', $password . '|NusaShare|owner');
            $canvas->get_cpdf()->setEncryption($password, $ownerPassword, ['print']);
        }
    }

    // ─── Private: PDF untuk karya teks/novel ─────
    private function getPurchasePrice(array $item): int
    {
        $purchasePrice = (int)($item['purchase_price'] ?? 0);

        return $purchasePrice > 0 ? $purchasePrice : (int)($item['price'] ?? 0);
    }

    private function isOwnCartItem(array $item, string $userId): bool
    {
        return (string)($item['creator_id'] ?? '') === (string)$userId;
    }

    private function _downloadChaptersAsPdf(array $work, string $buyerUserId = '')
    {
        $chapterModel = new ChapterModel();
        $chapters     = $chapterModel->getByWork($work['id']);

        if (empty($chapters)) {
            return redirect()->to('me/cart')->with('error', 'Tidak ada bab yang tersedia untuk diunduh.');
        }

        // Ambil info pembeli dari database agar pasti valid
        $userModel     = new UserModel();
        $buyerUser     = ($buyerUserId !== '') ? $userModel->find($buyerUserId) : null;
        $buyerUsername = $buyerUser['username'] ?? $buyerUserId;

        // Build HTML konten
        $footerTitle = preg_replace('/\s+/', ' ', strip_tags((string)$work['title']));
        $title       = htmlspecialchars($work['title'], ENT_QUOTES, 'UTF-8');
        $creator     = htmlspecialchars($work['creator_name'], ENT_QUOTES, 'UTF-8');
        $buyerName   = htmlspecialchars((string)$buyerUsername, ENT_QUOTES, 'UTF-8');
        $buyerId     = htmlspecialchars((string)$buyerUserId, ENT_QUOTES, 'UTF-8');
        $generatedAt = date('d M Y, H:i') . ' WIB';
        $chapterTotal = count($chapters);
        $typeMap = [
            'novel'       => 'Novel',
            'light_novel' => 'Light Novel',
            'comic'       => 'Comic',
            'text'        => 'Teks',
        ];
        $typeLabel = $typeMap[$work['content_type']] ?? ucfirst($work['content_type']);

        $html  = '<!DOCTYPE html><html><head><meta charset="UTF-8">';
        $html .= '<style>';
        $html .= '@page{margin:22mm 20mm 24mm;}';
        $html .= 'body{font-family:"DejaVu Sans",Arial,sans-serif;color:#172033;margin:0;padding:0;font-size:11.2pt;line-height:1.72;background:#fff;}';
        $html .= '.cover{height:297mm;margin:-22mm -20mm -24mm;background:#f8fafc;color:#0f172a;page-break-after:always;position:relative;overflow:hidden;}';
        $html .= '.cover-top{height:9mm;background:#111827;}';
        $html .= '.cover-mark{position:absolute;right:-34mm;top:22mm;width:118mm;height:118mm;border-radius:80mm;background:#4f46e5;opacity:.11;}';
        $html .= '.cover-mark.two{right:122mm;top:218mm;width:70mm;height:70mm;background:#f59e0b;opacity:.18;}';
        $html .= '.cover-inner{padding:34mm 26mm 0;}';
        $html .= '.brand-row{font-size:9pt;font-weight:800;letter-spacing:3px;text-transform:uppercase;color:#4f46e5;margin-bottom:42mm;}';
        $html .= '.type-pill{display:inline-block;background:#eef2ff;color:#4338ca;border:1px solid #c7d2fe;border-radius:18px;padding:6px 13px;font-size:8pt;font-weight:800;letter-spacing:2px;text-transform:uppercase;}';
        $html .= '.cover h1{font-family:Georgia,"DejaVu Serif",serif;font-size:35pt;font-weight:700;line-height:1.12;margin:18px 0 14px;color:#0f172a;}';
        $html .= '.cover .by{font-size:13pt;color:#475569;margin:0 0 26mm;}';
        $html .= '.cover .by strong{color:#111827;}';
        $html .= '.meta-grid{width:100%;border-collapse:separate;border-spacing:0 9px;margin-top:10mm;}';
        $html .= '.meta-grid td{background:#fff;border-top:1px solid #e5e7eb;border-bottom:1px solid #e5e7eb;padding:11px 14px;font-size:9.2pt;}';
        $html .= '.meta-grid td:first-child{border-left:4px solid #4f46e5;border-radius:7px 0 0 7px;color:#64748b;font-weight:800;text-transform:uppercase;letter-spacing:1.4px;width:38%;}';
        $html .= '.meta-grid td:last-child{border-right:1px solid #e5e7eb;border-radius:0 7px 7px 0;color:#111827;font-weight:700;}';
        $html .= '.ownership{position:absolute;left:26mm;right:26mm;bottom:28mm;border-top:1px solid #dbe3ef;padding-top:9mm;color:#475569;font-size:8.8pt;line-height:1.55;}';
        $html .= '.ownership strong{color:#111827;}';
        $html .= '.page-break{page-break-before:always;}';
        $html .= '.chapter{padding:0;}';
        $html .= '.chapter-kicker{font-size:8.5pt;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:#4f46e5;margin:0 0 8px;}';
        $html .= '.chapter h2{font-family:Georgia,"DejaVu Serif",serif;font-size:23pt;font-weight:700;line-height:1.2;margin:0 0 24px;color:#111827;border-bottom:1px solid #dbe3ef;padding-bottom:14px;}';
        $html .= '.chapter p{font-family:Georgia,"DejaVu Serif",serif;text-indent:1.8em;margin:0 0 12px;color:#1f2937;}';
        $html .= '.chapter p:first-of-type{text-indent:0;}';
        $html .= '.empty-note{font-style:italic;color:#64748b;background:#f8fafc;border-left:4px solid #cbd5e1;padding:12px 14px;text-indent:0;}';
        $html .= '.comic-page{page-break-inside:avoid;margin:0 0 18px;text-align:center;background:#f8fafc;border:1px solid #e5e7eb;padding:8px;}';
        $html .= '.comic-page img{max-width:100%;height:auto;display:block;margin:0 auto;}';
        $html .= '</style></head><body>';

        // Cover page
        $html .= '<div class="cover">';
        $html .= '<div class="cover-top"></div><div class="cover-mark"></div><div class="cover-mark two"></div>';
        $html .= '<div class="cover-inner">';
        $html .= '<div class="brand-row">NusaShare</div>';
        $html .= '<div class="type-pill">' . $typeLabel . '</div>';
        $html .= '<h1>' . $title . '</h1>';
        $html .= '<div class="by">oleh <strong>' . $creator . '</strong></div>';
        $html .= '<table class="meta-grid">';
        $html .= '<tr><td>Format</td><td>' . $typeLabel . '</td></tr>';
        $html .= '<tr><td>Jumlah bab</td><td>' . number_format($chapterTotal) . '</td></tr>';
        $html .= '<tr><td>Dicetak</td><td>' . htmlspecialchars($generatedAt, ENT_QUOTES, 'UTF-8') . '</td></tr>';
        $html .= '<tr><td>Pemilik</td><td>@' . $buyerName . '</td></tr>';
        $html .= '</table>';
        $html .= '</div>';
        $html .= '<div class="ownership"><strong>Dokumen kepemilikan digital.</strong><br>File ini dibuat untuk akun @' . $buyerName . ' dengan User ID ' . $buyerId . '. Mohon tidak mendistribusikan ulang tanpa izin kreator.</div>';
        $html .= '</div>';

        // Chapters
        foreach ($chapters as $i => $chapter) {
            $chapterClass = $i === 0 ? 'chapter' : 'chapter page-break';
            $html .= '<div class="' . $chapterClass . '">';
            $html .= '<div class="chapter-kicker">Bab ' . ($chapter['order_num'] ?? ($i + 1)) . '</div>';
            $html .= '<h2>' . htmlspecialchars($chapter['title'], ENT_QUOTES, 'UTF-8') . '</h2>';
            if ($work['content_type'] === 'comic') {
                $html .= $this->renderComicChapterForPdf($chapter['body'] ?? '');
            } elseif (!empty($chapter['body'])) {
                $body = $this->normalizeChapterBodyForPdf($chapter['body']);
                $paragraphs = preg_split('/\r?\n\r?\n/', trim($body));

                foreach ($paragraphs as $p) {
                    $p = trim($p);
                    if ($p === '') {
                        continue;
                    }

                    $html .= '<p>' . nl2br(htmlspecialchars($p)) . '</p>';
                }
            } else {
                $html .= '<p class="empty-note">Konten bab ini tidak tersedia.</p>';
            }
            $html .= '</div>';
        }

        $html .= '</body></html>';

        // Render HTML bab menjadi PDF asli sebelum dikirim ke browser.
        $fileName = 'NusaShare_' . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $work['title']) . '.pdf';

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $canvas = $dompdf->getCanvas();
        $fontMetrics = $dompdf->getFontMetrics();
        $footerFont = $fontMetrics->getFont('DejaVu Sans', 'normal');
        $canvas->page_text(42, 810, 'NusaShare - ' . $footerTitle, $footerFont, 8, [108, 117, 125]);
        $canvas->page_text(512, 810, 'Hal. {PAGE_NUM} / {PAGE_COUNT}', $footerFont, 8, [108, 117, 125]);
        $this->protectDompdf($dompdf, $this->getDownloadPassword($buyerUserId));

        $pdf = $dompdf->output();

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->setHeader('Content-Length', (string) strlen($pdf))
            ->setBody($pdf);
    }

    private function renderComicChapterForPdf(string $body): string
    {
        $images = json_decode($body, true);
        if (!is_array($images) || empty($images)) {
            return '<p><em>Tidak ada gambar dalam bab ini.</em></p>';
        }

        $html = '';
        foreach ($images as $image) {
            $src = $this->resolveChapterImageUrl((string)$image);
            $html .= '<div class="comic-page"><img src="' . htmlspecialchars($src, ENT_QUOTES, 'UTF-8') . '" alt="Halaman komik"></div>';
        }

        return $html;
    }

    private function resolveChapterImageUrl(string $path): string
    {
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        if (strpos($path, 'chapters/') === 0) {
            $parts = explode('/', $path);
            $workId = $parts[1] ?? 0;
            $file = $parts[2] ?? basename($path);

            return base_url('image/chapter/' . $workId . '/' . $file);
        }

        return base_url($path);
    }

    private function normalizeChapterBodyForPdf(string $body): string
    {
        $body = html_entity_decode($body, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $body = preg_replace('/<\s*br\s*\/?\s*>/i', "\n", $body);
        $body = preg_replace('/<\s*\/\s*p\s*>\s*<\s*p[^>]*>/i', "\n\n", $body);
        $body = preg_replace('/<\s*p[^>]*>/i', '', $body);
        $body = preg_replace('/<\s*\/\s*p\s*>/i', "\n\n", $body);
        $body = strip_tags($body);
        $body = preg_replace("/[ \t]+\r?\n/", "\n", $body);
        $body = preg_replace("/\n{3,}/", "\n\n", $body);

        return trim($body);
    }
}
