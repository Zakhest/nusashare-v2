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

        if ($work['content_type'] !== 'image') {
            return redirect()->back()->with('error', 'Hanya karya gambar yang dapat diunduh.');
        }

        if ($work['is_paid']) {
            return redirect()->back()->with('error', 'Karya berbayar tidak bisa diunduh secara gratis saat ini.');
        }

        $images = $this->imageModel->getByWork($id);
        
        if (empty($images)) {
            return redirect()->back()->with('error', 'Tidak ada gambar yang bisa diunduh.');
        }

        $zip = new \ZipArchive();
        $zipName = "NusaShare_" . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $work['title']) . ".zip";
        $zipDir = WRITEPATH . 'uploads/';
        if (!is_dir($zipDir)) {
            mkdir($zipDir, 0777, true);
        }
        $zipPath = $zipDir . $zipName;

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            foreach ($images as $index => $img) {
                $path = $img['file_path'];
                if (filter_var($path, FILTER_VALIDATE_URL)) {
                    try {
                        $client = \Config\Services::curlrequest();
                        $response = $client->get($path, ['timeout' => 10, 'http_errors' => false, 'verify' => false]);
                        if ($response->getStatusCode() === 200) {
                            $content = $response->getBody();
                            $zip->addFromString("image_" . ($index + 1) . ".jpg", $content);
                        }
                    } catch (\Exception $e) {
                        // skip
                    }
                } else {
                    $decodedPath = rawurldecode($path);
                    $fullPath = ROOTPATH . 'public/' . $decodedPath;
                    if (file_exists($fullPath)) {
                        $ext = pathinfo($fullPath, PATHINFO_EXTENSION) ?: 'jpg';
                        $zip->addFile($fullPath, "image_" . ($index + 1) . "." . $ext);
                    }
                }
            }
            $zip->close();

            if (file_exists($zipPath)) {
                return $this->response->download($zipPath, null)->setFileName($zipName);
            }
        }
        
        return redirect()->back()->with('error', 'Gagal membuat file unduhan.');
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
}
