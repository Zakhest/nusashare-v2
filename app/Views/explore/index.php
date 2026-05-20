<?php /** INTEGRATED SIDEBAR & DYNAMIC HEADER **/ ?>
<html lang="id"><head>

    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Jelajahi Karya - NusaShare</title>
    
    <!-- Fonts: Inter -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Material Icons -->
    <link rel="icon" href="<?= base_url('assets/icon/logonus.png') ?>" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/works.css') ?>">

    <style>
        :root {
            --lp-bg: #EEF2FF;
            --lp-primary: #4F46E5;
            --lp-accent: #22D3EE;
            --lp-surface: #FFFFFF;
            --lp-border: rgba(79, 70, 229, 0.15);
            --lp-text-main: #0F172A;
            --lp-text-muted: #475569;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #F8FAFC; /* Slightly lighter/cleaner for gallery */
            color: var(--lp-text-main);
            overflow-x: hidden;
        }

        .sidebar-link.active { background-color: #EEF2FF; color: #4F46E5; border-right: 4px solid #4F46E5; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #E2E8F0; border-radius: 10px; }

        /* Buttons & Gradients */
        .btn-primary {
            background: linear-gradient(135deg, #4F46E5, #22D3EE);
            color: white;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            opacity: 0.95;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
        }

        /* Card Styles */
        .art-card {
            background: var(--lp-surface);
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .art-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.08);
            border-color: #CBD5E1;
        }

        /* Modal Blur Effect */
        .blur-reveal {
            filter: blur(12px);
            transition: filter 0.5s ease;
        }
        
        /* Custom Scrollbar for modal content if needed */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1; 
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1; 
            border-radius: 10px;
        }

        /* Loading Skeleton Animation */
        @keyframes shimmer {
            0% { background-position: -1000px 0; }
            100% { background-position: 1000px 0; }
        }
        .skeleton {
            background: linear-gradient(to right, #f1f5f9 4%, #e2e8f0 25%, #f1f5f9 36%);
            background-size: 1000px 100%;
            animation: shimmer 2s infinite linear;
        }

        /* Modal Transition */
        .modal-enter {
            opacity: 0;
            transform: scale(0.95);
        }
        .modal-enter-active {
            opacity: 1;
            transform: scale(1);
            transition: opacity 0.2s, transform 0.2s;
        }
        .modal-exit {
            opacity: 1;
        }
        .modal-exit-active {
            opacity: 0;
            transition: opacity 0.2s;
        }
    </style>

</head>
<body class="bg-[#F8FAFC] <?= $isLoggedIn ? 'flex h-screen overflow-hidden' : '' ?>">

    <?php if ($isLoggedIn): ?>
        <?= view('dashboard/_sidebar', [
            'activePage'     => 'explore',
            'user'           => $user,
            'creatorProfile' => $creatorProfile
        ]) ?>
    <?php endif; ?>

    <!-- Main Wrapper if Logged In -->
    <div class="<?= $isLoggedIn ? 'flex-1 flex flex-col h-full overflow-hidden' : '' ?>">

    <!-- 1️⃣ Top Bar / Header -->
    <nav class="<?= $isLoggedIn ? 'bg-white border-b border-slate-200 px-8 py-4 flex items-center justify-between sticky top-0 z-40' : 'fixed top-0 w-full z-40 bg-white/80 backdrop-blur-md border-b border-slate-200' ?>">
        <div class="<?= $isLoggedIn ? 'w-full flex items-center justify-between' : 'max-w-7xl mx-auto px-4 md:px-6 h-16 flex items-center justify-between' ?>">
            <!-- Left: Logo & Nav -->
            <div class="flex items-center gap-6">
                <?php if (!$isLoggedIn): ?>
                    <a href="<?= base_url() ?>" class="flex items-center gap-2 group">
                        <img src="<?= base_url('assets/icon/logonus.png') ?>" alt="Logo N" class="w-8 h-8"/>
                        <span class="font-bold text-lg text-[#0F172A] tracking-tight">NusaShare</span>
                    </a>
                <?php else: ?>
                    <h2 class="text-lg font-bold text-slate-900 hidden lg:block">Explore Karya</h2>
                    <div class="flex items-center gap-4 lg:hidden">
                        <img src="<?= base_url('assets/icon/logonus.png') ?>" alt="Logo" class="w-8 h-8">
                    </div>
                <?php endif; ?>

                <div class="hidden lg:flex items-center bg-slate-100 rounded-full px-4 py-2 w-64 xl:w-80 group focus-within:ring-2 focus-within:ring-indigo-100 transition-all">
                    <span class="material-symbols-outlined text-slate-400 text-xl group-focus-within:text-indigo-500">search</span>
                    <form action="<?= base_url('search') ?>" method="GET" class="w-full">
                        <input type="text" name="q" value="<?= esc($query ?? '') ?>" placeholder="Cari karya atau kreator..." class="bg-transparent border-none outline-none text-sm w-full px-2 text-slate-700 placeholder:text-slate-400">
                    </form>
                </div>
            </div>

            <!-- Right: Auth Actions or Profile -->
            <div class="flex items-center gap-3 md:gap-6">
                <?php if (!$isLoggedIn): ?>
                    <div class="flex items-center gap-4">
                        <a href="<?= base_url('register') ?>" class="text-sm font-medium text-[#64748B] hover:text-[#4F46E5] transition-colors hidden sm:block">Daftar</a>
                        <a href="<?= base_url('login') ?>" class="btn-primary px-5 py-2 rounded-full text-sm font-medium shadow-sm">Masuk</a>
                    </div>
                <?php else: ?>
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
                    <a href="<?= base_url('topup') ?>" class="hidden md:flex bg-indigo-50 px-3 py-1.5 rounded-full items-center gap-2 border border-indigo-100 hover:bg-indigo-100 transition-colors">
                        <span class="material-symbols-outlined text-indigo-600 text-lg">account_balance_wallet</span>
                        <span class="text-xs font-bold text-indigo-900"><?= number_format($balance) ?> CC</span>
                    </a>
                    <!-- Mobile: compact wallet chip only -->
                    <a href="<?= base_url('topup') ?>" class="md:hidden flex bg-indigo-50 px-2.5 py-1.5 rounded-full items-center gap-1.5 border border-indigo-100">
                        <span class="material-symbols-outlined text-indigo-600 text-base">account_balance_wallet</span>
                        <span class="text-xs font-bold text-indigo-900"><?= number_format($balance) ?> CC</span>
                    </a>
                    <div class="hidden md:flex items-center gap-3 border-l pl-6 border-slate-100">
                        <div class="text-right">
                            <p class="text-xs font-bold text-slate-900"><?= $profile['display_name'] ?? $username ?></p>
                            <p class="text-[10px] text-slate-500"><?= $user['starsoul_status'] ?? 'Anggota NusaShare' ?></p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-[#4F46E5] font-bold overflow-hidden">
                            <?php if (!empty($profile['profile_image'])): ?>
                                <img src="/image-nusashare/profile/<?= $profile['profile_image'] ?>" alt="Avatar" class="w-full h-full object-cover">
                            <?php else: ?>
                                <?= strtoupper(substr($username, 0, 1)) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- 2️⃣ Hero Mini -->
    <?php if (!$isLoggedIn): ?>
    <header class="pt-28 pb-12 px-6 bg-gradient-to-b from-[#EEF2FF] to-[#F8FAFC]">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-3xl md:text-4xl font-bold text-[#0F172A] mb-3 tracking-tight">
                Jelajahi karya kreator Indonesia.
            </h1>
            <p class="text-[#475569] text-base md:text-lg mb-6 max-w-xl mx-auto leading-relaxed">
                Lihat preview karya pilihan dari talenta terbaik Nusantara sebelum membuka akses penuh.
            </p>
            <a href="<?= base_url('login') ?>" class="inline-flex items-center text-sm font-medium text-[#4F46E5] hover:text-[#4338CA] transition-colors gap-1 group">
                Masuk untuk akses penuh 
                <span class="material-symbols-outlined text-base group-hover:translate-x-1 transition-transform">arrow_forward</span>
            </a>
        </div>
    </header>
    <?php endif; ?>

    <!-- 3️⃣ Content Container (Scrollable if Logged In) -->
    <div class="<?= $isLoggedIn ? 'flex-1 overflow-y-auto pb-20 lg:pb-0' : 'pb-20 lg:pb-0' ?>">
        
        <!-- Filter Bar -->
        <section class="<?= $isLoggedIn ? 'sticky top-0 z-30 bg-[#F8FAFC]/95 backdrop-blur-sm border-b border-slate-200/60 py-3' : 'sticky top-16 z-30 bg-[#F8FAFC]/95 backdrop-blur-sm border-b border-slate-200/60 py-3' ?>">
            <div class="max-w-7xl mx-auto px-4 md:px-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-3">

            <?php
                $currentSort = $sort ?? 'latest';
                $currentType = $type ?? '';
                $exploreBase = base_url('explore');

                // Helper untuk build URL dengan param
                function exploreUrl(string $type = '', string $sort = 'latest'): string {
                    $params = [];
                    if (!empty($type)) $params['type'] = $type;
                    if ($sort !== 'latest') $params['sort'] = $sort;
                    $base = base_url('explore');
                    return empty($params) ? $base : $base . '?' . http_build_query($params);
                }

                $types = [
                    ''           => 'Semua',
                    'novel'      => 'Novel',
                    'light_novel'=> 'Light Novel',
                    'comic'      => 'Comic',
                    'text'       => 'Teks',
                ];
            ?>

            <!-- Categories -->
            <div class="flex gap-2 overflow-x-auto pb-1 md:pb-0 w-full md:w-auto hide-scrollbar">
                <?php foreach ($types as $typeKey => $typeLabel): ?>
                    <?php $isActive = ($currentType === $typeKey); ?>
                    <a href="<?= exploreUrl($typeKey, $currentSort) ?>"
                       class="px-4 py-1.5 rounded-full text-sm font-medium whitespace-nowrap transition-colors <?= $isActive
                           ? 'bg-[#0F172A] text-white shadow-sm ring-1 ring-[#0F172A]'
                           : 'bg-white text-[#64748B] border border-slate-200 hover:border-[#4F46E5] hover:text-[#4F46E5]' ?>">
                        <?= $typeLabel ?>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <!-- Sort -->
            <div class="flex items-center gap-2 text-sm text-[#64748B] shrink-0">
                <span>Urutkan:</span>
                <div class="relative group cursor-pointer flex items-center gap-1 font-medium text-[#0F172A]">
                    <?php
                        $sortLabels = ['latest' => 'Terbaru', 'popular' => 'Populer', 'trending' => 'Trending'];
                        echo $sortLabels[$currentSort] ?? 'Terbaru';
                    ?>
                    <span class="material-symbols-outlined text-base">expand_more</span>
                    <div class="absolute right-0 top-full mt-2 w-36 bg-white rounded-xl shadow-lg border border-slate-100 hidden group-hover:block p-1 z-50">
                        <?php foreach ($sortLabels as $sortKey => $sortName): ?>
                            <a href="<?= exploreUrl($currentType, $sortKey) ?>"
                               class="flex items-center gap-2 px-3 py-2 hover:bg-slate-50 rounded-lg text-sm <?= $currentSort === $sortKey ? 'text-[#4F46E5] font-semibold bg-indigo-50/50' : 'text-slate-700' ?>">
                                <?php if ($currentSort === $sortKey): ?>
                                    <span class="material-symbols-outlined text-xs text-[#4F46E5]">check</span>
                                <?php endif; ?>
                                <?= $sortName ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4️⃣ Grid Karya & Users -->
    <main class="max-w-7xl mx-auto px-4 md:px-6 py-8 min-h-screen">
        
        <?php if (isset($query)): ?>
            <div class="mb-10">
                <h1 class="text-2xl font-bold text-slate-900">Hasil Pencarian untuk "<?= esc($query) ?>"</h1>
                <p class="text-slate-500 text-sm mt-1">Ditemukan <?= count($works ?? []) ?> karya dan <?= count($users ?? []) ?> akun.</p>
            </div>

            <?php if (!empty($users)): ?>
                <section class="mb-12">
                    <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-indigo-500">group</span>
                        Akun User & Kreator
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        <?php foreach ($users as $u): ?>
                            <a href="<?= base_url('user/' . $u['username']) ?>" class="flex items-center gap-4 bg-white p-4 rounded-2xl border border-slate-100 hover:border-indigo-200 hover:shadow-md transition-all group">
                                <div class="w-12 h-12 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold border-2 border-white shadow-sm shrink-0 overflow-hidden">
                                    <?php if (!empty($u['profile_image'])): ?>
                                        <img src="/image-nusashare/profile/<?= $u['profile_image'] ?>" alt="" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <?= strtoupper(substr($u['display_name'] ?? $u['username'], 0, 1)) ?>
                                    <?php endif; ?>
                                </div>
                                <div class="overflow-hidden">
                                    <h3 class="font-bold text-slate-900 group-hover:text-indigo-600 transition-colors truncate"><?= esc($u['display_name'] ?? $u['username']) ?></h3>
                                    <p class="text-[10px] text-slate-400 font-medium uppercase tracking-wider"><?= esc($u['role']) ?></p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-indigo-500">palette</span>
                Karya
            </h2>
        <?php endif; ?>

        <div id="art-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 md:gap-8">
            <?php if (!empty($works)): ?>
                <?php foreach ($works as $index => $work): ?>
                    <?php 
                        $delay = $index * 50; 
                        $coverUrl = base_url('image/cover/' . $work['id']);
                        if (empty($work['cover_url'])) {
                            $coverUrl = base_url('assets/img/default-cover.jpg');
                        }
                        $viewCount = number_format($work['view_count'] ?? 0);
                        if (($work['view_count'] ?? 0) >= 1000) {
                            $viewCount = round(($work['view_count'] ?? 0) / 1000, 1) . 'k';
                        }
                    ?>
                    <div class="art-card group cursor-pointer opacity-0 translate-y-4 animate-in" 
                         style="animation: 0.5s ease <?= $delay ?>ms 1 normal forwards running fadeInUp;"
                         onclick="openModal(<?= htmlspecialchars(json_encode([
                             'id'          => $work['id'],
                             'title'       => $work['title'],
                             'type'        => $work['content_type'],
                             'image'       => $coverUrl,
                             'views'       => $work['view_count'] ?? 0,
                             'viewsFmt'    => $viewCount,
                             'created_at'  => $work['created_at'] ?? '',
                             'description' => $work['description'],
                             'status'      => $work['status'],
                             'creator' => [
                                 'name'     => $work['creator_name'],
                                 'username' => $work['creator_id'] ?? '',
                             ],
                             'is_bookmarked' => $work['is_bookmarked'] ?? false,
                         ])) ?>)">
                        <div class="relative aspect-[4/3] bg-slate-100 overflow-hidden border-b border-slate-100">
                            <img src="<?= $coverUrl ?>" alt="<?= htmlspecialchars($work['title']) ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy">
                            <div class="absolute top-3 left-3 bg-white/90 backdrop-blur px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider text-[#4F46E5] shadow-sm">
                                <?php 
                                    $t = $work['content_type'];
                                    if ($t === 'novel') echo 'Novel';
                                    elseif ($t === 'light_novel') echo 'LN';
                                    elseif ($t === 'comic') echo 'Comic';
                                    elseif ($t === 'text') echo 'Teks';
                                    else echo htmlspecialchars($t);
                                ?>
                            </div>
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors duration-300"></div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-[#0F172A] text-lg leading-tight mb-1 truncate group-hover:text-[#4F46E5] transition-colors"><?= htmlspecialchars($work['title']) ?></h3>
                            <div class="flex items-center justify-between mt-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-bold text-slate-500 overflow-hidden">
                                        <?= strtoupper(substr($work['creator_name'], 0, 1)) ?>
                                    </div>
                                    <span class="text-sm text-[#475569] font-medium truncate max-w-[100px]"><?= htmlspecialchars($work['creator_name'] ?: $work['creator_username']) ?></span>
                                </div>
                                <div class="flex items-center gap-1 text-xs text-[#94A3B8]">
                                    <span class="material-symbols-outlined text-[14px]">visibility</span>
                                    <?= $viewCount ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full py-12 text-center">
                    <p class="text-slate-500">Belum ada karya yang ditemukan.</p>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!$isLoggedIn): ?>
        <!-- 5️⃣ Soft Gate / Pagination -->
        <div class="mt-20 mb-12 py-12 bg-white border border-slate-200 rounded-2xl text-center max-w-2xl mx-auto shadow-sm">
            <div class="w-12 h-12 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-4 text-[#4F46E5]">
                <span class="material-symbols-outlined">lock_open</span>
            </div>
            <h3 class="text-xl font-bold text-[#0F172A] mb-2">Ingin menjelajah lebih jauh?</h3>
            <p class="text-[#64748B] mb-6 px-4">Bergabunglah dengan komunitas NusaShare untuk mengakses ribuan karya eksklusif lainnya.</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4 px-6">
                <a href="<?= base_url('login') ?>" class="btn-primary px-8 py-2.5 rounded-full font-medium w-full sm:w-auto">Masuk ke NusaShare</a>
                <a href="<?= base_url('register') ?>" class="px-8 py-2.5 rounded-full font-medium text-[#64748B] border border-slate-200 hover:bg-slate-50 hover:text-[#0F172A] w-full sm:w-auto transition-colors">Daftar Akun</a>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <!-- Footer - hidden on mobile for logged-in (bottom nav replaces it) -->
    <footer class="<?= $isLoggedIn ? 'hidden md:block' : '' ?> bg-white border-t border-slate-200 pt-10 pb-6">
        <div class="max-w-7xl mx-auto px-4 md:px-6">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
                <div class="flex items-center gap-2">
                    <img src="<?= base_url('assets/icon/logonus.png') ?>" alt="Logo N" class="w-7 h-7">
                    <span class="font-bold text-[#0F172A]">NusaShare</span>
                </div>
                <div class="flex gap-6 text-sm text-[#64748B]">
                    <a href="#" class="hover:text-[#4F46E5]">Tentang</a>
                    <a href="#" class="hover:text-[#4F46E5]">Privasi</a>
                    <a href="#" class="hover:text-[#4F46E5]">Bantuan</a>
                </div>
            </div>
            <div class="text-center sm:text-left text-xs text-[#94A3B8]">
                &copy;<?php echo date("Y"); ?> NusaShare. Platform Kreator Indonesia.
            </div>
        </div>
    </footer>

    </div> <!-- End Content Scroll Container -->
</div> <!-- End Main Flex Wrapper if Logged In -->

    <!-- Detail Modal -->
    <div id="detail-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity opacity-0" id="modal-backdrop"></div>
        
        <!-- Modal Card -->
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto custom-scrollbar opacity-0 scale-95 transition-all duration-300 transform" id="modal-content">
            <button onclick="closeModal()" class="absolute top-4 right-4 z-10 p-2 bg-white/80 hover:bg-white rounded-full text-slate-500 hover:text-red-500 transition-colors shadow-sm backdrop-blur">
                <span class="material-symbols-outlined">close</span>
            </button>

            <div class="grid md:grid-cols-2">
                <!-- Left: Cover Image -->
                <div class="relative bg-slate-100 min-h-[300px] md:min-h-[500px] overflow-hidden flex items-center justify-center" id="modal-image-wrap">
                    <img id="modal-image" src="" alt="Cover" class="w-full h-full object-cover transition-transform duration-700">
                    <!-- Lock Overlay — only shown for guests -->
                    <?php if (!$isLoggedIn): ?>
                    <div class="absolute inset-0 flex flex-col items-center justify-center z-10 bg-black/20 backdrop-blur-[3px]">
                        <div class="bg-white/90 backdrop-blur px-6 py-4 rounded-xl shadow-lg text-center border border-white/50">
                            <span class="material-symbols-outlined text-[#4F46E5] text-3xl mb-2 block">lock</span>
                            <p class="font-bold text-slate-900 text-sm">Preview Terbatas</p>
                            <p class="text-xs text-slate-500 mt-1">Masuk untuk akses penuh</p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Right: Info -->
                <div class="p-8 md:p-10 flex flex-col h-full bg-white">
                    <div class="mb-auto">
                        <!-- Type badge -->
                        <span id="modal-type" class="inline-block px-3 py-1 rounded-full bg-indigo-50 text-[#4F46E5] text-xs font-bold uppercase tracking-wider mb-4">—</span>

                        <!-- Title -->
                        <h2 id="detail-modal-title" class="text-2xl md:text-3xl font-bold text-[#0F172A] mb-3 leading-tight">—</h2>

                        <!-- Meta: date + views -->
                        <div class="flex items-center gap-3 text-xs text-slate-400 mb-5">
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">schedule</span>
                                <span id="detail-modal-date">—</span>
                            </span>
                            <span>•</span>
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">visibility</span>
                                <span id="detail-modal-views">—</span>
                            </span>
                        </div>

                        <!-- Description -->
                        <p id="detail-modal-desc" class="text-[#475569] text-sm leading-relaxed line-clamp-5">
                            —
                        </p>
                    </div>

                    <!-- Creator + Actions -->
                    <div class="border-t border-slate-100 pt-6 mt-6">
                        <a id="modal-creator-link" href="#" class="flex items-center gap-3 mb-6 group">
                            <div id="modal-creator-avatar" class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-lg border-2 border-white shadow-sm shrink-0">?</div>
                            <div>
                                <p class="text-[10px] text-slate-400 font-medium uppercase tracking-wider">Kreator</p>
                                <h4 id="modal-creator-name" class="font-bold text-[#0F172A] group-hover:text-[#4F46E5] transition-colors">—</h4>
                            </div>
                        </a>

                        <!-- CTA -->
                        <div class="space-y-3" id="modal-cta">
                        <?php if (!$isLoggedIn): ?>
                            <a href="<?= base_url('login') ?>" class="w-full btn-primary py-3.5 rounded-xl font-bold text-sm shadow-lg shadow-indigo-500/20 flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined">key</span>
                                Masuk untuk Membuka
                            </a>
                            <a href="<?= base_url('register') ?>" class="w-full py-3 rounded-xl font-medium text-sm text-slate-600 bg-slate-50 border border-slate-200 hover:bg-slate-100 transition-colors flex items-center justify-center gap-2">
                                Daftar Gratis
                            </a>
                        <?php else: ?>
                            <a id="modal-read-btn" href="#" class="w-full btn-primary py-3.5 rounded-xl font-bold text-sm shadow-lg shadow-indigo-500/20 flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined">menu_book</span>
                                Baca Karya
                            </a>
                            <button id="modal-bookmark-btn" onclick="toggleBookmark()" class="w-full py-3 rounded-xl font-medium text-sm text-slate-600 bg-slate-50 border border-slate-200 hover:bg-slate-100 transition-colors flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-sm" id="bookmark-icon">bookmark_add</span>
                                <span id="bookmark-text">Tambahkan ke Koleksi Saya</span>
                            </button>
                        <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const modal          = document.getElementById('detail-modal');
        const modalContent   = document.getElementById('modal-content');
         const modalBackdrop  = document.getElementById('modal-backdrop');
         const BASE_URL       = '<?= base_url() ?>';
         const IS_LOGGED_IN   = <?= $isLoggedIn ? 'true' : 'false' ?>;
         let currentWorkId    = null;
         let isProcessing     = false;

        // --- Relative date helper ---
        function relativeDate(dateStr) {
            if (!dateStr) return 'Tanggal tidak diketahui';
            const d     = new Date(dateStr.replace(' ', 'T'));
            const now   = new Date();
            const diff  = Math.floor((now - d) / 1000);
            if (diff < 60)     return 'Baru saja';
            if (diff < 3600)   return Math.floor(diff / 60) + ' menit lalu';
            if (diff < 86400)  return Math.floor(diff / 3600) + ' jam lalu';
            if (diff < 2592000) return Math.floor(diff / 86400) + ' hari lalu';
            if (diff < 31536000) return Math.floor(diff / 2592000) + ' bulan lalu';
            return Math.floor(diff / 31536000) + ' tahun lalu';
        }

        // --- Format view count ---
        function fmtViews(n) {
            if (n >= 1000000) return (n / 1000000).toFixed(1).replace('.0','') + 'jt';
            if (n >= 1000)    return (n / 1000).toFixed(1).replace('.0','') + 'k';
            return n.toLocaleString('id');
        }

        // --- Type label ---
        const typeLabel = { 
            text: 'Novel / Teks', 
            image: 'Gambar', 
            pdf: 'PDF',
            novel: 'Novel',
            light_novel: 'Light Novel',
            comic: 'Comic'
        };

        function openModal(art) {
            // Cover image
            document.getElementById('modal-image').src = art.image;

            // Type badge
            document.getElementById('modal-type').textContent = typeLabel[art.type] || art.type;

            // Title
            document.getElementById('detail-modal-title').textContent = art.title;

            // Date + views
            document.getElementById('detail-modal-date').textContent  = relativeDate(art.created_at);
            document.getElementById('detail-modal-views').textContent = fmtViews(art.views) + ' tayangan';

            // Description
            const descEl = document.getElementById('detail-modal-desc');
            descEl.textContent = art.description || 'Tidak ada deskripsi.';

            // Creator avatar initial
            const initial = (art.creator.name || '?').charAt(0).toUpperCase();
            document.getElementById('modal-creator-avatar').textContent = initial;

            // Creator name + link
            document.getElementById('modal-creator-name').textContent = art.creator.name || '—';
            const creatorLink = document.getElementById('modal-creator-link');
            creatorLink.href = art.creator.username ? BASE_URL + 'creator/' + art.creator.username : '#';

             // Read button (for logged-in)
             const readBtn = document.getElementById('modal-read-btn');
             if (readBtn) {
                 readBtn.href = BASE_URL + 'works/' + art.id;
             }

             // Handle bookmark button state
             currentWorkId = art.id; // Store current work ID for bookmark toggle
             const bookmarkBtn = document.getElementById('modal-bookmark-btn');
             if (bookmarkBtn) {
                 updateBookmarkUI(art.is_bookmarked);
             }

            // Show
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalBackdrop.classList.remove('opacity-0');
                modalContent.classList.remove('opacity-0', 'scale-95');
                modalContent.classList.add('opacity-100', 'scale-100');
            }, 10);

            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            modalBackdrop.classList.add('opacity-0');
            modalContent.classList.remove('opacity-100', 'scale-100');
            modalContent.classList.add('opacity-0', 'scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }, 300);
        }

         function updateBookmarkUI(isBookmarked) {
            const btn = document.getElementById('modal-bookmark-btn');
            const icon = document.getElementById('bookmark-icon');
            const text = document.getElementById('bookmark-text');
            
            if (isBookmarked) {
                btn.dataset.bookmarked = "true";
                btn.classList.add('bg-indigo-50', 'text-indigo-600', 'border-indigo-100');
                btn.classList.remove('bg-slate-50', 'text-slate-600', 'border-slate-200');
                icon.textContent = 'bookmark_added';
                text.textContent = 'Dalam Koleksi';
            } else {
                btn.dataset.bookmarked = "false";
                btn.classList.remove('bg-indigo-50', 'text-indigo-600', 'border-indigo-100');
                btn.classList.add('bg-slate-50', 'text-slate-600', 'border-slate-200');
                icon.textContent = 'bookmark_add';
                text.textContent = 'Tambahkan ke Koleksi Saya';
            }
        }

        async function toggleBookmark() {
            if (isProcessing || !currentWorkId) return;
            isProcessing = true;

            const btn = document.getElementById('modal-bookmark-btn');
            const isBookmarked = btn.dataset.bookmarked === "true";
            const url = isBookmarked ? `${BASE_URL}bookmark/${currentWorkId}/remove` : `${BASE_URL}bookmark/${currentWorkId}`;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const result = await response.json();

                if (result.status === 'success') {
                    updateBookmarkUI(!isBookmarked);
                    // Also update the local data in the grid if needed, 
                    // but for now, simple toast or just UI change is enough.
                } else {
                    alert(result.message || 'Terjadi kesalahan.');
                }
            } catch (error) {
                console.error('Error toggling bookmark:', error);
                alert('Gagal menghubungkan ke server.');
            } finally {
                isProcessing = false;
            }
        }

        modalBackdrop.onclick = closeModal;
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

        // Grid animation
        const styleSheet = document.createElement('style');
        styleSheet.innerText = `@keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }`;
        document.head.appendChild(styleSheet);
    </script>

   

    <script>
        window.nusaAppData = {
            baseUrl: '<?= base_url() ?>/'
        };
    </script>
    <script src="<?= base_url('assets/js/main.js') ?>"></script>
    <script src="<?= base_url('public/assets/js/notifications.js') ?>"></script>
</body></html>