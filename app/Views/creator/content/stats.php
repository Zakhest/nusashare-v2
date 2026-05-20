<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= $title ?? 'Statistik Karya - NusaShare' ?></title>
    
    <!-- Fonts: Inter -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">
    <link rel="icon" href="<?= base_url('assets/icon/logonus.png') ?>" type="image/x-icon">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F8FAFC; }
        .stat-card { transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-4px); }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #E2E8F0; border-radius: 10px; }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <?= view('creator/_sidebar', [
        'activePage'     => 'stats',
        'user'           => $user,
        'creatorProfile' => $creatorProfile,
        'username'       => $username
    ]) ?>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-full overflow-hidden bg-slate-50">
        <!-- Top Nav -->
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="<?= base_url('creator/stats') ?>" class="p-2 hover:bg-slate-50 rounded-xl transition-all text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <h2 class="text-lg font-bold text-slate-900">Statistik: <?= esc($work['title']) ?></h2>
            </div>
            
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-3 border-l pl-6 border-slate-100">
                    <div class="text-right">
                        <p class="text-xs font-bold text-slate-900"><?= $creatorProfile['display_name'] ?? $username ?></p>
                        <p class="text-[10px] text-slate-500 italic">Mode Kreator</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Body -->
        <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
            
            <div class="mb-10 flex items-start gap-6">
                <div class="w-24 h-32 bg-slate-200 rounded-xl overflow-hidden shadow-sm flex-shrink-0">
                    <?php 
                        $coverUrl = base_url('image/cover/' . $work['id']);
                        if (empty($work['cover_url'])) {
                            $coverUrl = base_url('assets/img/default-cover.jpg');
                        }
                    ?>
                    <img src="<?= $coverUrl ?>" alt="Cover" class="w-full h-full object-cover">
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900"><?= esc($work['title']) ?></h1>
                    <div class="mt-2 flex items-center gap-3">
                        <span class="px-3 py-1 bg-indigo-50 text-indigo-600 border border-indigo-100 rounded-full text-[10px] font-bold uppercase tracking-wider">
                            <?= esc($work['content_type']) ?>
                        </span>
                        <?php if ($work['status'] === 'published'): ?>
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 border border-emerald-100 rounded-full text-[10px] font-bold uppercase tracking-wider">Terbit</span>
                        <?php else: ?>
                            <span class="px-3 py-1 bg-amber-50 text-amber-600 border border-amber-100 rounded-full text-[10px] font-bold uppercase tracking-wider">Draft</span>
                        <?php endif; ?>
                        <span class="text-xs text-slate-400 font-medium">Dirilis: <?= date('d M Y', strtotime($work['created_at'])) ?></span>
                    </div>
                    <p class="text-sm text-slate-600 mt-4 max-w-2xl line-clamp-2"><?= esc($work['description']) ?></p>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-10">
                <div class="stat-card bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined">visibility</span>
                    </div>
                    <p class="text-slate-500 text-[10px] font-bold uppercase tracking-wider mb-1">Total Pembaca</p>
                    <p class="text-2xl font-black text-slate-900"><?= number_format($stats['views']) ?></p>
                </div>
                <!-- Optional Income Stat -->
                <?php if ($work['is_paid'] || $stats['total_income'] > 0): ?>
                <div class="stat-card bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined">payments</span>
                    </div>
                    <p class="text-slate-500 text-[10px] font-bold uppercase tracking-wider mb-1">Pendapatan</p>
                    <p class="text-2xl font-black text-slate-900"><?= number_format($stats['total_income']) ?> <span class="text-sm">CC</span></p>
                </div>
                <?php endif; ?>

                <div class="stat-card bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <div class="w-10 h-10 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined">favorite</span>
                    </div>
                    <p class="text-slate-500 text-[10px] font-bold uppercase tracking-wider mb-1">Disukai</p>
                    <p class="text-2xl font-black text-slate-900"><?= number_format($stats['likes']) ?></p>
                </div>
                <div class="stat-card bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined">forum</span>
                    </div>
                    <p class="text-slate-500 text-[10px] font-bold uppercase tracking-wider mb-1">Komentar</p>
                    <p class="text-2xl font-black text-slate-900"><?= number_format($stats['comments']) ?></p>
                </div>
                <div class="stat-card bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined">star</span>
                    </div>
                    <p class="text-slate-500 text-[10px] font-bold uppercase tracking-wider mb-1">Skor Kualitas</p>
                    <p class="text-2xl font-black text-slate-900"><?= $stats['quality_score'] ?></p>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 h-[400px]">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-bold text-slate-900">Pertumbuhan Pembaca (Karya)</h3>
                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">7 Hari Terakhir</div>
                    </div>
                    <canvas id="growthChart"></canvas>
                </div>
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 h-[400px]">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-bold text-slate-900">Distribusi Interaksi Pembaca</h3>
                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Semua Waktu</div>
                    </div>
                    <canvas id="distChart"></canvas>
                </div>
            </div>

            <!-- Recent Comments Section -->
            <div class="mb-6">
                <h3 class="font-bold text-slate-900 text-lg flex items-center gap-2">
                    <span class="material-symbols-outlined text-indigo-600">forum</span> 
                    Komentar Terbaru
                </h3>
            </div>
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden mb-10">
                <?php if (empty($recentComments)): ?>
                    <div class="p-8 text-center text-slate-400">
                        <span class="material-symbols-outlined text-4xl mb-2">speaker_notes_off</span>
                        <p class="text-sm">Belum ada komentar untuk karya ini.</p>
                    </div>
                <?php else: ?>
                    <ul class="divide-y divide-slate-100">
                        <?php foreach ($recentComments as $comment): ?>
                        <li class="p-6 hover:bg-slate-50 transition-colors">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 bg-slate-100 text-slate-500 font-bold rounded-full flex items-center justify-center shrink-0">
                                    <?= strtoupper(substr($comment['username'], 0, 1)) ?>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-1">
                                        <h4 class="font-bold text-slate-900 text-sm">
                                            <?= esc($comment['username']) ?>
                                            <?php if ($comment['role'] === 'creator'): ?>
                                                <span class="ml-2 px-2 py-0.5 bg-indigo-100 text-indigo-600 rounded text-[9px] font-black uppercase">Kreator</span>
                                            <?php endif; ?>
                                        </h4>
                                        <span class="text-[10px] font-medium text-slate-400"><?= date('d M Y, H:i', strtotime($comment['created_at'])) ?></span>
                                    </div>
                                    <p class="text-slate-600 text-sm leading-relaxed"><?= nl2br(esc($comment['content'])) ?></p>
                                </div>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

        </div>
    </main>

    <script>
        // Growth Chart
        const ctxGrowth = document.getElementById('growthChart').getContext('2d');
        new Chart(ctxGrowth, {
            type: 'line',
            data: {
                labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                datasets: [{
                    label: 'Pembaca Harian',
                    data: <?= json_encode($growthData ?? [0,0,0,0,0,0,0]) ?>,
                    borderColor: '#4F46E5',
                    backgroundColor: 'rgba(79, 70, 229, 0.05)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#4F46E5',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { display: false },
                        ticks: { font: { size: 10, weight: 'bold' }, color: '#94A3B8' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10, weight: 'bold' }, color: '#94A3B8' }
                    }
                }
            }
        });

        // Distribution Chart
        const ctxDist = document.getElementById('distChart').getContext('2d');
        new Chart(ctxDist, {
            type: 'doughnut',
            data: {
                labels: ['Pembaca Pasif', 'Menyukai', 'Menyimpan', 'Berkomentar'],
                datasets: [{
                    data: <?= json_encode($distributionData ?? [1,0,0,0]) ?>,
                    backgroundColor: [
                        '#E2E8F0', // Pasif
                        '#F43F5E', // Suka (Rose)
                        '#10B981', // Simpan (Emerald)
                        '#F59E0B'  // Komentar (Amber)
                    ],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { size: 11, weight: '600' }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
