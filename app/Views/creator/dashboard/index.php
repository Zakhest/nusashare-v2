<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= $title ?? 'Dashboard Kreator - NusaShare' ?></title>
    
    <!-- Fonts: Inter -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">
    <link rel="icon" href="<?= base_url('assets/icon/logonus.png') ?>" type="image/x-icon">
    
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
        'activePage'     => 'dashboard',
        'user'           => $user,
        'creatorProfile' => $creatorProfile,
        'username'       => $username
    ]) ?>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-full overflow-hidden bg-slate-50">
        <!-- Top Nav -->
        <header class="bg-white border-b border-slate-200 px-4 md:px-8 py-3 md:py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <h2 class="text-base md:text-lg font-bold text-slate-900">Dashboard Kreator</h2>
            </div>
            
            <div class="flex items-center gap-3 md:gap-6">
                <a href="<?= base_url('creator/content/create') ?>" class="hidden md:flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-xl font-bold text-sm hover:bg-indigo-700 transition-all">
                    <span class="material-symbols-outlined text-sm">add</span> Buat Karya Baru
                </a>
                <div class="hidden md:flex items-center gap-3 border-l pl-6 border-slate-100">
                    <div class="text-right">
                        <p class="text-xs font-bold text-slate-900"><?= $creatorProfile['display_name'] ?? $username ?></p>
                        <p class="text-[10px] text-slate-500 italic">Mode Kreator</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Body -->
        <div class="flex-1 overflow-y-auto p-4 md:p-8 pb-24 lg:pb-8 custom-scrollbar">
            
            <!-- Welcome Header -->
            <div class="mb-6 md:mb-10 flex flex-col md:flex-row md:items-end justify-between gap-2">
                <div>
                    <h1 class="text-xl md:text-3xl font-bold text-slate-900 mb-1 md:mb-2">Semangat Berkarya, <?= explode(' ', $creatorProfile['display_name'] ?? $username)[0] ?>! 🚀</h1>
                    <p class="text-slate-500 text-xs md:text-sm">Ayo buat perubahan melalui setiap kata dan cerita yang kamu bagikan.</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-slate-400 font-medium"><?= date('d F Y') ?></p>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-6 md:mb-10">
                <div class="stat-card bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined">auto_stories</span>
                    </div>
                    <p class="text-slate-500 text-[10px] font-bold uppercase tracking-wider mb-1">Total Karya</p>
                    <p class="text-2xl font-black text-slate-900"><?= number_format($stats['total_works']) ?></p>
                </div>
                <div class="stat-card bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined">publish</span>
                    </div>
                    <p class="text-slate-500 text-[10px] font-bold uppercase tracking-wider mb-1">Dipublikasikan</p>
                    <p class="text-2xl font-black text-slate-900"><?= number_format($stats['published_works']) ?></p>
                </div>
                <div class="stat-card bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <div class="w-10 h-10 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined">group</span>
                    </div>
                    <p class="text-slate-500 text-[10px] font-bold uppercase tracking-wider mb-1">Pengikut</p>
                    <p class="text-2xl font-black text-slate-900"><?= number_format($stats['followers']) ?></p>
                </div>
                <div class="stat-card bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined">drafts</span>
                    </div>
                    <p class="text-slate-500 text-[10px] font-bold uppercase tracking-wider mb-1">Draft</p>
                    <p class="text-2xl font-black text-slate-900"><?= number_format($stats['draft_works']) ?></p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-8">
                <!-- Latest Works List -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-slate-900 text-lg">Karya Terbaru Kamu</h3>
                        <a href="<?= base_url('creator/content') ?>" class="text-sm font-semibold text-indigo-600 hover:underline">Kelola Semua</a>
                    </div>
                    
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                        <?php if (empty($latestWorks)): ?>
                            <div class="p-12 text-center">
                                <span class="material-symbols-outlined text-4xl text-slate-200 mb-4">pending_actions</span>
                                <p class="text-slate-500 font-medium">Belu ada karya yang dibuat.</p>
                                <a href="<?= base_url('creator/content/create') ?>" class="mt-4 inline-block text-indigo-600 font-bold hover:underline">Mulai menulis sekarang!</a>
                            </div>
                        <?php else: ?>
                            <div class="divide-y divide-slate-50">
                                <?php foreach ($latestWorks as $work): ?>
                                    <div class="p-4 flex items-center justify-between hover:bg-slate-50 transition-colors">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-16 bg-slate-100 rounded-lg overflow-hidden flex-shrink-0">
                                                <?php if (!empty($work['cover_url'])): ?>
                                                    <?php 
                                                        $cUrl = base_url('image/cover/' . $work['id']);
                                                    ?>

                                                    <img src="<?= $cUrl ?>" class="w-full h-full object-cover">
                                                <?php else: ?>
                                                    <div class="w-full h-full flex items-center justify-center text-slate-300">
                                                        <span class="material-symbols-outlined text-sm">image</span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-slate-900 text-sm line-clamp-1"><?= $work['title'] ?></h4>
                                                <p class="text-[10px] text-slate-400 mt-1"><?= date('d M Y', strtotime($work['created_at'])) ?> • <?= strtoupper($work['status']) ?></p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <a href="<?= base_url('creator/content/' . $work['id'] . '/edit') ?>" class="p-2 text-slate-400 hover:text-indigo-600 transition-colors">
                                                <span class="material-symbols-outlined text-sm">edit</span>
                                            </a>
                                            <a href="<?= base_url('creator/content/' . $work['id'] . '/stats') ?>" class="p-2 text-slate-400 hover:text-emerald-600 transition-colors">
                                                <span class="material-symbols-outlined text-sm">bar_chart</span>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Guidance / Quick Tips -->
                <div class="space-y-6">
                    <h3 class="font-bold text-slate-900 text-lg">Tips Kreator</h3>
                    <div class="bg-indigo-600 rounded-3xl p-6 text-white shadow-lg shadow-indigo-100 relative overflow-hidden">
                        <span class="material-symbols-outlined absolute -right-4 -bottom-4 text-8xl opacity-10">lightbulb</span>
                        <h4 class="font-bold mb-2">Konsistensi adalah Kunci</h4>
                        <p class="text-xs text-indigo-100 leading-relaxed mb-4">
                            Kreator yang mengunggah karya secara rutin memiliki peluang 3x lebih besar untuk mendapatkan pengikut setia.
                        </p>
                        <button class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-xl text-xs font-bold backdrop-blur-md transition-all">
                            Baca Panduan Penulisan
                        </button>
                    </div>

                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                        <h4 class="font-bold text-slate-900 mb-4 flex items-center gap-2 text-sm">
                            <span class="material-symbols-outlined text-indigo-600 text-lg">star</span> Status Akun
                        </h4>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-500">Pencapaian Mingguan</span>
                                <span class="font-bold text-slate-900">75%</span>
                            </div>
                            <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-indigo-500" style="width: 75%"></div>
                            </div>
                            <p class="text-[10px] text-slate-400 leading-relaxed">
                                Selesaikan 2 karya lagi untuk mendapatkan badge <span class="font-bold text-indigo-500">Kreator Aktif</span> bulan ini!
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
</body>
</html>
