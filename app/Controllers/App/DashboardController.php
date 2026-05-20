<?php

namespace App\Controllers\App;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\BookmarkModel;
use App\Models\FollowModel;
use App\Models\CreditModel;
use App\Models\ExploreContentModel;
use App\Models\CreatorProfileModel;

class DashboardController extends BaseController
{
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $userId = session()->get('userId');
        $username = session()->get('username');

        $userModel = new UserModel();
        $bookmarkModel = new BookmarkModel();
        $followModel = new FollowModel();
        $creditModel = new CreditModel();
        $workModel = new ExploreContentModel();
        $creatorProfileModel = new CreatorProfileModel();

        // Get user data for starsoul
        $user = $userModel->find($userId);

        // Get creator profile
        $creatorProfile = $creatorProfileModel->find($userId);

        // Get profiles
        $userProfileModel = new \App\Models\UserProfileModel();
        $userProfile = $userProfileModel->find($userId);
        
        // Merge profile data
        $profile = [
            'display_name'  => $userProfile['display_name'] ?? ($creatorProfile['display_name'] ?? $user['username']),
            'bio'           => $userProfile['bio'] ?? ($creatorProfile['bio'] ?? ''),
            'profile_image' => $userProfile['profile_image'] ?? ($creatorProfile['profile_image'] ?? null),
        ];

        // Get stats
        $savedCount = $bookmarkModel->where('user_id', $userId)->countAllResults();
        $followingCount = $followModel->where('follower_id', $userId)->countAllResults();
        
        $credit = $creditModel->find($userId);
        $creditBalance = $credit ? $credit['balance'] : 0;

        $yourWorksCount = $workModel->where('creator_id', $userId)->countAllResults();

        // Get latest followed update
        $followedUpdates = $followModel->getFollowedUpdates($userId);
        $latestFollowedUpdate = !empty($followedUpdates) ? $followedUpdates[0] : null;

        // Get recommendations (latest works, exclude own)
        $recommendations = $workModel->where('creator_id !=', $userId)
                                    ->orderBy('created_at', 'DESC')
                                    ->limit(4)
                                    ->find();

        $data = [
            'title'           => 'Dashboard - NusaShare',
            'username'        => $username,
            'user'            => $user,
            'savedCount'      => $savedCount,
            'followingCount'  => $followingCount,
            'creditBalance'   => $creditBalance,
            'yourWorksCount'  => $yourWorksCount,
            'recommendations' => $recommendations,
            'profile'         => $profile,
            'creatorProfile'  => $creatorProfile,
        ];

        return view('dashboard/index', $data);
    }
}
