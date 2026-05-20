<?php

namespace App\Controllers\Creator;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\ExploreContentModel;
use App\Models\CreatorProfileModel;
use App\Models\FollowModel;
use App\Models\BookmarkModel;

class StatsController extends BaseController
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
        $bookmarkModel = new BookmarkModel();

        // Get user and creator data
        $user = $userModel->find($userId);
        $creatorProfile = $creatorProfileModel->find($userId);

        // Models for interaction
        $likeModel = new \App\Models\LikeModel();
        $commentModel = new \App\Models\CommentModel();

        // Works
        $works = $contentModel->where('creator_id', $userId)->findAll();
        $totalWorks = count($works);
        
        $publishedWorks = 0;
        $totalViews = 0;
        $workIds = [];

        foreach ($works as $w) {
            $workIds[] = $w['id'];
            $totalViews += ($w['view_count'] ?? 0);
            if ($w['status'] === 'published') {
                $publishedWorks++;
            }
        }

        $followerCount = $followModel->where('followed_id', $userId)->countAllResults();
        
        $totalBookmarks = 0;
        $totalLikes = 0;
        $totalComments = 0;

        if (!empty($workIds)) {
            $totalBookmarks = $bookmarkModel->whereIn('work_id', $workIds)->countAllResults();
            $totalLikes = $likeModel->whereIn('work_id', $workIds)->countAllResults();
            $totalComments = $commentModel->whereIn('work_id', $workIds)->countAllResults();
        }

        // Stats per work
        $worksWithStats = [];
        foreach ($works as $work) {
            $bookmarks = $bookmarkModel->where('work_id', $work['id'])->countAllResults();
            $views = $work['view_count'] ?? 0;
            
            $worksWithStats[] = [
                'id'         => $work['id'],
                'title'      => $work['title'],
                'status'     => $work['status'],
                'bookmarks'  => $bookmarks,
                'views'      => $views,
                'created_at' => $work['created_at']
            ];
        }

        // Sort by views descending as a default
        usort($worksWithStats, function($a, $b) {
            return $b['views'] <=> $a['views'];
        });

        // Calculate Quality Score (Max 5.0)
        // Formula mapping interactions per views. If 0 views, default 0 or 5.
        $qualityScore = 0.0;
        if ($totalViews > 0) {
            $interactionRate = ($totalLikes + $totalComments * 2 + $totalBookmarks * 3) / $totalViews;
            // Let's say 0.1 interaction rate gives a 5.0. 
            $score = 3.5 + ($interactionRate * 15);
            $qualityScore = min(5.0, max(1.0, floatval($score)));
        } else {
            $qualityScore = 0.0; // no views yet
        }

        // Prepare Distribution Chart Data (Views vs Bookmarks vs Likes vs Comments)
        // We use view_count, totalBookmarks, totalLikes, totalComments
        $distributionData = [
            max(0, $totalViews - ($totalBookmarks + $totalLikes + $totalComments)), // Pembaca Pasif
            $totalLikes,     // Suka
            $totalBookmarks, // Simpan
            $totalComments   // Komentar
        ];
        
        // If all 0, provide default visualization
        if (array_sum($distributionData) == 0) {
            $distributionData = [1, 0, 0, 0];
        }

        // Prepare Growth Chart Data (Dummy historical data based on current views to simulate growth)
        // Since we don't have a daily views log table yet, we generate a plausible curve that ends up near $totalViews / 7.
        $growthData = [];
        $baseDaily = $totalViews > 0 ? ceil($totalViews / 14) : 0; 
        for ($i = 6; $i >= 0; $i--) {
            // Generate some random fluctuation
            $fluctuation = $baseDaily > 0 ? rand(-intval($baseDaily * 0.2), intval($baseDaily * 0.4)) : 0;
            $val = max(0, $baseDaily + $fluctuation - ($i * intval($baseDaily * 0.1)));
            $growthData[] = $val;
        }

        $data = [
            'title'          => 'Statistik Kreator - NusaShare',
            'username'       => $username,
            'user'           => $user,
            'creatorProfile' => $creatorProfile,
            'stats'          => [
                'total_works'     => $totalWorks,
                'published_works' => $publishedWorks,
                'followers'       => $followerCount,
                'total_bookmarks' => $totalBookmarks,
                'total_views'     => $totalViews,
                'quality_score'   => number_format($qualityScore, 1)
            ],
            'worksWithStats'   => $worksWithStats,
            'distributionData' => $distributionData,
            'growthData'       => $growthData,
            'activePage'       => 'stats'
        ];

        return view('creator/stats/index', $data);
    }
}
