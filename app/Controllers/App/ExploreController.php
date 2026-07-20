<?php

namespace App\Controllers\App;

use App\Controllers\BaseController;
use App\Services\Content\ExploreContentService;

class ExploreController extends BaseController
{
    protected ExploreContentService $exploreService;

    public function __construct()
    {
        $this->exploreService = service('exploreContent');
    }

    /**
     * GET /explore
     */
    public function index(string $category = '')
    {
        $sort      = $this->request->getGet('sort') ?? 'latest';
        $type      = $this->request->getGet('type') ?? '';
        $genre     = $this->request->getGet('genre') ?? '';
        $userId    = session()->get('userId');
        $userIdInt = $userId ? (int)$userId : null;

        if ($category === 'gallery') {
            $type = 'image';
        } elseif ($category === 'story') {
            $type = 'story';
        }

        // Validasi sort
        $allowedSorts = ['latest', 'popular', 'recommended'];
        if (!in_array($sort, $allowedSorts)) $sort = 'latest';

        $data = $this->exploreService->getExploreList($sort, $userIdInt, $type, $genre);
        $data['genre'] = $genre;

        // Fetch user context if logged in
        if ($userId) {
            $userModel           = new \App\Models\UserModel();
            $userProfileModel    = new \App\Models\UserProfileModel();
            $creatorProfileModel = new \App\Models\CreatorProfileModel();

            $user           = $userModel->find($userId);
            $userProfile    = $userProfileModel->find($userId);
            $creatorProfile = $creatorProfileModel->find($userId);

            $profile = [
                'display_name'  => $userProfile['display_name']  ?? ($creatorProfile['display_name']  ?? $user['username']),
                'profile_image' => $userProfile['profile_image'] ?? ($creatorProfile['profile_image'] ?? null),
            ];

            $data['user']           = $user;
            $data['username']       = session()->get('username');
            $data['profile']        = $profile;
            $data['creatorProfile'] = $creatorProfile;
            $data['isLoggedIn']     = true;
        } else {
            $data['isLoggedIn'] = false;
        }

        // ── Hero Spotlight: rotasi per sesi ──────────────────────────────────
        // Ambil pool top-10 karya terbaik (curated/museum → published)
        $pool          = $this->exploreService->getSpotlightPool(10);
        $spotlightWork = null;

        if (!empty($pool)) {
            $poolIds = array_column($pool, 'id');
            $savedId = session()->get('explore_spotlight_id');

            if ($savedId && in_array($savedId, $poolIds)) {
                // Pakai spotlight yang sudah tersimpan di sesi ini
                foreach ($pool as $candidate) {
                    if ($candidate['id'] == $savedId) {
                        $spotlightWork = $candidate;
                        break;
                    }
                }
            } else {
                // Sesi baru → pilih acak dari pool → simpan ke sesi
                $spotlightWork = $pool[array_rand($pool)];
                session()->set('explore_spotlight_id', $spotlightWork['id']);
            }
        }

        $data['spotlightWork'] = $spotlightWork;

        return view('explore/index', $data);
    }

    public function gallery()
    {
        return $this->index('gallery');
    }

    public function story()
    {
        return $this->index('story');
    }

    /**
     * GET /search?q={query}
     */
    public function search()
    {
        $query = $this->request->getGet('q');

        if (empty($query)) {
            return redirect()->to(base_url('explore'));
        }

        $userId = session()->get('userId');
        $works  = $this->exploreService->searchWorks($query);
        $users  = $this->exploreService->searchUsers($query);

        $data = [
            'title'      => 'Hasil Pencarian: ' . esc($query),
            'query'      => $query,
            'works'      => $works,
            'users'      => $users,
            'isLoggedIn' => session()->get('isLoggedIn') ?? false,
        ];

        // Fetch user context if logged in
        if ($userId) {
            $userModel           = new \App\Models\UserModel();
            $userProfileModel    = new \App\Models\UserProfileModel();
            $creatorProfileModel = new \App\Models\CreatorProfileModel();

            $user           = $userModel->find($userId);
            $userProfile    = $userProfileModel->find($userId);
            $creatorProfile = $creatorProfileModel->find($userId);

            $profile = [
                'display_name'  => $userProfile['display_name']  ?? ($creatorProfile['display_name']  ?? $user['username']),
                'profile_image' => $userProfile['profile_image'] ?? ($creatorProfile['profile_image'] ?? null),
            ];

            $data['user']           = $user;
            $data['username']       = session()->get('username');
            $data['profile']        = $profile;
            $data['creatorProfile'] = $creatorProfile;
        }

        // Spotlight tidak berubah saat search — gunakan yang tersimpan di sesi
        $pool          = $this->exploreService->getSpotlightPool(10);
        $spotlightWork = null;

        if (!empty($pool)) {
            $poolIds = array_column($pool, 'id');
            $savedId = session()->get('explore_spotlight_id');

            if ($savedId && in_array($savedId, $poolIds)) {
                foreach ($pool as $candidate) {
                    if ($candidate['id'] == $savedId) {
                        $spotlightWork = $candidate;
                        break;
                    }
                }
            } else {
                $spotlightWork = $pool[array_rand($pool)];
                session()->set('explore_spotlight_id', $spotlightWork['id']);
            }
        }

        $data['spotlightWork'] = $spotlightWork;

        return view('explore/index', $data);
    }
}
