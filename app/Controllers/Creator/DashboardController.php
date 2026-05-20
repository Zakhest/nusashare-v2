<?php

namespace App\Controllers\Creator;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\ExploreContentModel;
use App\Models\CreatorProfileModel;
use App\Models\FollowModel;

class DashboardController extends BaseController
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
        $followModel = new FollowModel();

        // Get user and creator data
        $user = $userModel->find($userId);
        $creatorProfile = $creatorProfileModel->find($userId);

        // Stats for creator
        $totalWorks = $contentModel->where('creator_id', $userId)->countAllResults();
        $publishedWorks = $contentModel->where('creator_id', $userId)->where('status', 'published')->countAllResults();
        $draftWorks = $contentModel->where('creator_id', $userId)->where('status', 'draft')->countAllResults();
        
        $followerCount = $followModel->where('followed_id', $userId)->countAllResults();

        // Get latest works by this creator
        $latestWorks = $contentModel->where('creator_id', $userId)
                                    ->orderBy('created_at', 'DESC')
                                    ->limit(5)
                                    ->find();

        $data = [
            'title'          => 'Dashboard Kreator - NusaShare',
            'username'       => $username,
            'user'           => $user,
            'creatorProfile' => $creatorProfile,
            'stats'          => [
                'total_works'     => $totalWorks,
                'published_works' => $publishedWorks,
                'draft_works'     => $draftWorks,
                'followers'       => $followerCount,
            ],
            'latestWorks'    => $latestWorks,
            'activePage'     => 'dashboard'
        ];

        return view('creator/dashboard/index', $data);
    }
}
