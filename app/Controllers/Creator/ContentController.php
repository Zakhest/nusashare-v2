<?php

namespace App\Controllers\Creator;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\ExploreContentModel;
use App\Models\CreatorProfileModel;

class ContentController extends BaseController
{
    public function index()
    {
        // Check if logged in as creator
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'creator') {
            return redirect()->to(base_url('creator/login'));
        }

        $userId = session()->get('userId');
        $username = session()->get('username');

        $userModel = new UserModel();
        $contentModel = new ExploreContentModel();
        $creatorProfileModel = new CreatorProfileModel();

        // Get user and creator data
        $user = $userModel->find($userId);
        $creatorProfile = $creatorProfileModel->find($userId);

        // Get all works by this creator
        $works = $contentModel->where('creator_id', $userId)
                              ->orderBy('created_at', 'DESC')
                              ->findAll();

        $data = [
            'title'          => 'Kelola Karya - NusaShare',
            'username'       => $username,
            'user'           => $user,
            'creatorProfile' => $creatorProfile,
            'works'          => $works,
            'activePage'     => 'content'
        ];

        return view('creator/content/index', $data);
    }

    public function create()
    {
        // Check if logged in as creator
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'creator') {
            return redirect()->to(base_url('creator/login'));
        }

        $userId = session()->get('userId');
        $username = session()->get('username');

        $userModel = new UserModel();
        $creatorProfileModel = new CreatorProfileModel();

        $user = $userModel->find($userId);
        $creatorProfile = $creatorProfileModel->find($userId);

        $data = [
            'title'          => 'Tambah Karya Baru - NusaShare',
            'username'       => $username,
            'user'           => $user,
            'creatorProfile' => $creatorProfile,
            'activePage'     => 'content'
        ];

        return view('creator/content/create', $data);
    }

    public function store()
    {
        // Check if logged in as creator
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'creator') {
            return redirect()->to(base_url('creator/login'));
        }

        $userId = session()->get('userId');

        // Validation rules
        $rules = [
            'title'        => 'required|min_length[3]|max_length[255]',
            'description'  => 'required|min_length[10]',
            'content_type' => 'required|in_list[text,image,pdf,novel,light_novel,comic]',
            'status'       => 'required|in_list[draft,published]',
            'access_type'  => 'required|in_list[full,chapter]',
            'work_status'  => 'required|in_list[ongoing,ended]',
            'cover'        => 'is_image[cover]|max_size[cover,2048]|ext_in[cover,jpg,jpeg,png,webp]'
        ];

        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $contentModel = new ExploreContentModel();
        
        $data = [
            'creator_id'   => $userId,
            'title'        => $this->request->getPost('title'),
            'description'  => $this->request->getPost('description'),
            'content_type' => $this->request->getPost('content_type'),
            'status'       => $this->request->getPost('status'),
            'access_type'  => $this->request->getPost('access_type'),
            'work_status'  => $this->request->getPost('work_status'),
            'is_paid'      => $this->request->getPost('is_paid') ? 1 : 0,
            'price'        => (int) $this->request->getPost('price'),
            'watermark_text' => $this->request->getPost('watermark_text'),
            'timer_duration' => (int) $this->request->getPost('timer_duration'),
            'is_locked'    => 0, // Default for new works
            'view_count'   => 0
        ];

        // Handle remote cover upload
        $cover = $this->request->getFile('cover');
        if ($cover && $cover->isValid() && !$cover->hasMoved()) {
            $remoteService = new \App\Services\File\RemoteUploadService();
            $remoteUrl = $remoteService->upload($cover, 'cover');
            if ($remoteUrl) {
                $data['cover_url'] = str_replace(' ', '%20', $remoteUrl);
            } else {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'errors' => ['cover' => 'Gagal mengupload cover ke server remote.']]);
                }
                return redirect()->back()->withInput()->with('errors', ['cover' => 'Gagal mengupload cover ke server remote.']);
            }
        }

        if ($contentModel->insert((object) $data)) {
            $workId = $contentModel->insertID();

            // Handle multiple gallery images
            $galleryImages = $this->request->getFiles();
            if (isset($galleryImages['gallery_images'])) {
                $imageModel = new \App\Models\WorkImageModel();
                $remoteService = new \App\Services\File\RemoteUploadService();
                $order = 1;

                foreach ($galleryImages['gallery_images'] as $img) {
                    if ($img->isValid() && !$img->hasMoved()) {
                        $remoteUrl = $remoteService->upload($img, 'arts');
                        if ($remoteUrl) {
                            $imageModel->insert((object) [
                                'work_id'   => $workId,
                                'file_path' => str_replace(' ', '%20', $remoteUrl),
                                'order_num' => $order++
                            ]);
                        }
                    }
                }
            }

            if ($this->request->isAJAX()) {
                // Return success JSON so AJAX handler can redirect
                return $this->response->setJSON(['success' => true, 'redirect' => base_url('creator/content')]);
            }
            return redirect()->to(base_url('creator/content'))->with('message', 'Karya berhasil dibuat!');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'errors' => ['db' => 'Gagal menyimpan karya ke database.']]);
        }
        return redirect()->back()->withInput()->with('errors', ['db' => 'Gagal menyimpan karya ke database.']);
    }

    public function edit($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'creator') {
            return redirect()->to(base_url('creator/login'));
        }

        $userId   = session()->get('userId');
        $username = session()->get('username');

        $contentModel       = new ExploreContentModel();
        $userModel          = new UserModel();
        $creatorProfileModel = new CreatorProfileModel();

        // Fetch work and verify ownership
        $work = $contentModel->find($id);

        if (!$work || $work['creator_id'] !== $userId) {
            return redirect()->to(base_url('creator/content'))
                             ->with('errors', ['auth' => 'Karya tidak ditemukan atau kamu bukan pemiliknya.']);
        }

        $user          = $userModel->find($userId);
        $creatorProfile = $creatorProfileModel->find($userId);

        $data = [
            'title'          => 'Edit Karya - NusaShare',
            'username'       => $username,
            'user'           => $user,
            'creatorProfile' => $creatorProfile,
            'work'           => $work,
            'activePage'     => 'content',
        ];

        return view('creator/content/edit', $data);
    }

    public function update($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'creator') {
            return redirect()->to(base_url('creator/login'));
        }

        $userId       = session()->get('userId');
        $contentModel = new ExploreContentModel();

        // Ownership check
        $work = $contentModel->find($id);
        if (!$work || $work['creator_id'] !== $userId) {
            return redirect()->to(base_url('creator/content'))
                             ->with('errors', ['auth' => 'Akses ditolak.']);
        }

        // Validation
        $rules = [
            'title'        => 'required|min_length[3]|max_length[255]',
            'description'  => 'required|min_length[10]',
            'content_type' => 'required|in_list[text,image,pdf,novel,light_novel,comic]',
            'status'       => 'required|in_list[draft,published]',
            'access_type'  => 'required|in_list[full,chapter]',
            'work_status'  => 'required|in_list[ongoing,ended]',
            'cover'        => 'is_image[cover]|max_size[cover,3048]|ext_in[cover,jpg,jpeg,png,webp]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'title'        => $this->request->getPost('title'),
            'description'  => $this->request->getPost('description'),
            'content_type' => $this->request->getPost('content_type'),
            'status'       => $this->request->getPost('status'),
            'access_type'  => $this->request->getPost('access_type'),
            'work_status'  => $this->request->getPost('work_status'),
            'is_paid'      => $this->request->getPost('is_paid') ? 1 : 0,
            'price'        => (int) $this->request->getPost('price'),
            'watermark_text' => $this->request->getPost('watermark_text'),
            'timer_duration' => (int) $this->request->getPost('timer_duration'),
        ];

        // Handle new remote cover upload
        $cover = $this->request->getFile('cover');
        if ($cover && $cover->isValid() && !$cover->hasMoved()) {
            $remoteService = new \App\Services\File\RemoteUploadService();
            $remoteUrl = $remoteService->upload($cover, 'cover');

            if ($remoteUrl) {
                $updateData['cover_url'] = str_replace(' ', '%20', $remoteUrl);
                // Note: We don't delete old remote covers here as we don't have a remote delete API yet.
            } else {
                return redirect()->back()->withInput()->with('errors', ['cover' => 'Gagal mengupload cover baru ke server remote.']);
            }
        }

        $contentModel->update($id, $updateData);

        // Handle additional gallery images
        $galleryImages = $this->request->getFiles();
        if (isset($galleryImages['gallery_images'])) {
            $imageModel = new \App\Models\WorkImageModel();
            $remoteService = new \App\Services\File\RemoteUploadService();
            $order = $imageModel->getNextOrder((int)$id);

            foreach ($galleryImages['gallery_images'] as $img) {
                if ($img->isValid() && !$img->hasMoved()) {
                    $remoteUrl = $remoteService->upload($img, 'arts');
                    if ($remoteUrl) {
                        $imageModel->insert((object) [
                            'work_id'   => $id,
                            'file_path' => str_replace(' ', '%20', $remoteUrl),
                            'order_num' => $order++
                        ]);
                    }
                }
            }
        }

        return redirect()->to(base_url('creator/content'))
                         ->with('message', 'Karya berhasil diperbarui!');
    }

    public function stats($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'creator') {
            return redirect()->to(base_url('creator/login'));
        }

        $userId   = session()->get('userId');
        $username = session()->get('username');

        $contentModel       = new ExploreContentModel();
        $userModel          = new UserModel();
        $creatorProfileModel = new CreatorProfileModel();

        // Fetch work and verify ownership
        $work = $contentModel->find($id);

        if (!$work || $work['creator_id'] !== $userId) {
            return redirect()->to(base_url('creator/content'))
                             ->with('errors', ['auth' => 'Karya tidak ditemukan atau kamu bukan pemiliknya.']);
        }

        $user          = $userModel->find($userId);
        $creatorProfile = $creatorProfileModel->find($userId);

        // Models for interaction
        $likeModel = new \App\Models\LikeModel();
        $commentModel = new \App\Models\CommentModel();
        $bookmarkModel = new \App\Models\BookmarkModel();

        $views = $work['view_count'] ?? 0;
        $likes = $likeModel->where('work_id', $id)->countAllResults();
        $comments = $commentModel->where('work_id', $id)->countAllResults();
        $bookmarks = $bookmarkModel->where('work_id', $id)->countAllResults();

        // Calculate Quality Score for this work (Max 5.0)
        $qualityScore = 0.0;
        if ($views > 0) {
            $interactionRate = ($likes + $comments * 2 + $bookmarks * 3) / $views;
            $score = 3.5 + ($interactionRate * 15);
            $qualityScore = min(5.0, max(1.0, floatval($score)));
        }

        // Distribution Data for chart
        $distributionData = [
            max(0, $views - ($bookmarks + $likes + $comments)), // Pembaca Biasa
            $likes,
            $bookmarks,
            $comments
        ];
        
        if (array_sum($distributionData) == 0) {
            $distributionData = [1, 0, 0, 0];
        }

        // Growth Data (Simulated for this work)
        $growthData = [];
        $baseDaily = $views > 0 ? ceil($views / 14) : 0; 
        for ($i = 6; $i >= 0; $i--) {
            // Generate some random fluctuation
            $fluctuation = $baseDaily > 0 ? rand(-intval($baseDaily * 0.2), intval($baseDaily * 0.4)) : 0;
            $val = max(0, $baseDaily + $fluctuation - ($i * intval($baseDaily * 0.1)));
            $growthData[] = $val;
        }

        // Calculate Income
        $totalIncome = 0;
        if ($work['content_type'] === 'text') {
            // For text works, sum up (unlocked_chapters * chapter price)
            $chapterModel = new \App\Models\ChapterModel();
            $unlockedChapterModel = new \App\Models\UnlockedChapterModel();
            
            $chapters = $chapterModel->where('work_id', $id)->where('is_locked', 1)->findAll();
            foreach ($chapters as $ch) {
                if ($ch['price'] > 0) {
                    $unlockCount = $unlockedChapterModel->where('chapter_id', $ch['id'])->countAllResults();
                    $totalIncome += ($unlockCount * $ch['price']);
                }
            }
        } elseif ($work['content_type'] === 'image' && $work['is_paid']) {
            // For image works, since we don't have an explicit unlock log table yet (assuming sessions or similar unlock logic),
            // We can estimate based on a percentage of views or likes, or if we had a transactions table we'd use that.
            // Let's assume 10% of total views decided to unlock the image gallery as a rough estimation for the demo
            $estimatedUnlocks = floor($views * 0.1);
            $totalIncome = $estimatedUnlocks * $work['price'];
        }

        // Fetch Recent Comments
        $recentComments = $commentModel->getByWork($id);
        // Only limit to top 10 for the stats view
        $recentComments = array_slice($recentComments, 0, 10);

        $data = [
            'title'          => 'Statistik Karya - NusaShare',
            'username'       => $username,
            'user'           => $user,
            'creatorProfile' => $creatorProfile,
            'work'           => $work,
            'stats'          => [
                'views'         => $views,
                'likes'         => $likes,
                'comments'      => $comments,
                'bookmarks'     => $bookmarks,
                'quality_score' => number_format($qualityScore, 1),
                'total_income'  => $totalIncome
            ],
            'distributionData' => $distributionData,
            'growthData'       => $growthData,
            'recentComments'   => $recentComments,
            'activePage'     => 'content',
        ];

        return view('creator/content/stats', $data);
    }
}
