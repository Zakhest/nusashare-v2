<?php

namespace App\Controllers\Creator;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\CreatorProfileModel;
use App\Models\CreditModel;

class MonetizationController extends BaseController
{
    // ─── Auth Guard ─────────────────────────────────────────────────────────────
    private function guard()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'creator') {
            return redirect()->to(base_url('creator/login'));
        }
        return null;
    }

    // ─── Shared: build history rows ──────────────────────────────────────────────
    private function buildHistory($userId, array $filters = []): array
    {
        $userId = (string)$userId;
        $transactionModel = new \App\Models\TransactionModel();
        $builder = $transactionModel->where('user_id', $userId)->orderBy('created_at', 'DESC');

        if (!empty($filters['type']) && in_array($filters['type'], ['in', 'out'])) {
            $builder->where('type', $filters['type']);
        }
        if (!empty($filters['category'])) {
            $builder->where('category', $filters['category']);
        }
        if (!empty($filters['date_from'])) {
            $builder->where('created_at >=', $filters['date_from'] . ' 00:00:00');
        }
        if (!empty($filters['date_to'])) {
            $builder->where('created_at <=', $filters['date_to'] . ' 23:59:59');
        }

        $transactions = $builder->findAll();

        $history = [];
        foreach ($transactions as $idx => $tr) {
            $desc = $tr['description'] ?? '';
            if ($tr['type'] === 'out') {
                $desc = preg_replace('/^(Download karya|Buka karya|Buka bab)\s+(@\S+|user_id:\d+):\s*/u', '$1: ', $desc);
            }
            $history[] = [
                'id'          => $tr['id'],
                'no'          => $idx + 1,
                'date'        => $tr['created_at'],
                'amount'      => $tr['amount'],
                'amount_rp'   => $tr['amount'] * 10,
                'type'        => $tr['type'],
                'category'    => $tr['category'],
                'description' => $desc,
                'status'      => 'Selesai',
                'ref_id'      => $tr['reference_id'] ?? null,
            ];
        }
        return $history;
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // GET /creator/monetization  (dashboard)
    // ─────────────────────────────────────────────────────────────────────────────
    public function index()
    {
        if ($r = $this->guard()) return $r;

        $userId   = session()->get('userId');
        $username = session()->get('username');

        $userModel          = new UserModel();
        $creatorProfileModel = new CreatorProfileModel();
        $creditModel        = new CreditModel();

        $user           = $userModel->find($userId);
        $creatorProfile = $creatorProfileModel->find($userId);
        $credit         = $creditModel->find($userId);
        $balance        = $credit['balance'] ?? 0;

        // Latest 5 transactions for dashboard preview
        $transactionModel = new \App\Models\TransactionModel();
        $transactions = $transactionModel->where('user_id', $userId)
                                         ->orderBy('created_at', 'DESC')
                                         ->findAll(5);

        $history = [];
        foreach ($transactions as $tr) {
            $desc = $tr['description'] ?? '';
            if ($tr['type'] === 'out') {
                $desc = preg_replace('/^(Download karya|Buka karya|Buka bab)\s+(@\S+|user_id:\d+):\s*/u', '$1: ', $desc);
            }
            $history[] = [
                'id'          => $tr['id'],
                'date'        => $tr['created_at'],
                'amount'      => $tr['amount'],
                'type'        => $tr['type'],
                'category'    => $tr['category'],
                'description' => $desc,
                'status'      => 'Selesai',
            ];
        }

        // Monthly earnings
        $startOfMonth = date('Y-m-01 00:00:00');
        $monthlyTransactions = $transactionModel->where('user_id', $userId)
                                                ->where('type', 'in')
                                                ->where('created_at >=', $startOfMonth)
                                                ->findAll();

        $totalMonthlyCC = 0;
        $breakdown = [];
        foreach ($monthlyTransactions as $mtr) {
            $totalMonthlyCC += $mtr['amount'];
            $catLabel = ucfirst($mtr['category']);
            $breakdown[$catLabel] = ($breakdown[$catLabel] ?? 0) + ($mtr['amount'] * 10);
        }

        $monthlyEarnings = [
            'total'    => $totalMonthlyCC * 10,
            'total_cc' => $totalMonthlyCC,
            'growth'   => 0,
            'breakdown'=> $breakdown ?: ['Belum ada pendapatan' => 0],
        ];

        return view('creator/monetization/index', [
            'title'          => 'Monetisasi Kreator - NusaShare',
            'username'       => $username,
            'user'           => $user,
            'creatorProfile' => $creatorProfile,
            'balance'        => $balance,
            'history'        => $history,
            'monthlyEarnings'=> $monthlyEarnings,
            'activePage'     => 'monetization',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // POST /creator/monetization/withdraw
    // ─────────────────────────────────────────────────────────────────────────────
    public function withdraw()
    {
        if ($r = $this->guard()) return $r;

        $userId = session()->get('userId');
        $amount = (int) $this->request->getPost('amount');

        if ($amount <= 0) {
            return redirect()->back()->with('error', 'Jumlah penarikan tidak valid.');
        }

        $creditModel = new CreditModel();
        $credit = $creditModel->find($userId);
        $balance = $credit['balance'] ?? 0;

        if ($amount > $balance) {
            return redirect()->back()->with('error', 'Saldo tidak mencukupi.');
        }

        // Deduct balance
        $creditModel->update($userId, ['balance' => $balance - $amount]);

        // Add transaction record
        $transactionModel = new \App\Models\TransactionModel();
        $transactionModel->insert([
            'user_id'      => $userId,
            'type'         => 'out',
            'category'     => 'withdraw',
            'amount'       => $amount,
            'description'  => 'Penarikan saldo kreator',
            'reference_id' => 'WD-' . time() . '-' . rand(100, 999)
        ]);

        return redirect()->back()->with('success', 'Penarikan saldo sebesar ' . number_format($amount) . ' CC berhasil diproses (Dummy).');
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // GET /creator/monetization/history
    // ─────────────────────────────────────────────────────────────────────────────
    public function history()
    {
        if ($r = $this->guard()) return $r;

        $userId   = session()->get('userId');
        $username = session()->get('username');

        $userModel          = new UserModel();
        $creatorProfileModel = new CreatorProfileModel();
        $creditModel        = new CreditModel();

        $user           = $userModel->find($userId);
        $creatorProfile = $creatorProfileModel->find($userId);
        $credit         = $creditModel->find($userId);
        $balance        = $credit['balance'] ?? 0;

        // Read filters from GET
        $filters = [
            'type'      => $this->request->getGet('type')      ?? '',
            'category'  => $this->request->getGet('category')  ?? '',
            'date_from' => $this->request->getGet('date_from') ?? '',
            'date_to'   => $this->request->getGet('date_to')   ?? '',
        ];

        $history = $this->buildHistory($userId, $filters);

        // Summary stats for the filtered set
        $totalIn  = array_sum(array_column(array_filter($history, fn($r) => $r['type'] === 'in'),  'amount'));
        $totalOut = array_sum(array_column(array_filter($history, fn($r) => $r['type'] === 'out'), 'amount'));

        return view('creator/monetization/history', [
            'title'          => 'Riwayat Transaksi - NusaShare',
            'username'       => $username,
            'user'           => $user,
            'creatorProfile' => $creatorProfile,
            'balance'        => $balance,
            'history'        => $history,
            'filters'        => $filters,
            'totalIn'        => $totalIn,
            'totalOut'       => $totalOut,
            'activePage'     => 'monetization',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // GET /creator/monetization/export/excel
    // ─────────────────────────────────────────────────────────────────────────────
    public function exportExcel()
    {
        if ($r = $this->guard()) return $r;

        $userId   = session()->get('userId');
        $username = session()->get('username');
        $filters = [
            'type'      => $this->request->getGet('type')      ?? '',
            'category'  => $this->request->getGet('category')  ?? '',
            'date_from' => $this->request->getGet('date_from') ?? '',
            'date_to'   => $this->request->getGet('date_to')   ?? '',
        ];

        $history = $this->buildHistory($userId, $filters);
        $totalIn = array_sum(array_column(array_filter($history, fn($r) => $r['type'] === 'in'), 'amount'));
        $totalOut = array_sum(array_column(array_filter($history, fn($r) => $r['type'] === 'out'), 'amount'));
        $netCC = $totalIn - $totalOut;

        $dateLabel = (!empty($filters['date_from']) || !empty($filters['date_to']))
            ? (($filters['date_from'] ?: 'Awal') . ' s.d. ' . ($filters['date_to'] ?: 'Akhir'))
            : 'Semua waktu';
        $typeLabel = empty($filters['type'])
            ? 'Semua'
            : ($filters['type'] === 'in' ? 'Masuk' : 'Keluar');
        $categoryLabel = empty($filters['category']) ? 'Semua' : ucfirst($filters['category']);

        $rows = '';
        foreach ($history as $row) {
            $isIn = $row['type'] === 'in';
            $desc = htmlspecialchars($row['description'] ?: '-', ENT_QUOTES, 'UTF-8');
            $category = htmlspecialchars(ucfirst((string) $row['category']), ENT_QUOTES, 'UTF-8');
            $ref = htmlspecialchars($row['ref_id'] ? (string) $row['ref_id'] : '-', ENT_QUOTES, 'UTF-8');
            $status = htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8');

            $rows .= '<tr>
                <td class="center">' . (int) $row['no'] . '</td>
                <td>' . date('d/m/Y', strtotime($row['date'])) . '</td>
                <td>' . date('H:i', strtotime($row['date'])) . ' WIB</td>
                <td>' . $category . '</td>
                <td>' . $desc . '</td>
                <td class="text">' . $ref . '</td>
                <td class="money in">' . ($isIn ? (int) $row['amount'] : 0) . '</td>
                <td class="money out">' . (!$isIn ? (int) $row['amount'] : 0) . '</td>
                <td class="money in">' . ($isIn ? (int) $row['amount_rp'] : 0) . '</td>
                <td class="money out">' . (!$isIn ? (int) $row['amount_rp'] : 0) . '</td>
                <td class="center">' . ($isIn ? 'Masuk' : 'Keluar') . '</td>
                <td class="center">' . $status . '</td>
            </tr>';
        }

        if ($rows === '') {
            $rows = '<tr><td colspan="12" class="empty">Tidak ada transaksi pada filter ini.</td></tr>';
        }

        $safeUsername = preg_replace('/[^A-Za-z0-9_-]+/', '_', (string) $username);
        $filename = 'NusaShare_Laporan_Transaksi_' . $safeUsername . '_' . date('Ymd_His') . '.xls';

        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; color: #111827; }
                .title { font-size: 20px; font-weight: 700; color: #111827; }
                .subtitle { font-size: 11px; color: #475569; }
                .meta-table td { border: 1px solid #94a3b8; padding: 6px 8px; font-size: 11px; }
                .meta-label { background: #e2e8f0; font-weight: 700; color: #334155; }
                table.report { border-collapse: collapse; width: 100%; margin-top: 14px; }
                table.report th { border: 1px solid #334155; background: #1e293b; color: #ffffff; font-size: 11px; font-weight: 700; padding: 8px; text-align: center; }
                table.report td { border: 1px solid #94a3b8; padding: 7px 8px; font-size: 11px; vertical-align: top; }
                table.report tr:nth-child(even) td { background: #f8fafc; }
                .center { text-align: center; }
                .money { text-align: right; mso-number-format: "#,##0"; }
                .text { mso-number-format: "\@"; }
                .in { color: #047857; }
                .out { color: #be123c; }
                .empty { text-align: center; color: #64748b; font-style: italic; }
                .summary-label { background: #e2e8f0; font-weight: 700; }
                .summary-value { background: #f8fafc; font-weight: 700; text-align: right; mso-number-format: "#,##0"; }
            </style>
        </head>
        <body>
            <table width="100%">
                <tr><td class="title" colspan="12">Laporan Riwayat Transaksi Kreator</td></tr>
                <tr><td class="subtitle" colspan="12">NusaShare - @' . htmlspecialchars((string) $username, ENT_QUOTES, 'UTF-8') . ' - Dicetak ' . date('d/m/Y H:i') . ' WIB</td></tr>
            </table>
            <br>
            <table class="meta-table">
                <tr>
                    <td class="meta-label">Periode</td><td>' . htmlspecialchars($dateLabel, ENT_QUOTES, 'UTF-8') . '</td>
                    <td class="meta-label">Tipe</td><td>' . htmlspecialchars($typeLabel, ENT_QUOTES, 'UTF-8') . '</td>
                    <td class="meta-label">Kategori</td><td>' . htmlspecialchars($categoryLabel, ENT_QUOTES, 'UTF-8') . '</td>
                    <td class="meta-label">Total Transaksi</td><td>' . count($history) . '</td>
                </tr>
            </table>
            <table class="report">
                <thead>
                    <tr>
                        <th width="42">No</th>
                        <th width="90">Tanggal</th>
                        <th width="70">Waktu</th>
                        <th width="100">Kategori</th>
                        <th width="280">Keterangan</th>
                        <th width="110">Referensi</th>
                        <th width="90">Masuk (CC)</th>
                        <th width="90">Keluar (CC)</th>
                        <th width="110">Masuk (Rp)</th>
                        <th width="110">Keluar (Rp)</th>
                        <th width="70">Tipe</th>
                        <th width="80">Status</th>
                    </tr>
                </thead>
                <tbody>' . $rows . '</tbody>
                <tfoot>
                    <tr>
                        <td class="summary-label" colspan="6">TOTAL</td>
                        <td class="summary-value in">' . (int) $totalIn . '</td>
                        <td class="summary-value out">' . (int) $totalOut . '</td>
                        <td class="summary-value in">' . (int) ($totalIn * 10) . '</td>
                        <td class="summary-value out">' . (int) ($totalOut * 10) . '</td>
                        <td class="summary-label" colspan="2"></td>
                    </tr>
                    <tr>
                        <td class="summary-label" colspan="6">SELISIH BERSIH</td>
                        <td class="summary-value" colspan="2">' . (int) $netCC . '</td>
                        <td class="summary-value" colspan="2">' . (int) ($netCC * 10) . '</td>
                        <td class="summary-label" colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </body>
        </html>';

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($html);
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // GET /creator/monetization/export/pdf
    // ─────────────────────────────────────────────────────────────────────────────
    public function exportPdf(bool $inline = false)
    {
        if ($r = $this->guard()) return $r;

        $userId   = session()->get('userId');
        $username = session()->get('username');
        $filters  = [
            'type'      => $this->request->getGet('type')      ?? '',
            'category'  => $this->request->getGet('category')  ?? '',
            'date_from' => $this->request->getGet('date_from') ?? '',
            'date_to'   => $this->request->getGet('date_to')   ?? '',
        ];

        $history   = $this->buildHistory($userId, $filters);
        $totalIn   = array_sum(array_column(array_filter($history, fn($r) => $r['type'] === 'in'),  'amount'));
        $totalOut  = array_sum(array_column(array_filter($history, fn($r) => $r['type'] === 'out'), 'amount'));
        $netCC     = $totalIn - $totalOut;

        $dateLabel = '';
        if (!empty($filters['date_from']) || !empty($filters['date_to'])) {
            $dateLabel = ($filters['date_from'] ?? '?') . ' s.d. ' . ($filters['date_to'] ?? '?');
        } else {
            $dateLabel = 'Semua waktu';
        }

        $rows = '';
        foreach ($history as $row) {
            $typeColor = $row['type'] === 'in' ? '#059669' : '#e11d48';
            $typeBg    = $row['type'] === 'in' ? '#ecfdf5'  : '#fff1f2';
            $typeLabel = $row['type'] === 'in' ? 'Masuk'    : 'Keluar';
            $sign      = $row['type'] === 'in' ? '+'        : '-';
            $desc      = htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8');
            $category  = htmlspecialchars(ucfirst((string)$row['category']), ENT_QUOTES, 'UTF-8');
            $rows .= "
            <tr>
                <td class='cell muted'>" . $row['no'] . "</td>
                <td class='cell'>
                    <strong style='color:#0f172a;'>" . date('d M Y', strtotime($row['date'])) . "</strong><br>
                    <span style='color:#94a3b8;font-size:10px;'>" . date('H:i', strtotime($row['date'])) . " WIB</span>
                </td>
                <td class='cell'>" . $category . "</td>
                <td class='cell desc'>" . $desc . "</td>
                <td class='cell amount'>
                    <strong style='color:{$typeColor};'>{$sign}" . number_format($row['amount']) . " CC</strong><br>
                    <span style='color:#94a3b8;font-size:10px;'>Rp " . number_format($row['amount_rp']) . "</span>
                </td>
                <td class='cell center'>
                    <span style='background:{$typeBg};color:{$typeColor};padding:4px 10px;border-radius:14px;font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.5px;'>{$typeLabel}</span>
                </td>
            </tr>";
        }

        if ($rows === '') {
            $rows = '<tr><td colspan="6" class="empty-row">Tidak ada transaksi untuk filter ini.</td></tr>';
        }

        $creditModel = new \App\Models\CreditModel();
        $credit = $creditModel->where('user_id', $userId)->first();
        $balance = $credit ? (int)$credit['balance'] : 0;

        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8">
        <style>
            body { font-family: "DejaVu Sans", Arial, sans-serif; margin:0; padding:0; font-size:12px; color:#1e293b; }
            .header { background: #4f46e5; color: white; padding: 28px 36px; }
            .header h1 { margin:0 0 4px; font-size:22px; font-weight:900; }
            .header p { margin:0; opacity:.75; font-size:12px; }
            .meta { padding:20px 36px; background:#f8fafc; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; }
            .meta-item label { display:block; font-size:10px; color:#94a3b8; text-transform:uppercase; letter-spacing:.08em; font-weight:700; }
            .meta-item strong { font-size:15px; color:#0f172a; }
            table { width:100%; border-collapse:collapse; }
            thead tr { background:#f1f5f9; }
            thead th { padding:10px 12px; text-align:left; font-size:10px; color:#64748b; text-transform:uppercase; letter-spacing:.05em; font-weight:700; border-bottom:2px solid #e2e8f0; }
            .summary { padding:20px 36px; background:#f8fafc; border-top:2px solid #e2e8f0; display:flex; gap:16px; flex-wrap: wrap; }
            .sum-box { background:white; border-radius:10px; padding:14px 20px; flex:1; min-width: 150px; border:1px solid #e2e8f0; }
            .sum-box label { display:block; font-size:10px; color:#94a3b8; font-weight:700; text-transform:uppercase; }
            .footer { padding:16px 36px; text-align:center; font-size:10px; color:#94a3b8; border-top:1px solid #f1f5f9; }
            .cell { padding:10px 12px; border-bottom:1px solid #e5e7eb; font-size:10.5px; color:#334155; vertical-align:top; }
            .muted { color:#64748b; width:36px; }
            .desc { max-width:240px; line-height:1.45; }
            .amount { text-align:right; white-space:nowrap; }
            .center { text-align:center; }
            tbody tr:nth-child(even) { background:#fbfdff; }
            .empty-row { padding:24px; text-align:center; color:#64748b; border-bottom:1px solid #e5e7eb; }
        </style></head><body>
        <div class="header">
            <h1>Laporan Riwayat Transaksi</h1>
            <p>NusaShare · @' . htmlspecialchars($username) . ' · Dicetak: ' . date('d M Y H:i') . ' WIB</p>
        </div>
        <div class="meta">
            <div class="meta-item"><label>Periode</label><strong>' . $dateLabel . '</strong></div>
            <div class="meta-item"><label>Total Transaksi</label><strong>' . count($history) . '</strong></div>
            <div class="meta-item"><label>Tipe Filter</label><strong>' . (empty($filters['type']) ? 'Semua' : ucfirst($filters['type'] === 'in' ? 'Masuk' : 'Keluar')) . '</strong></div>
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width:36px">#</th>
                    <th>Waktu</th>
                    <th>Kategori</th>
                    <th>Keterangan</th>
                    <th style="text-align:right">Jumlah</th>
                    <th style="text-align:center">Tipe</th>
                </tr>
            </thead>
            <tbody>' . $rows . '</tbody>
        </table>
        <div class="summary">
            <div class="sum-box"><label>Total Masuk</label><strong style="color:#059669;font-size:16px;">+' . number_format($totalIn) . ' CC</strong><br><span style="color:#94a3b8;font-size:11px;">Rp ' . number_format($totalIn * 10) . '</span></div>
            <div class="sum-box"><label>Total Keluar</label><strong style="color:#e11d48;font-size:16px;">-' . number_format($totalOut) . ' CC</strong><br><span style="color:#94a3b8;font-size:11px;">Rp ' . number_format($totalOut * 10) . '</span></div>
            <div class="sum-box"><label>Selisih Bersih</label><strong style="color:' . ($netCC >= 0 ? '#4f46e5' : '#e11d48') . ';font-size:16px;">' . ($netCC >= 0 ? '+' : '') . number_format($netCC) . ' CC</strong><br><span style="color:#94a3b8;font-size:11px;">Rp ' . number_format(abs($netCC) * 10) . '</span></div>
            <div class="sum-box"><label>Saldo Akhir</label><strong style="color:#0f172a;font-size:16px;">' . number_format($balance) . ' CC</strong><br><span style="color:#94a3b8;font-size:11px;">Rp ' . number_format($balance * 10) . '</span></div>
        </div>
        <div class="footer">Dokumen ini digenerate otomatis oleh NusaShare &copy; ' . date('Y') . ' &mdash; platform kreator Indonesia</div>
        </body></html>';

        // Render with DomPDF
        $options = new \Dompdf\Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $canvas = $dompdf->getCanvas();
        $fontMetrics = $dompdf->getFontMetrics();
        $footerFont = $fontMetrics->getFont('DejaVu Sans', 'normal');
        $canvas->page_text(42, 570, 'NusaShare Creator Finance', $footerFont, 8, [100, 116, 139]);
        $canvas->page_text(745, 570, 'Hal. {PAGE_NUM} / {PAGE_COUNT}', $footerFont, 8, [100, 116, 139]);
        $pdf = $dompdf->output();

        $filename = 'NusaShare_Laporan_' . $username . '_' . date('Ymd_His') . '.pdf';

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', ($inline ? 'inline' : 'attachment') . '; filename="' . $filename . '"')
            ->setHeader('Content-Length', (string) strlen($pdf))
            ->setBody($pdf);
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // GET /creator/monetization/report/print
    // Opens the same filtered PDF report in the browser for printing.
    // ─────────────────────────────────────────────────────────────────────────────
    public function printReport()
    {
        return $this->exportPdf(true);
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // GET /creator/monetization/receipt/:id
    // ─────────────────────────────────────────────────────────────────────────────
    public function receipt($txId)
    {
        $txId = (int)$txId;
        if ($r = $this->guard()) return $r;

        $userId   = session()->get('userId');
        $username = session()->get('username');

        $transactionModel = new \App\Models\TransactionModel();
        $tx = $transactionModel->where('id', $txId)->where('user_id', $userId)->first();

        if (!$tx) {
            return $this->response->setStatusCode(404)->setBody('Transaksi tidak ditemukan.');
        }

        $isIn      = $tx['type'] === 'in';
        $typeLabel = $isIn ? 'MASUK' : 'KELUAR';
        $typeColor = $isIn ? '#059669' : '#e11d48';
        $typeBg    = $isIn ? '#ecfdf5' : '#fff1f2';
        $typeBorder= $isIn ? '#6ee7b7' : '#fecdd3';
        $sign      = $isIn ? '+' : '-';
        $catLabel  = str_replace('_', ' ', ucwords(str_replace('_', ' ', $tx['category'] ?? '-')));
        $rawDesc   = $tx['description'] ?? '-';
        if (!$isIn) {
            $rawDesc = preg_replace('/^(Download karya|Buka karya|Buka bab)\s+(@\S+|user_id:\d+):\s*/u', '$1: ', $rawDesc);
        }
        $desc   = htmlspecialchars($rawDesc, ENT_QUOTES, 'UTF-8');
        $txCode = 'NST-' . str_pad($txId, 8, '0', STR_PAD_LEFT);
        $rpEquiv= number_format($tx['amount'] * 10, 0, ',', '.');

        ob_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk <?= $txCode ?> — NusaShare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        /* ─── Reset ─── */
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        /* ─── Page ─── */
        html, body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: #EEF2FF;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* ─── Card ─── */
        .receipt-card {
            background: #fff;
            width: 100%;
            max-width: 360px;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 24px 64px rgba(79, 70, 229, 0.18), 0 4px 16px rgba(0,0,0,.08);
        }

        /* ─── Header ─── */
        .receipt-header {
            background: linear-gradient(135deg, #6366F1 0%, #4F46E5 60%, #4338CA 100%);
            padding: 28px 28px 22px;
            text-align: center;
            position: relative;
        }
        .receipt-header::after {
            content: '';
            position: absolute;
            bottom: -12px;
            left: 50%;
            transform: translateX(-50%);
            width: 24px;
            height: 24px;
            background: #EEF2FF;
            border-radius: 50%;
            box-shadow: -180px 0 0 #EEF2FF, 180px 0 0 #EEF2FF;
        }
        .brand-name {
            font-size: 22px;
            font-weight: 900;
            color: #fff;
            letter-spacing: -0.5px;
        }
        .brand-sub {
            font-size: 11px;
            color: rgba(255,255,255,0.65);
            margin-top: 4px;
            font-weight: 500;
            letter-spacing: 0.04em;
        }

        /* ─── Amount block ─── */
        .amount-block {
            padding: 30px 28px 20px;
            text-align: center;
            border-bottom: 2px dashed #E2E8F0;
        }
        .amount-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #94A3B8;
        }
        .amount-cc {
            font-size: 44px;
            font-weight: 900;
            letter-spacing: -1px;
            color: <?= $typeColor ?>;
            margin: 10px 0 4px;
            line-height: 1;
        }
        .amount-rp {
            font-size: 13px;
            color: #94A3B8;
            font-weight: 500;
        }
        .type-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: <?= $typeBg ?>;
            color: <?= $typeColor ?>;
            border: 1px solid <?= $typeBorder ?>;
            border-radius: 99px;
            padding: 5px 14px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-top: 14px;
        }
        .type-badge::before {
            content: '<?= $isIn ? '↓' : '↑' ?>';
            font-size: 12px;
        }

        /* ─── Detail rows ─── */
        .detail-rows {
            padding: 8px 28px 4px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            padding: 11px 0;
            border-bottom: 1px solid #F8FAFC;
        }
        .detail-row:last-child { border-bottom: none; }
        .row-key {
            font-size: 11px;
            font-weight: 600;
            color: #94A3B8;
            flex-shrink: 0;
            padding-top: 1px;
        }
        .row-val {
            font-size: 12px;
            font-weight: 700;
            color: #0F172A;
            text-align: right;
            word-break: break-word;
            max-width: 200px;
        }
        .row-val.mono { font-family: 'Courier New', monospace; font-size: 11px; }
        .row-val.success { color: #059669; }
        .row-val.danger  { color: #e11d48; }

        /* ─── Divider ─── */
        .dashed-divider {
            border: none;
            border-top: 2px dashed #E2E8F0;
            margin: 0 28px;
        }

        /* ─── Print button ─── */
        .print-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: calc(100% - 56px);
            margin: 20px 28px 4px;
            padding: 14px 20px;
            background: linear-gradient(135deg, #6366F1, #4F46E5);
            color: #fff;
            border: none;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: opacity 0.15s;
            letter-spacing: -0.01em;
        }
        .print-btn:hover { opacity: 0.9; }
        .print-btn svg { width: 18px; height: 18px; flex-shrink: 0; }

        .save-pdf-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: calc(100% - 56px);
            margin: 8px 28px 0;
            padding: 11px 20px;
            background: transparent;
            color: #6366F1;
            border: 1.5px solid #C7D2FE;
            border-radius: 14px;
            font-size: 13px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.15s;
        }
        .save-pdf-btn:hover { background: #EEF2FF; }

        /* ─── Footer ─── */
        .receipt-footer {
            padding: 16px 28px 20px;
            text-align: center;
            font-size: 10px;
            color: #94A3B8;
            font-weight: 500;
        }

        /* ─── Watermark / verified ─── */
        .verified-stamp {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #F0FDF4;
            border: 1px solid #BBF7D0;
            border-radius: 6px;
            padding: 3px 8px;
            font-size: 9px;
            font-weight: 700;
            color: #16A34A;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        /* ─── @media print ─── */
        @media print {
            @page {
                size: 400px 640px;
                margin: 0;
            }
            html, body {
                background: #fff !important;
                padding: 0 !important;
                min-height: auto !important;
            }
            .receipt-card {
                box-shadow: none !important;
                border-radius: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
            }
            .print-btn,
            .save-pdf-btn { display: none !important; }
            .receipt-footer { padding-bottom: 8px; }
        }
    </style>
</head>
<body>

<div class="receipt-card" id="receipt">

    <!-- Header -->
    <div class="receipt-header">
        <div class="brand-name">NusaShare</div>
        <div class="brand-sub">Struk Transaksi Digital</div>
    </div>

    <!-- Amount -->
    <div class="amount-block">
        <div class="amount-label">Jumlah</div>
        <div class="amount-cc"><?= $sign . number_format($tx['amount']) ?> CC</div>
        <div class="amount-rp">≈ Rp <?= $rpEquiv ?></div>
        <div class="type-badge"><?= $typeLabel ?> &mdash; <?= htmlspecialchars($catLabel) ?></div>
    </div>

    <!-- Detail Rows -->
    <div class="detail-rows">
        <div class="detail-row">
            <span class="row-key">No. Transaksi</span>
            <span class="row-val mono"><?= $txCode ?></span>
        </div>
        <div class="detail-row">
            <span class="row-key">Tanggal</span>
            <span class="row-val"><?= date('d M Y', strtotime($tx['created_at'])) ?></span>
        </div>
        <div class="detail-row">
            <span class="row-key">Waktu</span>
            <span class="row-val"><?= date('H:i:s', strtotime($tx['created_at'])) ?> WIB</span>
        </div>
        <div class="detail-row">
            <span class="row-key">Kategori</span>
            <span class="row-val"><?= htmlspecialchars($catLabel) ?></span>
        </div>
        <div class="detail-row">
            <span class="row-key">Keterangan</span>
            <span class="row-val"><?= $desc ?></span>
        </div>
        <div class="detail-row">
            <span class="row-key">Status</span>
            <span class="row-val success">&#10003; Selesai</span>
        </div>
        <div class="detail-row">
            <span class="row-key">Akun</span>
            <span class="row-val">@<?= htmlspecialchars($username) ?></span>
        </div>
    </div>

    <hr class="dashed-divider">

    <!-- Buttons -->
    <button class="print-btn" onclick="window.print()">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
        Cetak Struk
    </button>

    <button class="save-pdf-btn" onclick="savePdf()">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Simpan sebagai PDF
    </button>

    <!-- Footer -->
    <div class="receipt-footer">
        <div class="verified-stamp">&#10003; Terverifikasi NusaShare</div><br>
        Struk ini adalah bukti transaksi digital yang sah &bull; NusaShare &copy; <?= date('Y') ?>
    </div>

</div>

<script>
    function savePdf() {
        // Set document title to transaction code for better PDF filename
        const origTitle = document.title;
        document.title = '<?= $txCode ?>_NusaShare';
        window.print();
        document.title = origTitle;
    }
</script>

</body>
</html>
<?php
        $html = ob_get_clean();

        return $this->response
            ->setHeader('Content-Type', 'text/html; charset=UTF-8')
            ->setBody($html);
    }
}
