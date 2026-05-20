<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= $title ?? 'Dashboard - NusaShare' ?></title>
    
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
        .sidebar-link.active { background-color: #EEF2FF; color: #4F46E5; border-right: 4px solid #4F46E5; }
        .stat-card { transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-4px); }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <?= view('dashboard/_sidebar', [
        'activePage'     => 'dashboard',
        'user'           => $user,
        'creatorProfile' => $creatorProfile
    ]) ?>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-full overflow-hidden bg-slate-50">
        <!-- Top Nav -->
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4 lg:hidden">
                <img src="<?= base_url('assets/icon/logonus.png') ?>" alt="Logo" class="w-8 h-8">
            </div>
            <h2 class="text-lg font-bold text-slate-900 hidden lg:block">Ringkasan Dashboard</h2>
            
            <div class="flex items-center gap-3 md:gap-6">
                <!-- Notifications Dropdown -->
                <div class="relative hidden md:flex" id="notification-dropdown-container">
                    <button id="notification-bell" class="text-slate-500 hover:text-slate-900 transition-colors relative">
                        <span class="material-symbols-outlined">notifications</span>
                        <span id="notification-badge" class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full hidden border border-white"></span>
                    </button>
                    
                    <div id="notification-dropdown" class="absolute right-0 top-full mt-4 w-80 bg-white rounded-xl shadow-xl border border-slate-100 hidden z-50 transform opacity-0 scale-95 transition-all origin-top-right">
                        <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50 rounded-t-xl">
                            <h3 class="font-bold text-slate-900">Notifikasi</h3>
                            <button id="mark-all-read" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Tandai semua dibaca</button>
                        </div>
                        <div id="notification-list" class="max-h-80 overflow-y-auto custom-scrollbar">
                            <div class="p-6 text-center text-slate-400 text-sm">Memuat notifikasi...</div>
                        </div>
                    </div>
                </div>
                <?php 
                    $creditModel = new \App\Models\CreditModel();
                    $userCredit = $creditModel->find(session()->get('userId'));
                    $balance = $userCredit ? $userCredit['balance'] : 0;
                ?>
                <a href="<?= base_url('topup') ?>" class="hidden md:flex bg-indigo-50 px-3 py-1.5 rounded-full items-center gap-2 border border-indigo-100 hover:bg-indigo-100 transition-colors mr-2">
                    <span class="material-symbols-outlined text-indigo-600 text-lg">account_balance_wallet</span>
                    <span class="text-xs font-bold text-indigo-900"><?= number_format($balance) ?> CC</span>
                </a>
                <!-- Mobile: compact wallet chip -->
                <a href="<?= base_url('topup') ?>" class="md:hidden flex bg-indigo-50 px-2.5 py-1.5 rounded-full items-center gap-1.5 border border-indigo-100">
                    <span class="material-symbols-outlined text-indigo-600 text-base">account_balance_wallet</span>
                    <span class="text-xs font-bold text-indigo-900"><?= number_format($balance) ?> CC</span>
                </a>
                <div class="hidden md:flex items-center gap-3 border-l pl-6 border-slate-100">
                    <div class="text-right">
                        <?php 
                            $userModel = new \App\Models\UserModel();
                            $starsoulValue = $userModel->calculateStarsoul($user);
                            $starsoulTier = $userModel->getStarsoulTier($starsoulValue, $user['role']);
                        ?>
                        <p class="text-xs font-bold text-slate-900"><?= $profile['display_name'] ?? $username ?></p>
                        <p class="text-[10px] text-slate-500"><?= $starsoulTier ?></p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-[#4F46E5] font-bold overflow-hidden">
                        <?php if (!empty($profile['profile_image'])): ?>
                            <img src="<?= base_url('image/profile/' . $profile['profile_image']) ?>" alt="Avatar" class="w-full h-full object-cover">
                        <?php else: ?>
                            <?= strtoupper(substr($username, 0, 1)) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Body -->
        <div class="flex-1 overflow-y-auto p-4 md:p-8 pb-24 lg:pb-8">
            
            <!-- Welcome Header -->
            <div class="mb-10">
                <h1 class="text-3xl font-bold text-slate-900 mb-2">Halo, <?= $username ?>! 👋</h1>
                <p class="text-slate-500">Senang melihatmu kembali. Inilah yang terjadi pada koleksimu hari ini.</p>
            </div>

            <!-- Notifications (Success message from login) -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="mb-8 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 text-sm rounded-r-xl flex items-center justify-between animate-[fadeInDown_0.3s_ease-out]">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-emerald-500">check_circle</span>
                        <?= session()->getFlashdata('success') ?>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-600">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>
            <?php endif; ?>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <div class="stat-card bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <div class="w-10 h-10 bg-indigo-50 text-[#4F46E5] rounded-2xl flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined">bookmark</span>
                    </div>
                    <p class="text-slate-500 text-xs font-semibold mb-1">DISIMPAN</p>
                    <p class="text-2xl font-bold text-slate-900"><?= number_format($savedCount) ?> Karya</p>
                </div>
                <div class="stat-card bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <div class="w-10 h-10 bg-cyan-50 text-[#22D3EE] rounded-2xl flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined">payments</span>
                    </div>
                    <p class="text-slate-500 text-xs font-semibold mb-1">CREDITS</p>
                    <p class="text-2xl font-bold text-slate-900"><?= number_format($creditBalance) ?></p>
                </div>
                <div class="stat-card bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <div class="w-10 h-10 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined">group</span>
                    </div>
                    <p class="text-slate-500 text-xs font-semibold mb-1">MENGIKUTI</p>
                    <p class="text-2xl font-bold text-slate-900"><?= number_format($followingCount) ?> Kreator</p>
                </div>
                <div class="stat-card bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <div class="w-10 h-10 bg-purple-50 text-purple-500 rounded-2xl flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined">stylus</span>
                    </div>
                    <p class="text-slate-500 text-xs font-semibold mb-1">KARYA KAMU</p>
                    <p class="text-2xl font-bold text-slate-900"><?= number_format($yourWorksCount) ?></p>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-6">
                    
                    <?php if (!empty($latestFollowedUpdate)): ?>
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-slate-900 text-lg">Dari Kreator yang Kamu Ikuti</h3>
                        </div>
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-3xl p-6 shadow-md text-white flex flex-col md:flex-row gap-6 items-center hover:shadow-lg transition-shadow relative overflow-hidden">
                            <!-- Background decoration -->
                            <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl -mr-10 -mt-10"></div>
                            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white opacity-10 rounded-full blur-xl -ml-6 -mb-6"></div>
                            
                            <div class="w-full md:w-32 h-40 bg-white/20 rounded-2xl overflow-hidden flex-shrink-0 relative z-10 shadow-inner">
                                <?php if (!empty($latestFollowedUpdate['cover_url'])): ?>
                                    <img src="<?= $latestFollowedUpdate['cover_url'] ?>" alt="<?= htmlspecialchars($latestFollowedUpdate['title']) ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center text-white/50 bg-slate-800">
                                        <span class="material-symbols-outlined text-4xl">image</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="flex flex-col flex-1 relative z-10 w-full">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-6 h-6 rounded-full bg-white/20 overflow-hidden border border-white/30 flex items-center justify-center text-xs font-bold">
                                        <?php if (!empty($latestFollowedUpdate['profile_image'])): ?>
                                            <img src="<?= base_url('image/profile/' . $latestFollowedUpdate['profile_image']) ?>" alt="Creator" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <?= strtoupper(substr($latestFollowedUpdate['creator_name'], 0, 1)) ?>
                                        <?php endif; ?>
                                    </div>
                                    <span class="text-xs font-medium text-white/90">Oleh <span class="font-bold text-white"><?= htmlspecialchars($latestFollowedUpdate['creator_name']) ?></span></span>
                                    <span class="text-[10px] bg-white/20 px-2 py-0.5 rounded-full ml-auto uppercase tracking-wider font-bold">
                                        <?= $latestFollowedUpdate['content_type'] ?? 'Karya' ?>
                                    </span>
                                </div>
                                
                                <h4 class="font-bold text-xl md:text-2xl line-clamp-1 mb-2 drop-shadow-sm"><?= htmlspecialchars($latestFollowedUpdate['title']) ?></h4>
                                <p class="text-sm text-white/80 line-clamp-2 mb-4 font-light"><?= strip_tags($latestFollowedUpdate['description']) ?></p>
                                
                                <div class="mt-auto flex items-center justify-between">
                                    <span class="text-xs text-white/70 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-sm">schedule</span> 
                                        Terbaru
                                    </span>
                                    <a href="<?= base_url('works/' . $latestFollowedUpdate['id']) ?>" class="bg-white text-indigo-600 hover:bg-indigo-50 transition-colors px-4 py-2 rounded-xl text-sm font-bold shadow-sm inline-flex items-center gap-1">
                                        Lihat Karya <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-slate-900 text-lg">Karya yang Mungkin Kamu Suka</h3>
                        <a href="<?= base_url('explore') ?>" class="text-sm font-semibold text-[#4F46E5] hover:underline">Lihat Semua</a>
                    </div>
                    
                    <?php if (empty($recommendations)): ?>
                        <!-- Empty State for Works -->
                        <div class="bg-white border-2 border-dashed border-slate-100 rounded-3xl p-12 text-center">
                            <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="material-symbols-outlined text-3xl">image</span>
                            </div>
                            <h4 class="font-bold text-slate-900 mb-2">Belum ada koleksi?</h4>
                            <p class="text-slate-400 text-sm max-w-xs mx-auto mb-6">Mulai eksplorasi karya hebat dari kreator lokal Nusantara.</p>
                            <a href="<?= base_url('explore') ?>" class="inline-flex items-center justify-center px-6 py-2.5 bg-slate-900 text-white rounded-xl font-bold text-sm shadow-lg shadow-slate-200 hover:bg-slate-800 transition-colors">
                                Mulai Eksplorasi
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php foreach ($recommendations as $work): ?>
                                <div class="bg-white p-4 rounded-3xl shadow-sm border border-slate-100 flex gap-4 hover:shadow-md transition-shadow">
                                    <div class="w-20 h-28 bg-slate-100 rounded-xl overflow-hidden flex-shrink-0">
                                        <?php if (!empty($work['cover_url'])): ?>
                                            <img src="<?= $work['cover_url'] ?>" alt="<?= $work['title'] ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <div class="w-full h-full flex items-center justify-center text-slate-300">
                                                <span class="material-symbols-outlined text-3xl">image</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex flex-col justify-center">
                                        <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider mb-1"><?= $work['content_type'] ?></span>
                                        <h4 class="font-bold text-slate-900 line-clamp-1 mb-1"><?= $work['title'] ?></h4>
                                        <p class="text-xs text-slate-500 line-clamp-2 mb-3"><?= strip_tags($work['description']) ?></p>
                                        <a href="<?= base_url('works/' . $work['id']) ?>" class="text-xs font-bold text-slate-900 flex items-center gap-1 hover:text-indigo-600 transition-colors">
                                            Baca Sekarang <span class="material-symbols-outlined text-xs">arrow_forward</span>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="space-y-6">
                    <h3 class="font-bold text-slate-900 text-lg">Statistik StarSoul</h3>
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-[#4F46E5]">
                                <span class="material-symbols-outlined">auto_awesome</span>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Level Saat Ini</p>
                                <p class="font-bold text-slate-900"><?= $starsoulTier ?></p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <?php 
                                $starsoul = $starsoulValue;
                                $next_level_at = 15.5; // Final Cap
                                
                                // Simple progress mapping for visualization
                                $progress = ($starsoul / $next_level_at) * 100;
                                if ($progress > 100) $progress = 100;
                            ?>
                            <div class="flex justify-between text-xs font-bold">
                                <span class="text-slate-500">Progress ke Imperator</span>
                                <span class="text-[#4F46E5]"><?= number_format($starsoul, 1) ?> / 15.5</span>
                            </div>
                            <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-indigo-500 rounded-full" style="width: <?= $progress ?>%"></div>
                            </div>

                            <?php if ($user['role'] === 'kreator'): ?>
                                <div class="pt-4 border-t border-slate-50 mt-4">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Metrik Penilaian</p>
                                    <div class="grid grid-cols-3 gap-2">
                                        <div class="bg-slate-50 p-2 rounded-xl text-center">
                                            <p class="text-[10px] text-slate-500 mb-1">Engagement</p>
                                            <p class="text-sm font-bold text-indigo-600"><?= $user['engagement'] ?? 0 ?></p>
                                        </div>
                                        <div class="bg-slate-50 p-2 rounded-xl text-center">
                                            <p class="text-[10px] text-slate-500 mb-1">Commit</p>
                                            <p class="text-sm font-bold text-emerald-600"><?= $user['commitment'] ?? 0 ?></p>
                                        </div>
                                        <div class="bg-slate-50 p-2 rounded-xl text-center">
                                            <p class="text-[10px] text-slate-500 mb-1">Behavior</p>
                                            <p class="text-sm font-bold text-rose-600"><?= number_format($user['behavior'] ?? 15.5, 1) ?></p>
                                        </div>
                                    </div>
                                    <p class="text-[9px] text-slate-400 mt-3 leading-tight italic">
                                        *Total = (E × 0.5) + (C × 0.3) + (B × 0.2)
                                    </p>
                                </div>
                            <?php endif; ?>
                            <p class="text-[10px] text-slate-400 leading-relaxed italic">
                                Teruslah memberikan apresiasi pada kreator untuk meningkatkan StarSoul kamu!
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
    <script>
        window.nusaAppData = { baseUrl: '<?= base_url() ?>/' };
    </script>
    <script src="<?= base_url('assets/js/notifications.js') ?>"></script>
</body>
</html>
