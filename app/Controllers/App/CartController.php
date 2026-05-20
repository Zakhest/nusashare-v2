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
use App\Models\CreatorProfileModel;

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

        $userModel          = new UserModel();
        $creatorProfileModel = new CreatorProfileModel();
        $creditModel        = new CreditModel();

        $cartItems  = $this->cartModel->getCartWithWorks($userId);
        $userCredit = $creditModel->find($userId);
        $balance    = $userCredit ? (int)$userCredit['balance'] : 0;

        // Hitung total harga karya berbayar
        $totalPrice = 0;
        foreach ($cartItems as $item) {
            if ($item['is_paid']) {
                $totalPrice += (int)$item['price'];
            }
        }

        // Ambil session download yang baru saja di-checkout
        $downloadReady = session()->getFlashdata('cart_downloads') ?? [];

        $data = [
            'cartItems'     => $cartItems,
            'totalPrice'    => $totalPrice,
            'balance'       => $balance,
            'shortfall'     => max(0, $totalPrice - $balance),
            'downloadReady' => $downloadReady,
            'user'          => $userModel->find($userId),
            'username'      => session()->get('username'),
            'creatorProfile' => $creatorProfileModel->find($userId),
            'isLoggedIn'    => true,
            'activePage'    => 'cart',
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

        $creditModel      = new CreditModel();
        $transactionModel = new TransactionModel();

        $userCredit  = $creditModel->find($userId);
        $balance     = $userCredit ? (int)$userCredit['balance'] : 0;

        // Hitung total karya berbayar
        $totalPrice = 0;
        foreach ($cartItems as $item) {
            if ($item['is_paid']) $totalPrice += (int)$item['price'];
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

            $price = (int)$item['price'];
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
                $transactionModel->record(
                    $work['creator_id'], $price, 'in', 'download',
                    $item['work_id'], 'Karya diunduh: ' . $item['title']
                );
            }

            // Catat transaksi pembeli
            $transactionModel->record(
                $userId, $price, 'out', 'download',
                $item['work_id'], 'Download karya: ' . $item['title']
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

        // Untuk karya berbayar: validasi dari riwayat transaksi
        if ($work['is_paid']) {
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

        if ($work['content_type'] === 'image') {
            return $this->_downloadImagesAsZip($work);
        } else {
            return $this->_downloadChaptersAsPdf($work);
        }
    }

    // ─── Private: ZIP untuk karya gambar ─────────
    private function _downloadImagesAsZip(array $work)
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

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            foreach ($images as $index => $img) {
                $path = $img['file_path'];
                if (filter_var($path, FILTER_VALIDATE_URL)) {
                    try {
                        $client   = \Config\Services::curlrequest();
                        $response = $client->get($path, ['timeout' => 10, 'http_errors' => false, 'verify' => false]);
                        if ($response->getStatusCode() === 200) {
                            $zip->addFromString('image_' . ($index + 1) . '.jpg', $response->getBody());
                        }
                    } catch (\Exception $e) { /* skip */ }
                } else {
                    $fullPath = ROOTPATH . 'public/' . rawurldecode($path);
                    if (file_exists($fullPath)) {
                        $ext = pathinfo($fullPath, PATHINFO_EXTENSION) ?: 'jpg';
                        $zip->addFile($fullPath, 'image_' . ($index + 1) . '.' . $ext);
                    }
                }
            }
            $zip->close();

            if (file_exists($zipPath)) {
                return $this->response->download($zipPath, null)->setFileName($zipName);
            }
        }

        return redirect()->to('me/cart')->with('error', 'Gagal membuat file ZIP.');
    }

    // ─── Private: PDF untuk karya teks/novel ─────
    private function _downloadChaptersAsPdf(array $work)
    {
        $chapterModel = new ChapterModel();
        $chapters     = $chapterModel->getByWork($work['id']);

        if (empty($chapters)) {
            return redirect()->to('me/cart')->with('error', 'Tidak ada bab yang tersedia untuk diunduh.');
        }

        // Build HTML konten
        $title   = htmlspecialchars($work['title']);
        $creator = htmlspecialchars($work['creator_name']);
        $typeMap = [
            'novel'       => 'Novel',
            'light_novel' => 'Light Novel',
            'comic'       => 'Comic',
            'text'        => 'Teks',
        ];
        $typeLabel = $typeMap[$work['content_type']] ?? ucfirst($work['content_type']);

        $html  = '<!DOCTYPE html><html><head><meta charset="UTF-8">';
        $html .= '<style>';
        $html .= 'body{font-family:Georgia,serif;color:#1a1a1a;margin:0;padding:0;font-size:13pt;line-height:1.8;}';
        $html .= '.cover{min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;background:linear-gradient(135deg,#4F46E5,#22D3EE);color:white;padding:40px;}';
        $html .= '.cover h1{font-size:32pt;font-weight:900;margin-bottom:12px;}';
        $html .= '.cover .type{font-size:10pt;letter-spacing:4px;text-transform:uppercase;opacity:.7;margin-bottom:8px;}';
        $html .= '.cover .by{font-size:12pt;opacity:.85;}';
        $html .= '.cover .brand{position:absolute;bottom:40px;font-size:9pt;opacity:.5;letter-spacing:2px;}';
        $html .= '.page-break{page-break-before:always;}';
        $html .= '.chapter{padding:60px 80px;}';
        $html .= '.chapter h2{font-size:18pt;font-weight:700;border-bottom:2px solid #4F46E5;padding-bottom:10px;margin-bottom:30px;color:#4F46E5;}';
        $html .= '.chapter p{text-indent:2em;margin:0 0 1em;}';
        $html .= '.watermark{position:fixed;bottom:20px;right:20px;font-size:8pt;color:#ccc;letter-spacing:1px;}';
        $html .= '</style></head><body>';

        // Cover page
        $html .= '<div class="cover">';
        $html .= '<div class="type">' . $typeLabel . '</div>';
        $html .= '<h1>' . $title . '</h1>';
        $html .= '<div class="by">oleh ' . $creator . '</div>';
        $html .= '<div class="brand">NusaShare &mdash; Platform Kreator Indonesia</div>';
        $html .= '</div>';

        // Chapters
        foreach ($chapters as $i => $chapter) {
            $html .= '<div class="chapter page-break">';
            $html .= '<h2>Bab ' . ($chapter['order_num'] ?? ($i + 1)) . ': ' . htmlspecialchars($chapter['title']) . '</h2>';
            if (!empty($chapter['body'])) {
                // Render paragraf dengan bersih
                $paragraphs = preg_split('/\r?\n\r?\n/', trim($chapter['body']));
                foreach ($paragraphs as $p) {
                    $html .= '<p>' . nl2br(htmlspecialchars(trim($p))) . '</p>';
                }
            } else {
                $html .= '<p><em>Konten bab ini tidak tersedia.</em></p>';
            }
            $html .= '</div>';
        }

        $html .= '<div class="watermark">NusaShare.id &copy; ' . date('Y') . '</div>';
        $html .= '</body></html>';

        // Kirim sebagai file HTML yang diunduh (bisa dibuka sebagai e-book di browser)
        // Namanya .html agar bisa dibuka rapi — user bisa Ctrl+P → Save as PDF
        $fileName = 'NusaShare_' . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $work['title']) . '.html';

        return $this->response
            ->setHeader('Content-Type', 'text/html; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->setBody($html);
    }
}
