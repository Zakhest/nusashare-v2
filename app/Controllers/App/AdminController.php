<?php

namespace App\Controllers\App;

use App\Controllers\BaseController;
use App\Models\AdminModel;

class AdminController extends BaseController
{
    protected AdminModel $adminModel;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
    }

    // ═══════════════════════════════════════════════════
    //  MAIN ADMIN PANEL VIEW
    // ═══════════════════════════════════════════════════

    public function index()
    {
        $session = session();

        // Ambil data statistik real dari database
        $totalUsers       = $this->adminModel->getTotalUsers();
        $totalCreators    = $this->adminModel->getTotalCreators();
        $totalWorks       = $this->adminModel->getTotalWorks();
        $totalCC          = $this->adminModel->getTotalCCCirculation();
        $monthlyRevenue   = $this->adminModel->getMonthlyRevenue();
        $newUsersMonth    = $this->adminModel->getNewUsersThisMonth();
        $newUsersLast     = $this->adminModel->getNewUsersLastMonth();
        $recentTrx        = $this->adminModel->getRecentTransactions(5);
        $topCreators      = $this->adminModel->getTopCreators(5);
        $worksBreakdown   = $this->adminModel->getWorksBreakdown();
        $releasesWeek     = $this->adminModel->getReleasesThisWeek();

        // Data chart
        $regChart    = $this->adminModel->getMonthlyRegistrations(6);
        $topupChart  = $this->adminModel->getMonthlyTopups(6);

        // Hitung persentase pertumbuhan user
        $userGrowthPct = $newUsersLast > 0
            ? round((($newUsersMonth - $newUsersLast) / $newUsersLast) * 100, 1)
            : ($newUsersMonth > 0 ? 100 : 0);

        $data = [
            'title'           => 'Alpha Admin Panel - NusaShare',
            // Stat cards
            'totalUsers'      => $totalUsers,
            'totalCreators'   => $totalCreators,
            'totalWorks'      => $totalWorks,
            'totalCC'         => $totalCC,
            'monthlyRevenue'  => $monthlyRevenue,
            'newUsersMonth'   => $newUsersMonth,
            'userGrowthPct'   => $userGrowthPct,
            'releasesWeek'    => $releasesWeek,
            // Table data
            'recentTrx'       => $recentTrx,
            'topCreators'     => $topCreators,
            'worksBreakdown'  => $worksBreakdown,
            // Chart data (JSON encoded for Chart.js)
            'regChartLabels'  => json_encode($regChart['labels']),
            'regChartData'    => json_encode($regChart['counts']),
            'topupChartLabels' => json_encode($topupChart['labels']),
            'topupChartData'   => json_encode($topupChart['counts']),
            // Admin info from session
            'adminUsername'   => $session->get('username') ?? 'Admin',
        ];

        return view('admin/index', $data);
    }

    // ═══════════════════════════════════════════════════
    //  USER MANAGEMENT — AJAX API ENDPOINTS
    // ═══════════════════════════════════════════════════

    /**
     * GET alpha-admin/api/users
     * Query params: search, status, page, per_page
     * Returns JSON list of users with pagination meta.
     */
    public function apiUsers()
    {
        $search  = trim($this->request->getGet('search') ?? '');
        $status  = trim($this->request->getGet('status') ?? '');
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = max(5, min(100, (int) ($this->request->getGet('per_page') ?? 15)));

        $result = $this->adminModel->getUsers($search, $status, $page, $perPage);

        return $this->response->setJSON([
            'success' => true,
            'data'    => $result['data'],
            'meta'    => [
                'total'    => $result['total'],
                'pages'    => $result['pages'],
                'page'     => $result['page'],
                'per_page' => $perPage,
            ],
        ]);
    }

    /**
     * GET alpha-admin/api/users/{id}
     * Returns single user detail + stats.
     */
    public function apiUserDetail(string $userId)
    {
        $user = $this->adminModel->getUserById($userId);
        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'User tidak ditemukan.',
            ]);
        }

        $stats = $this->adminModel->getUserStats($userId);

        return $this->response->setJSON([
            'success' => true,
            'data'    => array_merge($user, ['stats' => $stats]),
        ]);
    }

    /**
     * POST alpha-admin/api/users/{id}/status
     * Body (JSON): { "status": "active"|"suspended"|"banned" }
     */
    public function apiUpdateUserStatus(string $userId)
    {
        $body   = $this->request->getJSON(true) ?? [];
        $status = $body['status'] ?? '';

        $statusMap = [
            'active'    => 1,
            'suspended' => 0,
            'banned'    => -1,
        ];

        if (!array_key_exists($status, $statusMap)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Status tidak valid. Gunakan: active, suspended, banned.',
            ]);
        }

        $user = $this->adminModel->getUserById($userId);
        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'User tidak ditemukan.',
            ]);
        }

        $ok = $this->adminModel->updateUserStatus($userId, $statusMap[$status]);

        return $this->response->setJSON([
            'success' => $ok,
            'message' => $ok ? 'Status user berhasil diperbarui.' : 'Gagal memperbarui status.',
        ]);
    }

    /**
     * POST alpha-admin/api/users/{id}/adjust-cc
     * Body (JSON): { "amount": 500, "reason": "...", "type": "add"|"deduct" }
     */
    public function apiAdjustUserCC(string $userId)
    {
        $session = session();
        $body    = $this->request->getJSON(true) ?? [];

        $amount = (int) ($body['amount'] ?? 0);
        $reason = trim($body['reason'] ?? 'Penyesuaian admin');
        $type   = $body['type'] ?? 'add'; // 'add' or 'deduct'

        if ($amount <= 0) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Jumlah CC harus lebih dari 0.',
            ]);
        }

        $user = $this->adminModel->getUserById($userId);
        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'User tidak ditemukan.',
            ]);
        }

        $finalAmount    = $type === 'deduct' ? -$amount : $amount;
        $adminUsername  = $session->get('username') ?? 'Admin';

        $ok = $this->adminModel->adjustUserCCBalance($userId, $finalAmount, $reason, $adminUsername);

        // Return updated balance
        $updatedUser = $this->adminModel->getUserById($userId);

        return $this->response->setJSON([
            'success'     => $ok,
            'message'     => $ok ? 'Saldo CC berhasil disesuaikan.' : 'Gagal menyesuaikan saldo.',
            'new_balance' => (int) ($updatedUser['cc_balance'] ?? 0),
        ]);
    }

    // ═══════════════════════════════════════════════════
    //  CREATOR MANAGEMENT — AJAX API ENDPOINTS
    // ═══════════════════════════════════════════════════

    /**
     * GET alpha-admin/api/creators
     * Query params: search, tab (all|pending|active|suspended), page, per_page
     */
    public function apiCreators()
    {
        $search  = trim($this->request->getGet('search') ?? '');
        $tab     = trim($this->request->getGet('tab') ?? '');
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = max(5, min(100, (int) ($this->request->getGet('per_page') ?? 15)));

        $result = $this->adminModel->getCreators($search, $tab, $page, $perPage);

        return $this->response->setJSON([
            'success' => true,
            'data'    => $result['data'],
            'meta'    => [
                'total'    => $result['total'],
                'pages'    => $result['pages'],
                'page'     => $result['page'],
                'per_page' => $perPage,
            ],
        ]);
    }

    /**
     * GET alpha-admin/api/creators/{id}
     * Returns single creator detail + stats.
     */
    public function apiCreatorDetail(string $userId)
    {
        $creator = $this->adminModel->getCreatorById($userId);
        if (!$creator) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Kreator tidak ditemukan.',
            ]);
        }

        $stats = $this->adminModel->getCreatorStats($userId);

        return $this->response->setJSON([
            'success' => true,
            'data'    => array_merge($creator, ['stats' => $stats]),
        ]);
    }

    /**
     * POST alpha-admin/api/creators/{id}/status
     * Body (JSON): { "status": "active"|"suspended"|"banned" }
     */
    public function apiUpdateCreatorStatus(string $userId)
    {
        $body   = $this->request->getJSON(true) ?? [];
        $status = $body['status'] ?? '';

        $statusMap = [
            'active'    => 1,
            'suspended' => 0,
            'banned'    => -1,
        ];

        if (!array_key_exists($status, $statusMap)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Status tidak valid. Gunakan: active, suspended, banned.',
            ]);
        }

        $creator = $this->adminModel->getCreatorById($userId);
        if (!$creator) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Kreator tidak ditemukan.',
            ]);
        }

        $ok = $this->adminModel->updateUserStatus($userId, $statusMap[$status]);

        return $this->response->setJSON([
            'success' => $ok,
            'message' => $ok ? 'Status kreator berhasil diperbarui.' : 'Gagal memperbarui status.',
        ]);
    }

    /**
     * POST alpha-admin/api/creators/{id}/starsoul-status
     * Body (JSON): { "starsoul_status": "normal"|"warning"|"probation" }
     * Digunakan untuk approve/pending kreator (toggle antara normal <-> probation)
     */
    public function apiUpdateCreatorStarsoulStatus(string $userId)
    {
        $body   = $this->request->getJSON(true) ?? [];
        $status = $body['starsoul_status'] ?? '';

        $allowed = ['normal', 'warning', 'probation'];
        if (!in_array($status, $allowed)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Starsoul status tidak valid. Gunakan: normal, warning, probation.',
            ]);
        }

        $creator = $this->adminModel->getCreatorById($userId);
        if (!$creator) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Kreator tidak ditemukan.',
            ]);
        }

        $ok = $this->adminModel->updateCreatorStarsoulStatus($userId, $status);

        return $this->response->setJSON([
            'success' => $ok,
            'message' => $ok ? 'Status kreator berhasil diperbarui.' : 'Gagal memperbarui status.',
        ]);
    }

    // ═══════════════════════════════════════════════════
    //  TRANSACTION HISTORY — AJAX API ENDPOINTS
    // ═══════════════════════════════════════════════════

    /**
     * GET alpha-admin/api/transactions
     * Query params: search, type (in|out|''), category, date_from (Y-m-d), date_to (Y-m-d), page, per_page
     * Returns JSON paginated transaction list.
     */
    public function apiTransactions()
    {
        $search   = trim($this->request->getGet('search')    ?? '');
        $type     = trim($this->request->getGet('type')      ?? '');
        $category = trim($this->request->getGet('category')  ?? '');
        $dateFrom = trim($this->request->getGet('date_from') ?? '');
        $dateTo   = trim($this->request->getGet('date_to')   ?? '');
        $page     = max(1, (int) ($this->request->getGet('page')     ?? 1));
        $perPage  = max(5, min(100, (int) ($this->request->getGet('per_page') ?? 20)));

        $result = $this->adminModel->getTransactions($search, $type, $category, $dateFrom, $dateTo, $page, $perPage);

        return $this->response->setJSON([
            'success' => true,
            'data'    => $result['data'],
            'meta'    => [
                'total'    => $result['total'],
                'pages'    => $result['pages'],
                'page'     => $result['page'],
                'per_page' => $perPage,
            ],
        ]);
    }

    /**
     * GET alpha-admin/api/transactions/stats
     * Returns aggregated stats: total_in, total_out, total_topup, total_count, etc.
     */
    public function apiTransactionStats()
    {
        $stats = $this->adminModel->getTransactionStats();

        return $this->response->setJSON([
            'success' => true,
            'data'    => $stats,
        ]);
    }
}

