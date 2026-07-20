<?php
// PHP data variables passed from AdminController:
// $totalUsers, $totalCreators, $totalWorks, $totalCC
// $monthlyRevenue, $newUsersMonth, $userGrowthPct, $releasesWeek
// $recentTrx, $topCreators, $worksBreakdown
// $regChartLabels, $regChartData, $topupChartLabels, $topupChartData
?>

<!-- ══════════════════════════════════════
     STAT CARDS ROW 1  (Small Boxes)
═══════════════════════════════════════ -->
<div class="row g-3 mb-4">

    <!-- Total Users -->
    <div class="col-6 col-md-4 col-xl-2">
        <div class="small-box" style="background: linear-gradient(135deg, #6366F1 0%, #818CF8 100%);">
            <div class="inner text-white px-3 pt-3 pb-2">
                <h3 class="mb-0 fw-800"><?= number_format($totalUsers ?? 0) ?></h3>
                <p class="mb-0 opacity-90">Total Pembaca</p>
            </div>
            <div class="icon text-white opacity-25">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="small-box-footer px-3 pb-2" style="background:rgba(0,0,0,0.10);">
                <?php if (($userGrowthPct ?? 0) >= 0): ?>
                    <small class="text-white opacity-90">
                        <i class="bi bi-arrow-up-short"></i>+<?= $userGrowthPct ?? 0 ?>% bulan ini
                    </small>
                <?php else: ?>
                    <small class="text-white opacity-90">
                        <i class="bi bi-arrow-down-short"></i><?= $userGrowthPct ?? 0 ?>% bulan ini
                    </small>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Total Kreator -->
    <div class="col-6 col-md-4 col-xl-2">
        <div class="small-box" style="background: linear-gradient(135deg, #06B6D4 0%, #38BDF8 100%);">
            <div class="inner text-white px-3 pt-3 pb-2">
                <h3 class="mb-0 fw-800"><?= number_format($totalCreators ?? 0) ?></h3>
                <p class="mb-0 opacity-90">Kreator Aktif</p>
            </div>
            <div class="icon text-white opacity-25">
                <i class="bi bi-person-badge-fill"></i>
            </div>
            <div class="small-box-footer px-3 pb-2" style="background:rgba(0,0,0,0.10);">
                <small class="text-white opacity-90">Terverifikasi platform</small>
            </div>
        </div>
    </div>

    <!-- Revenue Bulan Ini -->
    <div class="col-6 col-md-4 col-xl-2">
        <div class="small-box" style="background: linear-gradient(135deg, #10B981 0%, #34D399 100%);">
            <div class="inner text-white px-3 pt-3 pb-2">
                <h3 class="mb-0 fw-800" style="font-size:1.5rem;"><?= number_format($monthlyRevenue ?? 0) ?></h3>
                <p class="mb-0 opacity-90">Revenue (CC)</p>
            </div>
            <div class="icon text-white opacity-25">
                <i class="bi bi-coin"></i>
            </div>
            <div class="small-box-footer px-3 pb-2" style="background:rgba(0,0,0,0.10);">
                <small class="text-white opacity-90">Top-up & pembelian CC</small>
            </div>
        </div>
    </div>

    <!-- Total CC Beredar -->
    <div class="col-6 col-md-4 col-xl-2">
        <div class="small-box" style="background: linear-gradient(135deg, #8B5CF6 0%, #A78BFA 100%);">
            <div class="inner text-white px-3 pt-3 pb-2">
                <h3 class="mb-0 fw-800" style="font-size:1.4rem;"><?= number_format($totalCC ?? 0) ?></h3>
                <p class="mb-0 opacity-90">CC Beredar</p>
            </div>
            <div class="icon text-white opacity-25">
                <i class="bi bi-database-fill"></i>
            </div>
            <div class="small-box-footer px-3 pb-2" style="background:rgba(0,0,0,0.10);">
                <small class="text-white opacity-90">Di semua dompet user</small>
            </div>
        </div>
    </div>

    <!-- Karya Terbit -->
    <div class="col-6 col-md-4 col-xl-2">
        <div class="small-box" style="background: linear-gradient(135deg, #F59E0B 0%, #FCD34D 100%);">
            <div class="inner text-white px-3 pt-3 pb-2">
                <h3 class="mb-0 fw-800"><?= number_format($totalWorks ?? 0) ?></h3>
                <p class="mb-0 opacity-90">Karya Terbit</p>
            </div>
            <div class="icon text-white opacity-25">
                <i class="bi bi-book-fill"></i>
            </div>
            <div class="small-box-footer px-3 pb-2" style="background:rgba(0,0,0,0.10);">
                <small class="text-white opacity-90">Novel, Manga & Ilustrasi</small>
            </div>
        </div>
    </div>

    <!-- Bab Rilis Minggu Ini -->
    <div class="col-6 col-md-4 col-xl-2">
        <div class="small-box" style="background: linear-gradient(135deg, #EF4444 0%, #F87171 100%);">
            <div class="inner text-white px-3 pt-3 pb-2">
                <h3 class="mb-0 fw-800"><?= number_format($releasesWeek ?? 0) ?></h3>
                <p class="mb-0 opacity-90">Rilis Minggu Ini</p>
            </div>
            <div class="icon text-white opacity-25">
                <i class="bi bi-newspaper"></i>
            </div>
            <div class="small-box-footer px-3 pb-2" style="background:rgba(0,0,0,0.10);">
                <small class="text-white opacity-90">Bab & aset baru terbit</small>
            </div>
        </div>
    </div>

</div>

<!-- ══════════════════════════════════════
     STAT CARDS ROW 2  (Info Boxes - dynamic Alpine)
═══════════════════════════════════════ -->
<div class="row g-3 mb-4">

    <!-- Laporan Pending (Alpine dynamic) -->
    <div class="col-md-4">
        <div class="info-box mb-0"
             :style="getPendingReportsCount() > 0 ? 'border-left: 4px solid #EF4444;' : ''">
            <span class="info-box-icon" style="background:rgba(239,68,68,0.10); color:#EF4444; border-radius:10px; width:52px; height:52px;">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text fw-semibold">Laporan Pending</span>
                <span class="info-box-number fw-bold" x-text="getPendingReportsCount()"></span>
                <div class="progress" style="height:3px;">
                    <div class="progress-bar bg-danger" style="width:100%"></div>
                </div>
                <span class="progress-description text-muted" style="font-size:11px;">Menunggu moderasi</span>
            </div>
            <button class="btn btn-sm ms-auto align-self-center me-2" @click="setTab('reports')"
                    style="font-size:11px; color:var(--ns-primary); border:none; background:none;">
                Lihat →
            </button>
        </div>
    </div>

    <!-- Top Up Pending (Alpine dynamic) -->
    <div class="col-md-4">
        <div class="info-box mb-0"
             :style="getPendingTopupsCount() > 0 ? 'border-left: 4px solid #F59E0B;' : ''">
            <span class="info-box-icon" style="background:rgba(245,158,11,0.10); color:#F59E0B; border-radius:10px; width:52px; height:52px;">
                <i class="bi bi-credit-card-fill"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text fw-semibold">Top Up Pending</span>
                <span class="info-box-number fw-bold" x-text="getPendingTopupsCount()"></span>
                <div class="progress" style="height:3px;">
                    <div class="progress-bar bg-warning" style="width:100%"></div>
                </div>
                <span class="progress-description text-muted" style="font-size:11px;">Verifikasi manual</span>
            </div>
            <button class="btn btn-sm ms-auto align-self-center me-2" @click="setTab('topup')"
                    style="font-size:11px; color:var(--ns-primary); border:none; background:none;">
                Lihat →
            </button>
        </div>
    </div>

    <!-- Tipe Konten Breakdown -->
    <div class="col-md-4">
        <div class="info-box mb-0" style="border-left: 4px solid var(--ns-accent);">
            <span class="info-box-icon" style="background:rgba(6,182,212,0.10); color:var(--ns-accent); border-radius:10px; width:52px; height:52px;">
                <i class="bi bi-pie-chart-fill"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text fw-semibold">Breakdown Konten</span>
                <div class="mt-1">
                    <?php foreach (($worksBreakdown ?? []) as $type => $count): ?>
                    <small class="d-flex justify-content-between" style="font-size:11px;">
                        <span class="text-muted"><?= esc($type) ?></span>
                        <span class="fw-bold"><?= number_format($count) ?></span>
                    </small>
                    <?php endforeach; ?>
                    <?php if (empty($worksBreakdown)): ?>
                    <small class="text-muted">Belum ada konten</small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ══════════════════════════════════════
     CHARTS ROW
═══════════════════════════════════════ -->
<div class="row g-3 mb-4">

    <!-- Chart 1: Registrasi User Bulanan -->
    <div class="col-md-7">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="card-title mb-0 fw-bold" style="font-size:14px;">
                        <i class="bi bi-graph-up-arrow me-2" style="color:var(--ns-primary);"></i>
                        Tren Registrasi Pengguna Baru
                    </h5>
                    <small class="text-muted">6 bulan terakhir</small>
                </div>
                <span class="badge-ns-primary">Live Data</span>
            </div>
            <div class="card-body">
                <div style="height: 280px; position: relative;">
                    <canvas id="regChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart 2: Top-up CC Bulanan -->
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="card-title mb-0 fw-bold" style="font-size:14px;">
                        <i class="bi bi-bar-chart-fill me-2" style="color:var(--ns-accent);"></i>
                        Tren Top Up CC
                    </h5>
                    <small class="text-muted">6 bulan terakhir</small>
                </div>
                <span class="badge-ns-accent">CC Units</span>
            </div>
            <div class="card-body">
                <div style="height: 280px; position: relative;">
                    <canvas id="topupChart"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ══════════════════════════════════════
     RECENT TRX + TOP CREATORS
═══════════════════════════════════════ -->
<div class="row g-3">

    <!-- Recent Transactions -->
    <div class="col-md-7">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold" style="font-size:14px;">
                    <i class="bi bi-arrow-left-right me-2" style="color:var(--ns-primary);"></i>
                    Transaksi Terkini
                </h5>
                <button class="btn btn-sm" @click="setTab('transactions')"
                        style="font-size:11px; color:var(--ns-primary); border:1px solid var(--ns-primary); border-radius:6px; background:transparent; padding:3px 10px;">
                    Lihat Semua
                </button>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($recentTrx)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Deskripsi</th>
                                <th>User</th>
                                <th class="text-end pe-3">Jumlah</th>
                                <th class="text-end pe-3">Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentTrx as $trx): ?>
                            <tr>
                                <td class="ps-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0"
                                              style="width:30px; height:30px;
                                                     background:<?= $trx['type'] === 'in' ? 'rgba(16,185,129,0.10)' : 'rgba(239,68,68,0.10)' ?>;
                                                     color:<?= $trx['type'] === 'in' ? '#059669' : '#DC2626' ?>;">
                                            <i class="bi bi-<?= $trx['type'] === 'in' ? 'arrow-down-short' : 'arrow-up-short' ?>"></i>
                                        </span>
                                        <span class="fw-semibold" style="font-size:12px;">
                                            <?= esc($trx['description'] ?? ucfirst($trx['category'])) ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="text-muted" style="font-size:12px;">@<?= esc($trx['username'] ?? '—') ?></td>
                                <td class="text-end pe-3 fw-bold <?= $trx['type'] === 'in' ? 'text-success' : 'text-danger' ?>"
                                    style="font-size:12px;">
                                    <?= $trx['type'] === 'in' ? '+' : '-' ?><?= number_format($trx['amount']) ?> CC
                                </td>
                                <td class="text-end pe-3 text-muted" style="font-size:11px;">
                                    <?= date('d M, H:i', strtotime($trx['created_at'])) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="d-flex flex-column align-items-center justify-content-center py-5 text-muted">
                    <i class="bi bi-receipt mb-2" style="font-size:2rem;"></i>
                    <small>Belum ada transaksi</small>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Top Creators Leaderboard -->
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0 fw-bold" style="font-size:14px;">
                    <i class="bi bi-trophy-fill me-2" style="color:#F59E0B;"></i>
                    Papan Peringkat Kreator
                </h5>
                <small class="text-muted">Berdasarkan StarSoul Score</small>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($topCreators)): ?>
                <?php foreach ($topCreators as $i => $creator): ?>
                <div class="d-flex align-items-center justify-content-between px-3 py-2
                            <?= $i < count($topCreators) - 1 ? 'border-bottom' : '' ?>">
                    <div class="d-flex align-items-center gap-3">
                        <!-- Rank Badge -->
                        <div class="d-flex align-items-center justify-content-center rounded-2 fw-bold flex-shrink-0"
                             style="width:26px; height:26px; font-size:11px;
                                    background:<?= $i === 0 ? 'rgba(245,158,11,0.15)' : ($i === 1 ? 'rgba(148,163,184,0.15)' : ($i === 2 ? 'rgba(180,83,9,0.15)' : 'rgba(107,114,128,0.10)')) ?>;
                                    color:<?= $i === 0 ? '#D97706' : ($i === 1 ? '#64748B' : ($i === 2 ? '#B45309' : '#9CA3AF')) ?>;">
                            <?= $i + 1 ?>
                        </div>
                        <div>
                            <div class="fw-bold" style="font-size:13px;">
                                <?= esc($creator['display_name'] ?? $creator['username']) ?>
                            </div>
                            <small class="text-muted" style="font-size:11px;">@<?= esc($creator['username']) ?></small>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold" style="font-size:13px; color:var(--ns-accent);">
                            <i class="bi bi-stars" style="font-size:11px;"></i>
                            <?= number_format($creator['starsoul_value'] ?? 0, 1) ?>
                        </div>
                        <small class="text-muted" style="font-size:10px;">StarSoul</small>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <div class="d-flex flex-column align-items-center justify-content-center py-5 text-muted">
                    <i class="bi bi-trophy mb-2" style="font-size:2rem;"></i>
                    <small>Belum ada kreator</small>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<!-- Charts Init Script -->
<script>
(function() {
    const regLabels  = <?= $regChartLabels ?? '[]' ?>;
    const regData    = <?= $regChartData ?? '[]' ?>;
    const topupLabels = <?= $topupChartLabels ?? '[]' ?>;
    const topupData   = <?= $topupChartData ?? '[]' ?>;

    // Registration Chart
    const ctx1 = document.getElementById('regChart');
    if (ctx1) {
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: regLabels,
                datasets: [{
                    label: 'Pengguna Baru',
                    data: regData,
                    borderColor: '#6366F1',
                    backgroundColor: 'rgba(99,102,241,0.07)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#6366F1',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { grid: { color: '#F3F4F6' }, ticks: { color: '#9CA3AF', font: { size: 10 } } },
                    x: { grid: { display: false }, ticks: { color: '#9CA3AF', font: { size: 10 } } }
                },
                plugins: { legend: { display: false } }
            }
        });
    }

    // Topup Chart
    const ctx2 = document.getElementById('topupChart');
    if (ctx2) {
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: topupLabels,
                datasets: [{
                    label: 'Total CC Top-Up',
                    data: topupData,
                    backgroundColor: 'rgba(6,182,212,0.60)',
                    hoverBackgroundColor: '#06B6D4',
                    borderRadius: 8,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { grid: { color: '#F3F4F6' }, ticks: { color: '#9CA3AF', font: { size: 10 } } },
                    x: { grid: { display: false }, ticks: { color: '#9CA3AF', font: { size: 10 } } }
                },
                plugins: { legend: { display: false } }
            }
        });
    }
})();
</script>
