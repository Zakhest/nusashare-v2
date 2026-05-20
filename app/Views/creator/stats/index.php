<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= $title ?? 'Statistik Kreator - NusaShare' ?></title>
    
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
            <h2 class="text-lg font-bold text-slate-900">Analitik & Statistik</h2>
            
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
            
            <div class="mb-10">
                <h1 class="text-2xl font-bold text-slate-900">Ringkasan Performa</h1>
                <p class="text-slate-500 text-sm mt-1">Pantau perkembangan pembaca dan interaksi karyamu.</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <div class="stat-card bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined">visibility</span>
                    </div>
                    <p class="text-slate-500 text-[10px] font-bold uppercase tracking-wider mb-1">Total Pembaca</p>
                    <p class="text-2xl font-black text-slate-900"><?= number_format($stats['total_views']) ?></p>
                    <div class="mt-2 flex items-center gap-1 text-emerald-500 font-bold text-[10px]">
                        <span class="material-symbols-outlined text-xs">trending_up</span>
                        <span>+12% dr bulan lalu</span>
                    </div>
                </div>
                <div class="stat-card bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <div class="w-10 h-10 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined">bookmark</span>
                    </div>
                    <p class="text-slate-500 text-[10px] font-bold uppercase tracking-wider mb-1">Total Simpan</p>
                    <p class="text-2xl font-black text-slate-900"><?= number_format($stats['total_bookmarks']) ?></p>
                    <div class="mt-2 flex items-center gap-1 text-emerald-500 font-bold text-[10px]">
                        <span class="material-symbols-outlined text-xs">trending_up</span>
                        <span>+5% dr bulan lalu</span>
                    </div>
                </div>
                <div class="stat-card bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <div class="w-10 h-10 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined">group</span>
                    </div>
                    <p class="text-slate-500 text-[10px] font-bold uppercase tracking-wider mb-1">Pengikut</p>
                    <p class="text-2xl font-black text-slate-900"><?= number_format($stats['followers']) ?></p>
                    <div class="mt-2 flex items-center gap-1 text-emerald-500 font-bold text-[10px]">
                        <span class="material-symbols-outlined text-xs">trending_up</span>
                        <span>+2 hari ini</span>
                    </div>
                </div>
                <div class="stat-card bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined">star</span>
                    </div>
                    <p class="text-slate-500 text-[10px] font-bold uppercase tracking-wider mb-1">Skor Kualitas</p>
                    <p class="text-2xl font-black text-slate-900"><?= $stats['quality_score'] ?></p>
                    <div class="mt-2 flex items-center gap-1 text-slate-400 font-bold text-[10px]">
                        <span>Sangat Baik</span>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 h-[400px]">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-bold text-slate-900">Pertumbuhan Pembaca</h3>
                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">7 Hari Terakhir</div>
                    </div>
                    <canvas id="growthChart"></canvas>
                </div>
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 h-[400px]">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-bold text-slate-900">Distribusi Interaksi</h3>
                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Bulan Ini</div>
                    </div>
                    <canvas id="distChart"></canvas>
                </div>
            </div>

            <!-- Per Work Breakdown -->
            <div class="mb-6">
                <h3 class="font-bold text-slate-900 text-lg">Performa Per Karya</h3>
            </div>
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden mb-8">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Karya</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center">Pembaca</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center">Disimpan</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center">Status</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-right">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($worksWithStats)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-slate-400 text-sm">Belum ada data statistik tersedia.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($worksWithStats as $work): ?>
                                <tr class="hover:bg-slate-50/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-10 bg-slate-100 rounded-md overflow-hidden flex-shrink-0">
                                                <div class="w-full h-full flex items-center justify-center text-slate-300">
                                                    <span class="material-symbols-outlined text-xs">image</span>
                                                </div>
                                            </div>
                                            <span class="font-bold text-slate-900 text-sm line-clamp-1"><?= $work['title'] ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center font-medium text-slate-600 text-sm">
                                        <?= number_format($work['views']) ?>
                                    </td>
                                    <td class="px-6 py-4 text-center font-medium text-slate-600 text-sm">
                                        <?= number_format($work['bookmarks']) ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <?php if ($work['status'] === 'published'): ?>
                                            <span class="px-2 py-0.5 bg-emerald-50 text-emerald-600 text-[9px] font-bold rounded-full uppercase">Terbit</span>
                                        <?php else: ?>
                                            <span class="px-2 py-0.5 bg-amber-50 text-amber-600 text-[9px] font-bold rounded-full uppercase">Draft</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="<?= base_url('creator/content/' . $work['id'] . '/stats') ?>" class="text-indigo-600 hover:text-indigo-800 font-bold text-xs uppercase transition-colors">Insight</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
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
                    label: 'Pembaca',
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
                labels: ['Pembaca Biasa', 'Suka (Likes)', 'Interaksi Simpan', 'Komentar'],
                datasets: [{
                    data: <?= json_encode($distributionData ?? [1,0,0,0]) ?>,
                    backgroundColor: [
                        '#4F46E5',
                        '#10B981',
                        '#F43F5E',
                        '#F59E0B'
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
