<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    /**
     * Total users registered (role = user or creator, excluding admin)
     */
    public function getTotalUsers(): int
    {
        return (int) $this->db->table('users')
            ->whereIn('role', ['user', 'creator'])
            ->countAllResults();
    }

    /**
     * Total active/verified creators
     */
    public function getTotalCreators(): int
    {
        return (int) $this->db->table('users')
            ->where('role', 'creator')
            ->countAllResults();
    }

    /**
     * Total published works
     */
    public function getTotalWorks(): int
    {
        return (int) $this->db->table('works')
            ->whereIn('status', ['published', 'curated', 'museum'])
            ->countAllResults();
    }

    /**
     * Total all works (including drafts)
     */
    public function getTotalWorksAll(): int
    {
        return (int) $this->db->table('works')->countAllResults();
    }

    /**
     * Total CC (credits) in circulation across all user wallets
     */
    public function getTotalCCCirculation(): int
    {
        $result = $this->db->table('credits')->selectSum('balance')->get()->getRowArray();
        return (int) ($result['balance'] ?? 0);
    }

    /**
     * Total revenue from approved top-ups this month (amount in IDR equivalent)
     * Transactions with type='topup' and positive amount
     */
    public function getMonthlyRevenue(): int
    {
        $startOfMonth = date('Y-m-01 00:00:00');
        $result = $this->db->table('transactions')
            ->selectSum('amount')
            ->where('type', 'in')
            ->where('category', 'topup')
            ->where('created_at >=', $startOfMonth)
            ->get()->getRowArray();
        return (int) ($result['amount'] ?? 0);
    }

    /**
     * New users registered in current month
     */
    public function getNewUsersThisMonth(): int
    {
        return (int) $this->db->table('users')
            ->whereIn('role', ['user', 'creator'])
            ->where('created_at >=', date('Y-m-01 00:00:00'))
            ->countAllResults();
    }

    /**
     * New users registered last month (for growth comparison)
     */
    public function getNewUsersLastMonth(): int
    {
        $lastMonthStart = date('Y-m-01 00:00:00', strtotime('first day of last month'));
        $lastMonthEnd   = date('Y-m-t 23:59:59', strtotime('first day of last month'));
        return (int) $this->db->table('users')
            ->whereIn('role', ['user', 'creator'])
            ->where('created_at >=', $lastMonthStart)
            ->where('created_at <=', $lastMonthEnd)
            ->countAllResults();
    }

    /**
     * Get recent transactions (last N rows) joined with username
     */
    public function getRecentTransactions(int $limit = 5): array
    {
        return $this->db->table('transactions t')
            ->select('t.id, t.amount, t.type, t.category, t.description, t.created_at, u.username')
            ->join('users u', 'u.id = t.user_id', 'left')
            ->orderBy('t.created_at', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }

    /**
     * Get top creators by starsoul_value
     */
    public function getTopCreators(int $limit = 5): array
    {
        return $this->db->table('users u')
            ->select('u.id, u.username, u.starsoul_value, u.created_at, cp.display_name, cp.profile_image')
            ->join('creator_profiles cp', 'cp.user_id = u.id', 'left')
            ->where('u.role', 'creator')
            ->orderBy('u.starsoul_value', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }

    /**
     * Count works per content_type (for chart breakdown)
     */
    public function getWorksBreakdown(): array
    {
        $rows = $this->db->table('works')
            ->select('content_type, COUNT(*) as total')
            ->groupBy('content_type')
            ->get()->getResultArray();

        $result = [];
        foreach ($rows as $row) {
            $result[$row['content_type']] = (int) $row['total'];
        }
        return $result;
    }

    /**
     * Monthly user registration count for the last 6 months (for chart)
     */
    public function getMonthlyRegistrations(int $months = 6): array
    {
        $labels = [];
        $counts = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $start  = date('Y-m-01 00:00:00', strtotime("-$i months"));
            $end    = date('Y-m-t 23:59:59', strtotime("-$i months"));
            $label  = date('M Y', strtotime("-$i months"));

            $count = (int) $this->db->table('users')
                ->whereIn('role', ['user', 'creator'])
                ->where('created_at >=', $start)
                ->where('created_at <=', $end)
                ->countAllResults();

            $labels[] = $label;
            $counts[] = $count;
        }

        return ['labels' => $labels, 'counts' => $counts];
    }

    /**
     * Monthly top-up credit count for the last 6 months (for chart)
     */
    public function getMonthlyTopups(int $months = 6): array
    {
        $labels = [];
        $counts = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $start  = date('Y-m-01 00:00:00', strtotime("-$i months"));
            $end    = date('Y-m-t 23:59:59', strtotime("-$i months"));
            $label  = date('M Y', strtotime("-$i months"));

            $result = $this->db->table('transactions')
                ->selectSum('amount')
                ->where('type', 'in')
                ->where('category', 'topup')
                ->where('created_at >=', $start)
                ->where('created_at <=', $end)
                ->get()->getRowArray();

            $labels[] = $label;
            $counts[] = (int) ($result['amount'] ?? 0);
        }

        return ['labels' => $labels, 'counts' => $counts];
    }

    /**
     * Count total chapter/image releases this week
     */
    public function getReleasesThisWeek(): int
    {
        $weekStart = date('Y-m-d 00:00:00', strtotime('monday this week'));
        return (int) $this->db->table('chapters')
            ->where('created_at >=', $weekStart)
            ->countAllResults();
    }

    // ═══════════════════════════════════════════════════
    //  USER MANAGEMENT
    // ═══════════════════════════════════════════════════

    /**
     * Get paginated user list with optional search & status filter.
     * Returns ['data' => [...], 'total' => int, 'pages' => int]
     */
    public function getUsers(string $search = '', string $status = '', int $page = 1, int $perPage = 15): array
    {
        $builder = $this->db->table('users u')
            ->select('u.id, u.username, u.email, u.role, u.is_active, u.created_at,
                      COALESCE(c.balance, 0) AS cc_balance,
                      up.display_name, up.profile_image')
            ->join('credits c', 'c.user_id = u.id', 'left')
            ->join('user_profiles up', 'up.user_id = u.id', 'left')
            ->whereIn('u.role', ['user', 'creator']);

        // Search
        if ($search !== '') {
            $builder->groupStart()
                ->like('u.username', $search)
                ->orLike('u.email', $search)
                ->orLike('up.display_name', $search)
                ->groupEnd();
        }

        // Status filter
        if ($status === 'active') {
            $builder->where('u.is_active', 1);
        } elseif ($status === 'suspended') {
            $builder->where('u.is_active', 0);
        } elseif ($status === 'banned') {
            $builder->where('u.is_active', -1);
        } elseif ($status === 'creator') {
            $builder->where('u.role', 'creator');
        }

        // Count total before paging
        $total = $builder->countAllResults(false);

        // Paginate
        $offset = ($page - 1) * $perPage;
        $data   = $builder->orderBy('u.created_at', 'DESC')
            ->limit($perPage, $offset)
            ->get()->getResultArray();

        return [
            'data'  => $data,
            'total' => $total,
            'pages' => (int) ceil($total / $perPage),
            'page'  => $page,
        ];
    }

    /**
     * Get a single user with credit balance and profile.
     */
    public function getUserById(string $userId): ?array
    {
        $row = $this->db->table('users u')
            ->select('u.id, u.username, u.email, u.role, u.is_active, u.created_at, u.starsoul_value,
                      COALESCE(c.balance, 0) AS cc_balance,
                      up.display_name, up.profile_image, up.bio')
            ->join('credits c', 'c.user_id = u.id', 'left')
            ->join('user_profiles up', 'up.user_id = u.id', 'left')
            ->where('u.id', $userId)
            ->get()->getRowArray();

        return $row ?: null;
    }

    /**
     * Update user is_active status.
     *  1  = Active
     *  0  = Suspended
     * -1  = Banned
     */
    public function updateUserStatus(string $userId, int $status): bool
    {
        return $this->db->table('users')
            ->where('id', $userId)
            ->update(['is_active' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Add or subtract CC balance for a user.
     * Positive $amount = credit; negative = debit.
     * Also inserts a transaction log row.
     */
    public function adjustUserCCBalance(string $userId, int $amount, string $reason, string $adminUsername): bool
    {
        $this->db->transStart();

        // Upsert credit balance
        $existing = $this->db->table('credits')->where('user_id', $userId)->get()->getRowArray();
        if ($existing) {
            $newBalance = max(0, (int)$existing['balance'] + $amount);
            $this->db->table('credits')->where('user_id', $userId)->update([
                'balance'    => $newBalance,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            $this->db->table('credits')->insert([
                'user_id'    => $userId,
                'balance'    => max(0, $amount),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        // Log in transactions — 'in' = saldo masuk (tambah), 'out' = saldo keluar (kurangi)
        $this->db->table('transactions')->insert([
            'user_id'     => $userId,
            'type'        => $amount >= 0 ? 'in' : 'out',
            'category'    => 'admin_adjustment',
            'amount'      => abs($amount),
            'description' => "[Admin: {$adminUsername}] {$reason}",
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        $this->db->transComplete();
        return $this->db->transStatus();
    }

    /**
     * Get summary stats for a specific user (total spent CC, total received CC, transaction count).
     */
    public function getUserStats(string $userId): array
    {
        $spent = (int) ($this->db->table('transactions')
            ->selectSum('amount')
            ->where('user_id', $userId)
            ->where('type', 'out') // CC keluar dari dompet user
            ->get()->getRowArray()['amount'] ?? 0);

        $earned = (int) ($this->db->table('transactions')
            ->selectSum('amount')
            ->where('user_id', $userId)
            ->where('type', 'in') // CC masuk ke dompet user
            ->get()->getRowArray()['amount'] ?? 0);

        $txCount = (int) $this->db->table('transactions')
            ->where('user_id', $userId)
            ->countAllResults();

        $worksUnlocked = (int) $this->db->table('transactions')
            ->where('user_id', $userId)
            ->whereIn('category', ['unlock_chapter', 'unlock_work', 'unlock'])
            ->countAllResults();

        return [
            'total_spent'    => $spent,
            'total_earned'   => $earned,
            'tx_count'       => $txCount,
            'works_unlocked' => $worksUnlocked,
        ];
    }

    // ═══════════════════════════════════════════════════
    //  CREATOR MANAGEMENT
    // ═══════════════════════════════════════════════════

    /**
     * Get paginated creator list with optional search & status filter.
     * Status tab: 'pending' (starsoul_status=probation & is_active=1),
     *             'active'  (is_active=1 & starsoul_status != probation),
     *             'suspended' (is_active=0 or is_active=-1),
     *             '' = semua kreator
     * Returns ['data' => [...], 'total' => int, 'pages' => int, 'page' => int]
     */
    public function getCreators(string $search = '', string $tab = '', int $page = 1, int $perPage = 15): array
    {
        $builder = $this->db->table('users u')
            ->select('u.id, u.username, u.email, u.role, u.is_active,
                      u.starsoul_value, u.starsoul_status, u.created_at,
                      COALESCE(c.balance, 0) AS cc_balance,
                      cp.display_name, cp.profile_image, cp.last_active_at,
                      COUNT(DISTINCT w.id) AS works_count,
                      COALESCE(SUM(w.view_count), 0) AS total_views,
                      COUNT(DISTINCT f.follower_id) AS follower_count')
            ->join('credits c', 'c.user_id = u.id', 'left')
            ->join('creator_profiles cp', 'cp.user_id = u.id', 'left')
            ->join('works w', 'w.creator_id = u.id', 'left')
            ->join('follows f', 'f.followed_id = u.id', 'left')
            ->where('u.role', 'creator')
            ->groupBy('u.id');

        // Search
        if ($search !== '') {
            $builder->groupStart()
                ->like('u.username', $search)
                ->orLike('u.email', $search)
                ->orLike('cp.display_name', $search)
                ->groupEnd();
        }

        // Tab filter
        if ($tab === 'pending') {
            // Kreator baru yang belum pernah publikasi karya (masih probation/belum aktif penuh)
            $builder->where('u.is_active', 1)
                    ->where('u.starsoul_status', 'probation');
        } elseif ($tab === 'active') {
            $builder->where('u.is_active', 1)
                    ->where('u.starsoul_status !=', 'probation');
        } elseif ($tab === 'suspended') {
            $builder->groupStart()
                ->where('u.is_active', 0)
                ->orWhere('u.is_active', -1)
                ->groupEnd();
        }

        // Count total sebelum paging
        $total = $builder->countAllResults(false);

        // Paginate
        $offset = ($page - 1) * $perPage;
        $data   = $builder->orderBy('u.created_at', 'DESC')
            ->limit($perPage, $offset)
            ->get()->getResultArray();

        return [
            'data'  => $data,
            'total' => $total,
            'pages' => (int) ceil($total / max(1, $perPage)),
            'page'  => $page,
        ];
    }

    /**
     * Get a single creator detail with profile and CC balance.
     */
    public function getCreatorById(string $userId): ?array
    {
        $row = $this->db->table('users u')
            ->select('u.id, u.username, u.email, u.role, u.is_active, u.created_at,
                      u.starsoul_value, u.starsoul_status, u.engagement, u.commitment, u.behavior,
                      COALESCE(c.balance, 0) AS cc_balance,
                      cp.display_name, cp.profile_image, cp.bio, cp.last_active_at')
            ->join('credits c', 'c.user_id = u.id', 'left')
            ->join('creator_profiles cp', 'cp.user_id = u.id', 'left')
            ->where('u.id', $userId)
            ->where('u.role', 'creator')
            ->get()->getRowArray();

        return $row ?: null;
    }

    /**
     * Get creator statistics: works count, total views, chapters published,
     * CC earned, total followers, transaction count.
     */
    public function getCreatorStats(string $userId): array
    {
        $worksCount = (int) $this->db->table('works')
            ->where('creator_id', $userId)
            ->countAllResults();

        $publishedWorks = (int) $this->db->table('works')
            ->where('creator_id', $userId)
            ->whereIn('status', ['published', 'curated', 'museum'])
            ->countAllResults();

        $totalViews = (int) ($this->db->table('works')
            ->selectSum('view_count')
            ->where('creator_id', $userId)
            ->get()->getRowArray()['view_count'] ?? 0);

        $chaptersCount = (int) $this->db->table('chapters ch')
            ->join('works w', 'w.id = ch.work_id', 'inner')
            ->where('w.creator_id', $userId)
            ->countAllResults();

        $ccEarned = (int) ($this->db->table('transactions')
            ->selectSum('amount')
            ->where('user_id', $userId)
            ->where('type', 'in')
            ->get()->getRowArray()['amount'] ?? 0);

        $followerCount = (int) $this->db->table('follows')
            ->where('followed_id', $userId)
            ->countAllResults();

        return [
            'works_count'     => $worksCount,
            'published_works' => $publishedWorks,
            'total_views'     => $totalViews,
            'chapters_count'  => $chaptersCount,
            'cc_earned'       => $ccEarned,
            'follower_count'  => $followerCount,
        ];
    }

    /**
     * Update starsoul_status for a creator (used to toggle monetization/probation).
     * 'normal' = aktif penuh, 'warning' = peringatan, 'probation' = dibekukan/pending
     */
    public function updateCreatorStarsoulStatus(string $userId, string $status): bool
    {
        $allowed = ['normal', 'warning', 'probation'];
        if (!in_array($status, $allowed)) {
            return false;
        }

        return $this->db->table('users')
            ->where('id', $userId)
            ->where('role', 'creator')
            ->update(['starsoul_status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
    }

    // ═══════════════════════════════════════════════════
    //  TRANSACTION HISTORY
    // ═══════════════════════════════════════════════════

    /**
     * Get paginated transaction log with optional search, type, category, date filter.
     * Returns ['data' => [...], 'total' => int, 'pages' => int, 'page' => int]
     *
     * @param string $search      Search by username or description
     * @param string $type        'in' | 'out' | '' (all)
     * @param string $category    e.g. 'topup', 'unlock_chapter', 'admin_adjustment', '' (all)
     * @param string $dateFrom    Y-m-d
     * @param string $dateTo      Y-m-d
     * @param int    $page
     * @param int    $perPage
     */
    public function getTransactions(
        string $search   = '',
        string $type     = '',
        string $category = '',
        string $dateFrom = '',
        string $dateTo   = '',
        int    $page     = 1,
        int    $perPage  = 20
    ): array {
        $builder = $this->db->table('transactions t')
            ->select('t.id, t.user_id, t.type, t.category, t.amount, t.description, t.created_at,
                      u.username, u.role,
                      COALESCE(up.display_name, u.username) AS display_name,
                      up.profile_image')
            ->join('users u',         'u.id = t.user_id', 'left')
            ->join('user_profiles up', 'up.user_id = t.user_id', 'left');

        // Filter: search
        if ($search !== '') {
            $builder->groupStart()
                ->like('u.username', $search)
                ->orLike('t.description', $search)
                ->orLike('up.display_name', $search)
                ->groupEnd();
        }

        // Filter: type (in / out)
        if ($type !== '') {
            $builder->where('t.type', $type);
        }

        // Filter: category
        if ($category !== '') {
            $builder->where('t.category', $category);
        }

        // Filter: date range
        if ($dateFrom !== '') {
            $builder->where('t.created_at >=', $dateFrom . ' 00:00:00');
        }
        if ($dateTo !== '') {
            $builder->where('t.created_at <=', $dateTo . ' 23:59:59');
        }

        // Count total before paging
        $total = $builder->countAllResults(false);

        // Paginate
        $offset = ($page - 1) * $perPage;
        $data   = $builder->orderBy('t.created_at', 'DESC')
            ->limit($perPage, $offset)
            ->get()->getResultArray();

        return [
            'data'  => $data,
            'total' => $total,
            'pages' => (int) ceil($total / max(1, $perPage)),
            'page'  => $page,
        ];
    }

    /**
     * Get summary statistics for the transaction history section.
     * Returns total CC spent (out), total CC received (in), topup volume, and total tx count.
     */
    public function getTransactionStats(): array
    {
        // Total CC keluar (pengeluaran user: unlock, download, dll)
        $totalOut = (int) ($this->db->table('transactions')
            ->selectSum('amount')
            ->where('type', 'out')
            ->get()->getRowArray()['amount'] ?? 0);

        // Total CC masuk (topup, reward, admin adjustment+)
        $totalIn = (int) ($this->db->table('transactions')
            ->selectSum('amount')
            ->where('type', 'in')
            ->get()->getRowArray()['amount'] ?? 0);

        // Total CC topup saja
        $totalTopup = (int) ($this->db->table('transactions')
            ->selectSum('amount')
            ->where('type', 'in')
            ->where('category', 'topup')
            ->get()->getRowArray()['amount'] ?? 0);

        // Total transaksi keseluruhan
        $totalCount = (int) $this->db->table('transactions')->countAllResults();

        // Total CC unlock chapter
        $totalUnlock = (int) ($this->db->table('transactions')
            ->selectSum('amount')
            ->where('type', 'out')
            ->whereIn('category', ['unlock_chapter', 'unlock_work', 'unlock'])
            ->get()->getRowArray()['amount'] ?? 0);

        // Distinct categories in use
        $categories = $this->db->table('transactions')
            ->select('DISTINCT category')
            ->get()->getResultArray();
        $categoryList = array_column($categories, 'category');

        return [
            'total_out'    => $totalOut,
            'total_in'     => $totalIn,
            'total_topup'  => $totalTopup,
            'total_count'  => $totalCount,
            'total_unlock' => $totalUnlock,
            'categories'   => $categoryList,
        ];
    }
}
