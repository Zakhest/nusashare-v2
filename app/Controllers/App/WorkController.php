<?php

namespace App\Controllers\App;

use App\Controllers\BaseController;
use App\Models\ExploreContentModel;
use App\Models\ChapterModel;
use App\Models\WorkImageModel;
use App\Models\UserModel;
use App\Models\CreatorProfileModel;
use App\Models\LikeModel;
use App\Models\CommentModel;
use App\Models\ReadingHistoryModel;
use App\Models\UnlockedChapterModel;
use App\Models\BookmarkModel;
use App\Services\NotificationService;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\TransactionModel;

class WorkController extends BaseController
{
    private const VIEW_THRESHOLD_SECONDS = 120;

    protected $contentModel;
    protected $chapterModel;
    protected $imageModel;
    protected $likeModel;
    protected $commentModel;

    public function __construct()
    {
        $this->contentModel = new ExploreContentModel();
        $this->chapterModel = new ChapterModel();
        $this->imageModel   = new WorkImageModel();
        $this->likeModel    = new LikeModel();
        $this->commentModel = new CommentModel();
        $this->readingHistoryModel = new ReadingHistoryModel();
        $this->unlockedChapterModel = new UnlockedChapterModel();
    }

    /**
     * GET /works/(:num)
     */
    public function show($id)
    {
        $work = $this->contentModel->findById($id);

        if (!$work) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Karya tidak ditemukan.");
        }

        if ($work['content_type'] === 'artikel') {
            $articleModel = new \App\Models\ArticleModel();
            $article = $articleModel->findByWorkId($id);
            if ($article) {
                return redirect()->to(base_url('artikel/' . $article['slug']));
            }
        }

        // View count ditangani via AJAX setelah 2 menit (threshold)
        $this->startViewThreshold((int)$id);

        $data = [
            'work'          => $work,
            'isLoggedIn'    => session()->get('isLoggedIn') ?? false,
            'likeCount'     => $this->likeModel->getCount($id),
            'hasLiked'      => false,
            'hasBookmarked' => false,
            'comments'      => $this->commentModel->getByWork($id),
            'inCart'        => false,
            'hasPurchased'  => false,
        ];

        // Jika belum login, simpan URL ini agar setelah login bisa kembali ke sini
        if (!session()->get('isLoggedIn')) {
            session()->set('redirect_url', current_url());
        }

        // Fetch user context if logged in (for navbar etc)
        $userId = session()->get('userId');
        if ($userId) {
            $userModel = new UserModel();
            $creatorProfileModel = new CreatorProfileModel();
            $bookmarkModel = new BookmarkModel();
            
            $data['user']          = $userModel->find($userId);
            $data['username']      = session()->get('username');
            $data['creatorProfile'] = $creatorProfileModel->find($userId);
            $data['hasLiked']      = $this->likeModel->hasLiked($userId, $id);
            $data['lastRead']      = $this->readingHistoryModel->getLastRead($userId, (int)$id);
            $data['hasBookmarked'] = $bookmarkModel->where('user_id', $userId)->where('work_id', $id)->countAllResults() > 0;
            
            $cartModel             = new \App\Models\CartModel();
            $data['inCart']        = $cartModel->inCart($userId, (int)$id);

            $transactionModel      = new TransactionModel();
            $data['hasPurchased']  = $transactionModel
                ->where('user_id', $userId)
                ->where('reference_id', $id)
                ->where('category', 'download')
                ->where('type', 'out')
                ->countAllResults() > 0;
        }

        // Fetch content based on type
        $chapterBased = ['text', 'novel', 'light_novel', 'comic'];
        if (in_array($work['content_type'], $chapterBased)) {
            $chapters = $this->chapterModel->getByWork($id);
            
            // Mark unlocked status if user is logged in
            if ($userId) {
                foreach ($chapters as &$chapter) {
                    $chapter['is_unlocked'] = $this->unlockedChapterModel->hasUnlocked($userId, (int)$chapter['id']);
                }
            }
            
            $data['chapters'] = $chapters;
        } elseif ($work['content_type'] === 'image') {
            $data['images'] = $this->imageModel->getByWork($id);
        }

        return view('works/show', $data);
    }

    /**
     * GET /works/(:num)/download
     */
    public function download($id)
    {
        $work = $this->contentModel->findById($id);

        if (!$work) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Karya tidak ditemukan.");
        }

        $downloadable = ['image', 'text', 'novel', 'light_novel', 'comic'];
        if (!in_array($work['content_type'], $downloadable)) {
            return redirect()->back()->with('error', 'Tipe karya ini tidak dapat diunduh.');
        }

        if ((int)($work['allow_downloads'] ?? 1) !== 1) {
            return redirect()->back()->with('error', 'Kreator menonaktifkan unduhan untuk karya ini.');
        }

        $userId = session()->get('userId');
        $isOwner = $userId && ((string)$work['creator_id'] === (string)$userId);

        if ($work['is_paid'] && !$isOwner) {
            if (!$userId) {
                return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu untuk mengunduh karya berbayar.');
            }
            $transactionModel = new TransactionModel();
            $hasPurchased = $transactionModel
                ->where('user_id', $userId)
                ->where('reference_id', $id)
                ->where('category', 'download')
                ->where('type', 'out')
                ->countAllResults() > 0;

            if (!$hasPurchased) {
                return redirect()->back()->with('error', 'Akses ditolak. Silakan lakukan checkout terlebih dahulu.');
            }
        }

        // Lepas session lock sebelum proses file berat
        session()->close();

        if ($work['content_type'] === 'image') {
            return $this->_downloadImagesAsZip($work, (string)($userId ?? ''));
        } else {
            return $this->_downloadChaptersAsPdf($work, (string)($userId ?? ''));
        }
    }

    /**
     * GET /works/(:num)/read/(:num)
     */
    public function read($workId, $chapterId)
    {
        $work = $this->contentModel->findById($workId);

        if (!$work) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Karya tidak ditemukan.");
        }

        $chapter = $this->chapterModel->find($chapterId);

        if (!$chapter || (int)$chapter['work_id'] !== (int)$workId) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Bab tidak ditemukan.");
        }

        // View count ditangani via AJAX setelah 2 menit (threshold)
        $this->startViewThreshold((int)$workId);

        // Get all chapters to determine prev/next
        $allChapters = $this->chapterModel->getByWork($workId);
        
        $prevChapter = null;
        $nextChapter = null;
        $currentIdx = -1;

        foreach ($allChapters as $idx => $c) {
            if ((int)$c['id'] === (int)$chapterId) {
                $currentIdx = $idx;
                break;
            }
        }

        if ($currentIdx > 0) {
            $prevChapter = $allChapters[$currentIdx - 1];
        }
        if ($currentIdx < count($allChapters) - 1) {
            $nextChapter = $allChapters[$currentIdx + 1];
        }

        $data = [
            'work'         => $work,
            'chapter'      => $chapter,
            'prevChapter'  => $prevChapter,
            'nextChapter'  => $nextChapter,
            'allChapters'  => $allChapters,
            'isLoggedIn'   => session()->get('isLoggedIn') ?? false,
        ];

        // Fetch user context if logged in
        $userId = session()->get('userId');
        if ($userId) {
            $userModel = new UserModel();
            $creatorProfileModel = new CreatorProfileModel();
            
            $data['user'] = $userModel->find($userId);
            $data['username'] = session()->get('username');
            $data['creatorProfile'] = $creatorProfileModel->find($userId);

            // Update reading progress
            $this->readingHistoryModel->updateProgress($userId, (int)$workId, (int)$chapterId);

            // Check if user has unlocked this chapter
            $data['is_unlocked'] = $this->unlockedChapterModel->hasUnlocked($userId, (int)$chapterId);
        } else {
            $data['is_unlocked'] = false;
        }

        // If chapter is locked and user hasn't unlocked it, we still pass 'chapter' 
        // but the view will handle hiding the body and showing the prompt.
        // Unless it's the creator of the work.
        $isCreator = $userId && $work['creator_id'] === $userId;
        if ($chapter['is_locked'] && !$data['is_unlocked'] && !$isCreator) {
            $chapter['body'] = null; // Don't send body content to client
        }

        // Fetch comments for this chapter
        $data['comments'] = $this->commentModel->getByChapter($chapterId);

        return view('works/read', $data);
    }

    /**
     * POST /works/(:num)/unlock
     */
    public function unlock($id)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Silakan login terlebih dahulu.'
            ])->setStatusCode(401);
        }

        $userId = session()->get('userId');
        $work   = $this->contentModel->find($id);

        if (!$work || !$work['is_paid']) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Karya tidak ditemukan atau tidak berbayar.'
            ]);
        }

        $price = (int) $work['price'];
        $creditModel = new \App\Models\CreditModel();
        $userCredit  = $creditModel->find($userId);
        $balance     = $userCredit ? $userCredit['balance'] : 0;

        if ($balance < $price) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Saldo CC tidak mencukupi. Silakan top up.'
            ]);
        }

        // Deduct credit and Reward creator
        $db = \Config\Database::connect();
        $db->transStart();
        
        // Reader deduct
        $creditModel->update($userId, [
            'balance' => $balance - $price
        ]);

        // Creator reward
        $creatorCredit = $creditModel->find($work['creator_id']);
        if ($creatorCredit) {
            $creditModel->update($work['creator_id'], [
                'balance' => ($creatorCredit['balance'] ?? 0) + $price
            ]);
        } else {
            $creditModel->insert((object)[
                'user_id' => $work['creator_id'],
                'balance' => $price
            ]);
        }

        // Record Transactions
        $transactionModel = new \App\Models\TransactionModel();
        $_buyerUser = (new \App\Models\UserModel())->find($userId);
        $buyerUsername = '@' . ($_buyerUser['username'] ?? 'user_id:' . $userId);
        $_creatorUser = (new \App\Models\UserModel())->find($work['creator_id']);
        $creatorUsername = '@' . ($_creatorUser['username'] ?? 'user_id:' . $work['creator_id']);
        $transactionModel->record($userId, $price, 'out', 'unlock', $id, 'Buka karya ' . $creatorUsername . ': ' . $work['title']);
        $transactionModel->record($work['creator_id'], $price, 'in', 'unlock', $id, 'Karya dibuka oleh ' . $buyerUsername . ': ' . $work['title']);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal memproses pembayaran.'
            ]);
        }

        $timer = $work['timer_duration'] ?: 30;
        session()->set('unlocked_' . $work['id'], time() + $timer);

        // Kirim notifikasi ke kreator bahwa karyanya dibuka
        (new NotificationService())->notifyUnlock(
            $work['creator_id'],
            $buyerUsername,
            $work['title'],
            (int)$id
        );

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Akses dibuka!',
            'balance' => $balance - $price,
            'timer'   => $timer,
            'watermark' => $work['watermark_text'] ?: session()->get('username')
        ]);
    }

    /**
     * POST /chapters/(:num)/unlock
     */
    public function unlockChapter($chapterId)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Silakan login terlebih dahulu.'
            ])->setStatusCode(401);
        }

        $userId  = session()->get('userId');
        $chapter = $this->chapterModel->find($chapterId);

        if (!$chapter) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Bab tidak ditemukan.'
            ]);
        }

        if (!$chapter['is_locked']) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Bab ini tidak terkunci.'
            ]);
        }

        if ($this->unlockedChapterModel->hasUnlocked($userId, $chapterId)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Bab sudah dibuka.'
            ]);
        }

        $price = (int) $chapter['price'];
        $creditModel = new \App\Models\CreditModel();
        $userCredit  = $creditModel->find($userId);
        $balance     = $userCredit ? $userCredit['balance'] : 0;

        if ($balance < $price) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Saldo CC tidak mencukupi untuk membuka bab ini.'
            ]);
        }

        // Deduct credit, Record unlock, and Reward creator
        $db = \Config\Database::connect();
        $db->transStart();
        
        // Reader deduct
        $creditModel->update($userId, [
            'balance' => $balance - $price
        ]);

        // Record unlock
        $this->unlockedChapterModel->insert((object) [
            'user_id'    => $userId,
            'chapter_id' => $chapterId
        ]);

        // Fetch work to get creator
        $work = $this->contentModel->find($chapter['work_id']);
        
        // Creator reward
        $creatorCredit = $creditModel->find($work['creator_id']);
        if ($creatorCredit) {
            $creditModel->update($work['creator_id'], [
                'balance' => ($creatorCredit['balance'] ?? 0) + $price
            ]);
        } else {
            $creditModel->insert((object)[
                'user_id' => $work['creator_id'],
                'balance' => $price
            ]);
        }

        // Record Transactions
        $transactionModel = new \App\Models\TransactionModel();
        $_buyerUser = (new \App\Models\UserModel())->find($userId);
        $buyerUsername = '@' . ($_buyerUser['username'] ?? 'user_id:' . $userId);
        $_creatorUser = (new \App\Models\UserModel())->find($work['creator_id']);
        $creatorUsername = '@' . ($_creatorUser['username'] ?? 'user_id:' . $work['creator_id']);
        $transactionModel->record($userId, $price, 'out', 'unlock', $chapterId, 'Buka bab ' . $creatorUsername . ': ' . $chapter['title'] . ' (Karya: ' . $work['title'] . ')');
        $transactionModel->record($work['creator_id'], $price, 'in', 'unlock', $chapterId, 'Bab dibuka oleh ' . $buyerUsername . ': ' . $chapter['title'] . ' (Karya: ' . $work['title'] . ')');

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal memproses pembayaran.'
            ]);
        }

        // Kirim notifikasi ke kreator bahwa babnya dibuka
        (new NotificationService())->notifyUnlockChapter(
            $work['creator_id'],
            $buyerUsername,
            $chapter['title'],
            $work['title'],
            (int)$chapter['work_id']
        );

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Bab berhasil dibuka!',
            'balance' => $balance - $price
        ]);
    }

    /**
     * POST /works/(:num)/view
     * Dipanggil oleh JavaScript setelah user membuka halaman selama >= 2 menit.
     * Tidak memerlukan login.
     */
    public function recordView($id)
    {
        // Pastikan hanya menerima request yang valid
        $work = $this->contentModel->find((int)$id);

        if (!$work) {
            return $this->response
                ->setHeader('Content-Type', 'application/json')
                ->setJSON(['status' => 'error', 'message' => 'Work not found.'])
                ->setStatusCode(404);
        }

        $workId = (int)$id;
        $session = session();
        $startedKey = $this->viewStartedKey($workId);
        $countedKey = $this->viewCountedKey($workId);

        if ($session->get($countedKey)) {
            return $this->response
                ->setHeader('Content-Type', 'application/json')
                ->setJSON([
                    'status'     => 'success',
                    'message'    => 'View already recorded for this session.',
                    'view_count' => (int)($work['view_count'] ?? 0),
                ]);
        }

        $startedAt = (int)($session->get($startedKey) ?? 0);
        if ($startedAt <= 0) {
            $this->startViewThreshold($workId);
            return $this->response
                ->setHeader('Content-Type', 'application/json')
                ->setJSON([
                    'status'            => 'pending',
                    'message'           => 'View threshold has not started yet.',
                    'remaining_seconds' => self::VIEW_THRESHOLD_SECONDS,
                ])
                ->setStatusCode(425);
        }

        $elapsed = time() - $startedAt;
        if ($elapsed < self::VIEW_THRESHOLD_SECONDS) {
            return $this->response
                ->setHeader('Content-Type', 'application/json')
                ->setJSON([
                    'status'            => 'pending',
                    'message'           => 'View threshold has not been reached.',
                    'remaining_seconds' => self::VIEW_THRESHOLD_SECONDS - $elapsed,
                ])
                ->setStatusCode(425);
        }

        $this->contentModel
            ->set('view_count', 'COALESCE(view_count, 0) + 1', false)
            ->where('id', $workId)
            ->update();

        $updatedWork = $this->contentModel->select('view_count')->find($workId);
        $newCount = (int)($updatedWork['view_count'] ?? (($work['view_count'] ?? 0) + 1));

        $session->set($countedKey, true);
        $session->remove($startedKey);

        return $this->response
            ->setHeader('Content-Type', 'application/json')
            ->setJSON([
                'status'     => 'success',
                'message'    => 'View recorded.',
                'view_count' => $newCount,
            ]);
    }

    private function startViewThreshold(int $workId): void
    {
        $session = session();

        if ($session->get($this->viewCountedKey($workId)) || $session->get($this->viewStartedKey($workId))) {
            return;
        }

        $session->set($this->viewStartedKey($workId), time());
    }

    private function viewStartedKey(int $workId): string
    {
        return 'view_started_work_' . $workId;
    }

    private function viewCountedKey(int $workId): string
    {
        return 'view_counted_work_' . $workId;
    }

    private function _downloadImagesAsZip(array $work, string $buyerUserId = '')
    {
        $images = $this->imageModel->getByWork($work['id']);

        if (empty($images)) {
            return redirect()->back()->with('error', 'Tidak ada gambar yang bisa diunduh.');
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
                return redirect()->back()->with('error', 'Gagal memberi password pada file ZIP. Pastikan ekstensi ZIP server mendukung enkripsi.');
            }

            if (file_exists($zipPath)) {
                return $this->response->download($zipPath, null)->setFileName($zipName);
            }
        }

        return redirect()->back()->with('error', 'Gagal membuat file ZIP.');
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

    private function _downloadChaptersAsPdf(array $work, string $buyerUserId = '')
    {
        $chapters = $this->chapterModel->getByWork($work['id']);

        if (empty($chapters)) {
            return redirect()->back()->with('error', 'Tidak ada bab yang tersedia untuk diunduh.');
        }

        // Ambil info pembeli dari database agar pasti valid
        $userModel     = new UserModel();
        $buyerUser     = ($buyerUserId !== '') ? $userModel->find($buyerUserId) : null;
        $buyerUsername = $buyerUser ? '@' . $buyerUser['username'] : 'Tamu';

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
        $html .= '<tr><td>Pemilik</td><td>' . $buyerName . '</td></tr>';
        $html .= '</table>';
        $html .= '</div>';
        $html .= '<div class="ownership"><strong>Dokumen kepemilikan digital.</strong><br>File ini dibuat untuk akun ' . $buyerName . ($buyerId !== '' ? ' dengan User ID ' . $buyerId : '') . '. Mohon tidak mendistribusikan ulang tanpa izin kreator.</div>';
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
