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
    public function index()
    {
        $sort   = $this->request->getGet('sort') ?? 'latest';
        $type   = $this->request->getGet('type') ?? '';
        $userId = session()->get('userId');
        $userIdInt = $userId ? (int)$userId : null;

        // Validasi sort
        $allowedSorts = ['latest', 'popular', 'trending'];
        if (!in_array($sort, $allowedSorts)) $sort = 'latest';

        $data = $this->exploreService->getExploreList($sort, $userIdInt, $type);

        // Fetch user context if logged in
        if ($userId) {
            $userModel = new \App\Models\UserModel();
            $userProfileModel = new \App\Models\UserProfileModel();
            $creatorProfileModel = new \App\Models\CreatorProfileModel();
            
            $user = $userModel->find($userId);
            $userProfile = $userProfileModel->find($userId);
            $creatorProfile = $creatorProfileModel->find($userId);

            $profile = [
                'display_name'  => $userProfile['display_name'] ?? ($creatorProfile['display_name'] ?? $user['username']),
                'profile_image' => $userProfile['profile_image'] ?? ($creatorProfile['profile_image'] ?? null),
            ];

            $data['user'] = $user;
            $data['username'] = session()->get('username');
            $data['profile'] = $profile;
            $data['creatorProfile'] = $creatorProfile;
            $data['isLoggedIn'] = true;
        } else {
            $data['isLoggedIn'] = false;
        }

        return view('explore/index', $data);
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
        $works = $this->exploreService->searchWorks($query);
        $users = $this->exploreService->searchUsers($query);

        $data = [
            'title'      => 'Hasil Pencarian: ' . esc($query),
            'query'      => $query,
            'works'      => $works,
            'users'      => $users,
            'isLoggedIn' => session()->get('isLoggedIn') ?? false
        ];

        // Fetch user context if logged in
        if ($userId) {
            $userModel = new \App\Models\UserModel();
            $userProfileModel = new \App\Models\UserProfileModel();
            $creatorProfileModel = new \App\Models\CreatorProfileModel();
            
            $user = $userModel->find($userId);
            $userProfile = $userProfileModel->find($userId);
            $creatorProfile = $creatorProfileModel->find($userId);

            // Merge profile data
            $profile = [
                'display_name'  => $userProfile['display_name'] ?? ($creatorProfile['display_name'] ?? $user['username']),
                'profile_image' => $userProfile['profile_image'] ?? ($creatorProfile['profile_image'] ?? null),
            ];

            $data['user'] = $user;
            $data['username'] = session()->get('username');
            $data['profile'] = $profile;
            $data['creatorProfile'] = $creatorProfile;
        }

        return view('explore/index', $data);
    }
}
