<?php

namespace App\Controllers\Creator;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\ExploreContentModel;
use App\Models\CreatorProfileModel;
use App\Models\FollowModel;
use App\Models\BookmarkModel;
use App\Models\LikeModel;
use App\Models\CommentModel;
use App\Models\TransactionModel;

class StatsController extends BaseController
{
    public function index()
    {
        // Check if logged in as creator
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'creator') {
            return redirect()->to(base_url('creator/login'));
        }

        $userId   = session()->get('userId');
        $username = session()->get('username');

        $userModel           = new UserModel();
        $contentModel        = new ExploreContentModel();
        $creatorProfileModel = new CreatorProfileModel();
        $followModel         = new FollowModel();
        $bookmarkModel       = new BookmarkModel();
        $likeModel           = new LikeModel();
        $commentModel        = new CommentModel();
        $transactionModel    = new TransactionModel();
        $db                  = \Config\Database::connect();

        // Get user and creator profile
        $user           = $userModel->find($userId);
        $creatorProfile = $creatorProfileModel->find($userId);

        // ── Works ────────────────────────────────────────────────────────────
        $works          = $contentModel->where('creator_id', $userId)->findAll();
        $totalWorks     = count($works);
        $publishedWorks = 0;
        $totalViews     = 0;
        $workIds        = [];
        $workMap        = []; // id => work row for quick lookup

        foreach ($works as $w) {
            $workIds[]          = $w['id'];
            $totalViews        += (int)($w['view_count'] ?? 0);
            $workMap[$w['id']]  = $w;
            if ($w['status'] === 'published') {
                $publishedWorks++;
            }
        }

        // ── Followers ────────────────────────────────────────────────────────
        $followerCount  = $followModel->where('followed_id', $userId)->countAllResults();
        $thisMonthStart = date('Y-m-01 00:00:00');
        $lastMonthStart = date('Y-m-01 00:00:00', strtotime('-1 month'));
        $lastMonthEnd   = date('Y-m-t 23:59:59', strtotime('-1 month'));

        $newFollowersThisMonth = $followModel
            ->where('followed_id', $userId)
            ->where('created_at >=', $thisMonthStart)
            ->countAllResults();

        $newFollowersLastMonth = $followModel
            ->where('followed_id', $userId)
            ->where('created_at >=', $lastMonthStart)
            ->where('created_at <=', $lastMonthEnd)
            ->countAllResults();

        // ── Aggregate interaction stats ───────────────────────────────────────
        $totalBookmarks     = 0;
        $totalLikes         = 0;
        $totalComments      = 0;
        $bookmarksThisMonth = 0;
        $bookmarksLastMonth = 0;
        $likesThisMonth     = 0;
        $likesLastMonth     = 0;

        if (!empty($workIds)) {
            $totalBookmarks = $bookmarkModel->whereIn('work_id', $workIds)->countAllResults();
            $totalLikes     = $likeModel->whereIn('work_id', $workIds)->countAllResults();
            $totalComments  = $commentModel->whereIn('work_id', $workIds)->countAllResults();

            $bookmarksThisMonth = $bookmarkModel->whereIn('work_id', $workIds)->where('created_at >=', $thisMonthStart)->countAllResults();
            $bookmarksLastMonth = $bookmarkModel->whereIn('work_id', $workIds)->where('created_at >=', $lastMonthStart)->where('created_at <=', $lastMonthEnd)->countAllResults();
            $likesThisMonth     = $likeModel->whereIn('work_id', $workIds)->where('created_at >=', $thisMonthStart)->countAllResults();
            $likesLastMonth     = $likeModel->whereIn('work_id', $workIds)->where('created_at >=', $lastMonthStart)->where('created_at <=', $lastMonthEnd)->countAllResults();
        }

        // ── Engagement Rate ───────────────────────────────────────────────────
        $engagementRate = 0.0;
        if ($totalViews > 0) {
            $engagementRate = min(100, round(($totalLikes + $totalComments + $totalBookmarks) / $totalViews * 100, 1));
        }

        // ── Quality Score (1–5) ───────────────────────────────────────────────
        $qualityScore = 0.0;
        if ($totalViews > 0) {
            $interactionRate = ($totalLikes + $totalComments * 2 + $totalBookmarks * 3) / $totalViews;
            $score           = 1.5 + ($interactionRate * 20);
            $qualityScore    = min(5.0, max(1.0, round($score, 1)));
        }
        if ($qualityScore >= 4.5)      { $qualityLabel = 'Luar Biasa'; }
        elseif ($qualityScore >= 3.5)  { $qualityLabel = 'Sangat Baik'; }
        elseif ($qualityScore >= 2.5)  { $qualityLabel = 'Baik'; }
        elseif ($qualityScore >= 1.5)  { $qualityLabel = 'Cukup'; }
        else                           { $qualityLabel = 'Perlu Ditingkatkan'; }

        // ── Per-Work Stats ────────────────────────────────────────────────────
        $worksWithStats = [];
        foreach ($works as $work) {
            $wid       = $work['id'];
            $wViews    = (int)($work['view_count'] ?? 0);
            $wLikes    = $likeModel->where('work_id', $wid)->countAllResults();
            $wBmarks   = $bookmarkModel->where('work_id', $wid)->countAllResults();
            $wComments = $commentModel->where('work_id', $wid)->countAllResults();

            $wEngagement = 0.0;
            if ($wViews > 0) {
                $wEngagement = min(100, round(($wLikes + $wComments + $wBmarks) / $wViews * 100, 1));
            }

            $worksWithStats[] = [
                'id'           => $wid,
                'title'        => $work['title'],
                'status'       => $work['status'],
                'content_type' => $work['content_type'] ?? 'text',
                'cover_url'    => $work['cover_url'] ?? null,
                'views'        => $wViews,
                'likes'        => $wLikes,
                'bookmarks'    => $wBmarks,
                'comments'     => $wComments,
                'engagement'   => $wEngagement,
                'created_at'   => $work['created_at'],
            ];
        }
        usort($worksWithStats, fn($a, $b) => $b['views'] <=> $a['views']);

        // ── Content Type Breakdown ─────────────────────────────────────────────
        $typeBreakdown = [];
        foreach ($works as $w) {
            $ct = $w['content_type'] ?? 'text';
            $typeBreakdown[$ct] = ($typeBreakdown[$ct] ?? 0) + 1;
        }

        // ── Distribution Chart Data ────────────────────────────────────────────
        $passiveReaders   = max(0, $totalViews - ($totalLikes + $totalComments + $totalBookmarks));
        $distributionData = [$passiveReaders, $totalLikes, $totalBookmarks, $totalComments];
        if (array_sum($distributionData) == 0) { $distributionData = [1, 0, 0, 0]; }

        // ── Growth Chart: Last 30 days ─────────────────────────────────────────
        $growthLabels   = [];
        $growthLikes    = [];
        $growthBmarks   = [];
        $growthComments = [];

        for ($i = 29; $i >= 0; $i--) {
            $dayLabel = date('d/m', strtotime("-{$i} days"));
            $dayStart = date('Y-m-d 00:00:00', strtotime("-{$i} days"));
            $dayEnd   = date('Y-m-d 23:59:59', strtotime("-{$i} days"));
            $growthLabels[] = $dayLabel;

            if (!empty($workIds)) {
                $growthLikes[]    = $likeModel->whereIn('work_id', $workIds)->where('created_at >=', $dayStart)->where('created_at <=', $dayEnd)->countAllResults();
                $growthBmarks[]   = $bookmarkModel->whereIn('work_id', $workIds)->where('created_at >=', $dayStart)->where('created_at <=', $dayEnd)->countAllResults();
                $growthComments[] = $commentModel->whereIn('work_id', $workIds)->where('created_at >=', $dayStart)->where('created_at <=', $dayEnd)->countAllResults();
            } else {
                $growthLikes[] = $growthBmarks[] = $growthComments[] = 0;
            }
        }

        // ── Helper: percent change ─────────────────────────────────────────────
        $pctChange = function(int $prev, int $curr): array {
            if ($prev === 0) { return ['value' => $curr > 0 ? 100 : 0, 'up' => true]; }
            $delta = round(($curr - $prev) / $prev * 100);
            return ['value' => abs($delta), 'up' => $delta >= 0];
        };

        $followerChange = $pctChange($newFollowersLastMonth, $newFollowersThisMonth);
        $bookmarkChange = $pctChange($bookmarksLastMonth, $bookmarksThisMonth);
        $likesChange    = $pctChange($likesLastMonth, $likesThisMonth);
        $topWork        = !empty($worksWithStats) ? $worksWithStats[0] : null;

        // ════════════════════════════════════════════════════════════════════════
        // ── PURCHASE / UNLOCK ANALYTICS ─────────────────────────────────────────
        // ════════════════════════════════════════════════════════════════════════

        // 1. Creator's income transactions (type='in', from buyers paying for content)
        //    These are recorded TO the creator's user_id when someone buys their work/chapter.
        $allIncomeTransactions = $transactionModel
            ->where('user_id', $userId)
            ->where('type', 'in')
            ->whereIn('category', ['unlock', 'purchase', 'chapter_unlock', 'work_purchase'])
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Total revenue (in CC and IDR equivalent: 1 CC = Rp 10)
        $totalRevenueCC  = 0;
        $revenueThisMonth = 0;
        $revenueLastMonth = 0;
        $totalTransactions = count($allIncomeTransactions);

        foreach ($allIncomeTransactions as $tx) {
            $totalRevenueCC += (int)$tx['amount'];
            if ($tx['created_at'] >= $thisMonthStart) {
                $revenueThisMonth += (int)$tx['amount'];
            }
            if ($tx['created_at'] >= $lastMonthStart && $tx['created_at'] <= $lastMonthEnd) {
                $revenueLastMonth += (int)$tx['amount'];
            }
        }
        $totalRevenueIDR  = $totalRevenueCC * 10;
        $revenueThisMonthIDR = $revenueThisMonth * 10;
        $revenueChange    = $pctChange($revenueLastMonth, $revenueThisMonth);

        // 2. Revenue per work — join transactions.reference_id with chapters.id/works.id
        //    For 'unlock' category: reference_id = chapter_id → we join chapters to get work_id
        //    For 'purchase' category: reference_id = work_id directly
        $revenueByWork = []; // work_id => ['title', 'revenue_cc', 'tx_count', 'cover_url']

        foreach ($allIncomeTransactions as $tx) {
            $refId    = (int)($tx['reference_id'] ?? 0);
            $cat      = $tx['category'];
            $amount   = (int)$tx['amount'];
            $workId   = null;

            if (in_array($cat, ['unlock', 'chapter_unlock']) && $refId > 0) {
                // reference_id is a chapter_id — look up which work it belongs to
                $chapter = $db->table('chapters')->select('work_id')->where('id', $refId)->get()->getRowArray();
                if ($chapter) { $workId = (int)$chapter['work_id']; }
            } elseif (in_array($cat, ['purchase', 'work_purchase']) && $refId > 0) {
                $workId = $refId;
            }

            if ($workId && isset($workMap[$workId])) {
                if (!isset($revenueByWork[$workId])) {
                    $revenueByWork[$workId] = [
                        'work_id'     => $workId,
                        'title'       => $workMap[$workId]['title'],
                        'cover_url'   => $workMap[$workId]['cover_url'] ?? null,
                        'content_type'=> $workMap[$workId]['content_type'] ?? 'text',
                        'revenue_cc'  => 0,
                        'tx_count'    => 0,
                    ];
                }
                $revenueByWork[$workId]['revenue_cc'] += $amount;
                $revenueByWork[$workId]['tx_count']++;
            }
        }
        // Sort by revenue descending
        usort($revenueByWork, fn($a, $b) => $b['revenue_cc'] <=> $a['revenue_cc']);
        $topRevenueWorks = array_slice($revenueByWork, 0, 5);

        // 3. Unlock history from unlocked_chapters — chapters that belong to creator's works
        //    unlocked_chapters → chapters → works (filter by creator_id)
        $unlockedRows = [];
        if (!empty($workIds)) {
            $unlockedRows = $db->table('unlocked_chapters')
                ->select([
                    'unlocked_chapters.id',
                    'unlocked_chapters.user_id as buyer_id',
                    'unlocked_chapters.chapter_id',
                    'unlocked_chapters.created_at',
                    'chapters.title as chapter_title',
                    'chapters.work_id',
                    'chapters.price as chapter_price',
                    'chapters.order_num',
                    'works.title as work_title',
                    'works.cover_url',
                    'users.username as buyer_name',
                ])
                ->join('chapters', 'chapters.id = unlocked_chapters.chapter_id')
                ->join('works', 'works.id = chapters.work_id')
                ->join('users', 'users.id = unlocked_chapters.user_id', 'left')
                ->whereIn('chapters.work_id', $workIds)
                ->orderBy('unlocked_chapters.created_at', 'DESC')
                ->get()
                ->getResultArray();
        }

        $totalUnlocks         = count($unlockedRows);
        $unlocksThisMonth     = 0;
        $unlocksLastMonth     = 0;
        $totalUnlockRevenueCC = 0;

        foreach ($unlockedRows as $ur) {
            $totalUnlockRevenueCC += (int)($ur['chapter_price'] ?? 0);
            if ($ur['created_at'] >= $thisMonthStart) { $unlocksThisMonth++; }
            if ($ur['created_at'] >= $lastMonthStart && $ur['created_at'] <= $lastMonthEnd) { $unlocksLastMonth++; }
        }
        $unlockChange = $pctChange($unlocksLastMonth, $unlocksThisMonth);

        // 4. Daily revenue trend — last 30 days
        $revenueDailyLabels = [];
        $revenueDailyCC     = [];
        $revenueDailyUnlocks = [];

        for ($i = 29; $i >= 0; $i--) {
            $dayLabel = date('d/m', strtotime("-{$i} days"));
            $dayStart = date('Y-m-d 00:00:00', strtotime("-{$i} days"));
            $dayEnd   = date('Y-m-d 23:59:59', strtotime("-{$i} days"));

            $revenueDailyLabels[] = $dayLabel;

            // Sum income CC for that day
            $dayCC = 0;
            foreach ($allIncomeTransactions as $tx) {
                if ($tx['created_at'] >= $dayStart && $tx['created_at'] <= $dayEnd) {
                    $dayCC += (int)$tx['amount'];
                }
            }
            $revenueDailyCC[] = $dayCC;

            // Count unlocks for that day
            $dayUnlocks = 0;
            foreach ($unlockedRows as $ur) {
                if ($ur['created_at'] >= $dayStart && $ur['created_at'] <= $dayEnd) {
                    $dayUnlocks++;
                }
            }
            $revenueDailyUnlocks[] = $dayUnlocks;
        }

        // 5. Purchase hour heatmap — count unlocks by hour of day (0–23)
        $unlocksByHour = array_fill(0, 24, 0);
        foreach ($unlockedRows as $ur) {
            $hour = (int)date('G', strtotime($ur['created_at']));
            $unlocksByHour[$hour]++;
        }
        $peakHour     = array_search(max($unlocksByHour), $unlocksByHour);
        $peakHourFmt  = sprintf('%02d:00–%02d:00', $peakHour, $peakHour + 1);

        // 6. Purchase day-of-week breakdown
        $unlocksByDay = array_fill(0, 7, 0); // 0=Sun ... 6=Sat
        foreach ($unlockedRows as $ur) {
            $dow = (int)date('w', strtotime($ur['created_at']));
            $unlocksByDay[$dow]++;
        }
        $dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

        // 7. Conversion rate: buyers / total unique readers
        //    Unique buyers = count distinct buyer_id in unlockedRows + purchase transactions
        $uniqueBuyers = count(array_unique(array_column($unlockedRows, 'buyer_id')));
        $conversionRate = $totalViews > 0 ? round($uniqueBuyers / $totalViews * 100, 2) : 0.0;

        // 8. Most-unlocked chapters (top 5)
        $chapterUnlockCounts = [];
        foreach ($unlockedRows as $ur) {
            $cid = $ur['chapter_id'];
            if (!isset($chapterUnlockCounts[$cid])) {
                $chapterUnlockCounts[$cid] = [
                    'chapter_id'    => $cid,
                    'chapter_title' => $ur['chapter_title'],
                    'order_num'     => $ur['order_num'],
                    'work_title'    => $ur['work_title'],
                    'unlocks'       => 0,
                    'price'         => (int)($ur['chapter_price'] ?? 0),
                ];
            }
            $chapterUnlockCounts[$cid]['unlocks']++;
        }
        usort($chapterUnlockCounts, fn($a, $b) => $b['unlocks'] <=> $a['unlocks']);
        $topUnlockedChapters = array_slice($chapterUnlockCounts, 0, 5);

        // 9. Recent unlock activity (last 10)
        $recentUnlocks = array_slice($unlockedRows, 0, 10);

        // ── Assemble data ──────────────────────────────────────────────────────
        $data = [
            'title'          => 'Statistik Kreator - NusaShare',
            'username'       => $username,
            'user'           => $user,
            'creatorProfile' => $creatorProfile,
            'activePage'     => 'stats',

            'stats' => [
                'total_works'              => $totalWorks,
                'published_works'          => $publishedWorks,
                'followers'                => $followerCount,
                'total_bookmarks'          => $totalBookmarks,
                'total_views'              => $totalViews,
                'total_likes'              => $totalLikes,
                'total_comments'           => $totalComments,
                'quality_score'            => number_format($qualityScore, 1),
                'quality_label'            => $qualityLabel,
                'engagement_rate'          => $engagementRate,
                'new_followers_this_month' => $newFollowersThisMonth,
                'new_followers_last_month' => $newFollowersLastMonth,
                'follower_change'          => $followerChange,
                'bookmark_change'          => $bookmarkChange,
                'likes_change'             => $likesChange,
                'bookmarks_this_month'     => $bookmarksThisMonth,
                'likes_this_month'         => $likesThisMonth,
            ],

            'typeBreakdown'    => $typeBreakdown,
            'worksWithStats'   => $worksWithStats,
            'topWork'          => $topWork,
            'distributionData' => $distributionData,

            // Growth chart (last 30 days)
            'growthLabels'    => $growthLabels,
            'growthLikes'     => $growthLikes,
            'growthBmarks'    => $growthBmarks,
            'growthComments'  => $growthComments,

            // ── Purchase analytics ──────────────────────────────────────────
            'purchase' => [
                'total_revenue_cc'       => $totalRevenueCC,
                'total_revenue_idr'      => $totalRevenueIDR,
                'revenue_this_month_cc'  => $revenueThisMonth,
                'revenue_this_month_idr' => $revenueThisMonthIDR,
                'revenue_change'         => $revenueChange,
                'total_transactions'     => $totalTransactions,
                'total_unlocks'          => $totalUnlocks,
                'unlocks_this_month'     => $unlocksThisMonth,
                'unlock_change'          => $unlockChange,
                'unique_buyers'          => $uniqueBuyers,
                'conversion_rate'        => $conversionRate,
                'peak_hour'              => $peakHourFmt,
                'peak_hour_raw'          => $peakHour,
            ],

            'topRevenueWorks'      => $topRevenueWorks,
            'topUnlockedChapters'  => $topUnlockedChapters,
            'recentUnlocks'        => $recentUnlocks,
            'revenueDailyLabels'   => $revenueDailyLabels,
            'revenueDailyCC'       => $revenueDailyCC,
            'revenueDailyUnlocks'  => $revenueDailyUnlocks,
            'unlocksByHour'        => array_values($unlocksByHour),
            'unlocksByDay'         => array_values($unlocksByDay),
            'dayNames'             => $dayNames,
        ];

        return view('creator/stats/index', $data);
    }

    public function work($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'creator') {
            return redirect()->to(base_url('creator/login'));
        }

        $userId   = session()->get('userId');
        $username = session()->get('username');

        $contentModel        = new ExploreContentModel();
        $userModel           = new UserModel();
        $creatorProfileModel = new CreatorProfileModel();
        $likeModel           = new \App\Models\LikeModel();
        $commentModel        = new \App\Models\CommentModel();
        $bookmarkModel       = new \App\Models\BookmarkModel();
        $chapterModel        = new \App\Models\ChapterModel();
        $db                  = \Config\Database::connect();

        // Fetch work and verify ownership
        $work = $contentModel->find($id);

        if (!$work || $work['creator_id'] !== $userId) {
            return redirect()->to(base_url('creator/stats'))
                             ->with('errors', ['auth' => 'Karya tidak ditemukan atau kamu bukan pemiliknya.']);
        }

        $user          = $userModel->find($userId);
        $creatorProfile = $creatorProfileModel->find($userId);

        $views    = (int)($work['view_count'] ?? 0);
        $likes    = $likeModel->where('work_id', $id)->countAllResults();
        $comments = $commentModel->where('work_id', $id)->countAllResults();
        $bookmarks = $bookmarkModel->where('work_id', $id)->countAllResults();

        // ── Quality Score for this work (Max 5.0)
        $qualityScore = 0.0;
        if ($views > 0) {
            $interactionRate = ($likes + $comments * 2 + $bookmarks * 3) / $views;
            $score           = 1.5 + ($interactionRate * 20);
            $qualityScore    = min(5.0, max(1.0, round($score, 1)));
        }
        if ($qualityScore >= 4.5)      { $qualityLabel = 'Luar Biasa'; }
        elseif ($qualityScore >= 3.5)  { $qualityLabel = 'Sangat Baik'; }
        elseif ($qualityScore >= 2.5)  { $qualityLabel = 'Baik'; }
        elseif ($qualityScore >= 1.5)  { $qualityLabel = 'Cukup'; }
        else                           { $qualityLabel = 'Perlu Ditingkatkan'; }

        // ── Engagement Rate
        $engagementRate = 0.0;
        if ($views > 0) {
            $engagementRate = min(100, round(($likes + $comments + $bookmarks) / $views * 100, 1));
        }

        // ── Distribution Data for chart
        $passiveReaders   = max(0, $views - ($bookmarks + $likes + $comments));
        $distributionData = [$passiveReaders, $likes, $bookmarks, $comments];
        if (array_sum($distributionData) == 0) {
            $distributionData = [1, 0, 0, 0];
        }

        // ── Real Daily Growth / Interaction Trend (Last 30 Days)
        $growthLabels   = [];
        $growthLikes    = [];
        $growthBmarks   = [];
        $growthComments = [];

        for ($i = 29; $i >= 0; $i--) {
            $dayLabel = date('d/m', strtotime("-{$i} days"));
            $dayStart = date('Y-m-d 00:00:00', strtotime("-{$i} days"));
            $dayEnd   = date('Y-m-d 23:59:59', strtotime("-{$i} days"));

            $growthLabels[] = $dayLabel;

            $growthLikes[]    = $likeModel->where('work_id', $id)->where('created_at >=', $dayStart)->where('created_at <=', $dayEnd)->countAllResults();
            $growthBmarks[]   = $bookmarkModel->where('work_id', $id)->where('created_at >=', $dayStart)->where('created_at <=', $dayEnd)->countAllResults();
            $growthComments[] = $commentModel->where('work_id', $id)->where('created_at >=', $dayStart)->where('created_at <=', $dayEnd)->countAllResults();
        }

        // ── Get all chapters of this work
        $chapters = $chapterModel->where('work_id', $id)->orderBy('order_num', 'ASC')->findAll();
        $chapterIds = array_column($chapters, 'id');

        // ── Real Purchase & Revenue Analysis
        $transactions = [];
        $totalRevenueCC = 0;
        $revenueThisMonth = 0;
        $revenueLastMonth = 0;

        $thisMonthStart = date('Y-m-01 00:00:00');
        $lastMonthStart = date('Y-m-01 00:00:00', strtotime('-1 month'));
        $lastMonthEnd   = date('Y-m-t 23:59:59', strtotime('-1 month'));

        // Query transactions belonging to this creator that reference this work or its chapters
        $txBuilder = $db->table('transactions')
            ->where('user_id', $userId)
            ->where('type', 'in');

        if (!empty($chapterIds)) {
            $txBuilder->groupStart()
                ->groupStart()
                    ->whereIn('category', ['purchase', 'work_purchase'])
                    ->where('reference_id', $id)
                ->groupEnd()
                ->orGroupStart()
                    ->whereIn('category', ['unlock', 'chapter_unlock'])
                    ->whereIn('reference_id', $chapterIds)
                ->groupEnd()
            ->groupEnd();
        } else {
            $txBuilder->whereIn('category', ['purchase', 'work_purchase'])
                      ->where('reference_id', $id);
        }

        $transactions = $txBuilder->orderBy('created_at', 'DESC')->get()->getResultArray();

        foreach ($transactions as $tx) {
            $amount = (int)$tx['amount'];
            $totalRevenueCC += $amount;
            if ($tx['created_at'] >= $thisMonthStart) {
                $revenueThisMonth += $amount;
            }
            if ($tx['created_at'] >= $lastMonthStart && $tx['created_at'] <= $lastMonthEnd) {
                $revenueLastMonth += $amount;
            }
        }

        // Helper percent change
        $pctChange = function(int $prev, int $curr): array {
            if ($prev === 0) { return ['value' => $curr > 0 ? 100 : 0, 'up' => true]; }
            $delta = round(($curr - $prev) / $prev * 100);
            return ['value' => abs($delta), 'up' => $delta >= 0];
        };
        $revenueChange = $pctChange($revenueLastMonth, $revenueThisMonth);

        // ── Chapter unlock breakdown
        $chaptersWithStats = [];
        $unlockedRows = [];
        if (!empty($chapterIds)) {
            $unlockedRows = $db->table('unlocked_chapters')
                ->select('unlocked_chapters.*, chapters.title as chapter_title, chapters.price, chapters.order_num, users.username as buyer_name')
                ->join('chapters', 'chapters.id = unlocked_chapters.chapter_id')
                ->join('users', 'users.id = unlocked_chapters.user_id', 'left')
                ->whereIn('unlocked_chapters.chapter_id', $chapterIds)
                ->orderBy('unlocked_chapters.created_at', 'DESC')
                ->get()
                ->getResultArray();

            foreach ($chapters as $ch) {
                $cid = $ch['id'];
                $unlocksCount = 0;
                $chRevenue = 0;
                foreach ($unlockedRows as $ur) {
                    if ((int)$ur['chapter_id'] === (int)$cid) {
                        $unlocksCount++;
                        $chRevenue += (int)($ur['price'] ?? 0);
                    }
                }
                $chaptersWithStats[] = [
                    'id'        => $cid,
                    'title'     => $ch['title'],
                    'order_num' => $ch['order_num'],
                    'price'     => $ch['price'],
                    'is_locked' => $ch['is_locked'],
                    'unlocks'   => $unlocksCount,
                    'revenue'   => $chRevenue,
                ];
            }
            // Sort chapters by order_num
            usort($chaptersWithStats, fn($a, $b) => $a['order_num'] <=> $b['order_num']);
        }

        // ── Hour of day purchase patterns
        $unlocksByHour = array_fill(0, 24, 0);
        foreach ($unlockedRows as $ur) {
            $hour = (int)date('G', strtotime($ur['created_at']));
            $unlocksByHour[$hour]++;
        }
        $peakHour     = array_search(max($unlocksByHour), $unlocksByHour);
        $peakHourFmt  = sprintf('%02d:00–%02d:00', $peakHour, $peakHour + 1);

        // ── Day of week purchase patterns
        $unlocksByDay = array_fill(0, 7, 0);
        foreach ($unlockedRows as $ur) {
            $dow = (int)date('w', strtotime($ur['created_at']));
            $unlocksByDay[$dow]++;
        }
        $dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

        // ── Daily revenue trend (Last 30 Days)
        $revenueDailyLabels  = [];
        $revenueDailyCC      = [];
        $revenueDailyUnlocks = [];

        for ($i = 29; $i >= 0; $i--) {
            $dayLabel = date('d/m', strtotime("-{$i} days"));
            $dayStart = date('Y-m-d 00:00:00', strtotime("-{$i} days"));
            $dayEnd   = date('Y-m-d 23:59:59', strtotime("-{$i} days"));

            $revenueDailyLabels[] = $dayLabel;

            $dayCC = 0;
            foreach ($transactions as $tx) {
                if ($tx['created_at'] >= $dayStart && $tx['created_at'] <= $dayEnd) {
                    $dayCC += (int)$tx['amount'];
                }
            }
            $revenueDailyCC[] = $dayCC;

            $dayUnlocks = 0;
            foreach ($unlockedRows as $ur) {
                if ($ur['created_at'] >= $dayStart && $ur['created_at'] <= $dayEnd) {
                    $dayUnlocks++;
                }
            }
            $revenueDailyUnlocks[] = $dayUnlocks;
        }

        // ── Unique buyers & conversion rate
        $uniqueBuyers = count(array_unique(array_column($unlockedRows, 'user_id')));
        $conversionRate = $views > 0 ? round($uniqueBuyers / $views * 100, 2) : 0.0;

        // ── Fetch Recent Comments
        $recentComments = $commentModel->getByWork($id);
        $recentComments = array_slice($recentComments, 0, 10);

        $data = [
            'title'          => 'Statistik Karya - NusaShare',
            'username'       => $username,
            'user'           => $user,
            'creatorProfile' => $creatorProfile,
            'work'           => $work,
            'stats'          => [
                'views'            => $views,
                'likes'            => $likes,
                'comments'         => $comments,
                'bookmarks'        => $bookmarks,
                'quality_score'    => number_format($qualityScore, 1),
                'quality_label'    => $qualityLabel,
                'engagement_rate'  => $engagementRate,
                'total_income'     => $totalRevenueCC,
                'total_income_idr' => $totalRevenueCC * 10,
                'income_this_month'=> $revenueThisMonth,
                'income_change'    => $revenueChange,
                'total_unlocks'    => count($unlockedRows),
                'unique_buyers'    => $uniqueBuyers,
                'conversion_rate'  => $conversionRate,
                'peak_hour'        => $peakHourFmt,
            ],
            'distributionData'    => $distributionData,
            'growthLabels'        => $growthLabels,
            'growthLikes'         => $growthLikes,
            'growthBmarks'        => $growthBmarks,
            'growthComments'      => $growthComments,
            'chaptersWithStats'   => $chaptersWithStats,
            'recentUnlocks'       => array_slice($unlockedRows, 0, 10),
            'recentComments'      => $recentComments,
            'activePage'          => 'stats', // Make active page stats to highlight sidebar correctly
            
            // Purchase/unlock trends for this work
            'revenueDailyLabels'  => $revenueDailyLabels,
            'revenueDailyCC'      => $revenueDailyCC,
            'revenueDailyUnlocks' => $revenueDailyUnlocks,
            'unlocksByHour'       => array_values($unlocksByHour),
            'unlocksByDay'        => array_values($unlocksByDay),
            'dayNames'            => $dayNames,
        ];

        return view('creator/stats/work', $data);
    }
}
