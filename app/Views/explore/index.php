<?php /** INTEGRATED SIDEBAR & DYNAMIC HEADER **/ ?>
<html lang="id"><head>

    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Jelajahi Karya - NusaShare</title>

    <!-- Fonts: Inter -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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
            background-color: #F8FAFC;
            color: var(--lp-text-main);
            overflow-x: hidden;
        }

        .sidebar-link.active { background-color: #EEF2FF; color: #4F46E5; border-right: 4px solid #4F46E5; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

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

        /* Spotlight */
        .explore-spotlight {
            background:
                linear-gradient(135deg, rgba(15, 23, 42, 0.94), rgba(49, 46, 129, 0.88)),
                var(--spotlight-image);
            background-size: cover;
            background-position: center;
        }

        /* Work Card */
        .work-card {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease, border-color 0.3s ease;
        }
        .work-card:hover {
            transform: translateY(-4px);
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .hide-scrollbar::-webkit-scrollbar { display: none; }

        /* Modal */
        .blur-reveal { filter: blur(12px); transition: filter 0.5s ease; }
        @keyframes shimmer {
            0% { background-position: -1000px 0; }
            100% { background-position: 1000px 0; }
        }
        .skeleton {
            background: linear-gradient(to right, #f1f5f9 4%, #e2e8f0 25%, #f1f5f9 36%);
            background-size: 1000px 100%;
            animation: shimmer 2s infinite linear;
        }
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
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

    <!-- Top Bar / Header -->
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
                                <img src="<?= base_url('image/profile/' . $profile['profile_image']) ?>" alt="Avatar" class="w-full h-full object-cover">
                            <?php else: ?>
                                <?= strtoupper(substr($username, 0, 1)) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content Container (Scrollable if Logged In) -->
    <div class="<?= $isLoggedIn ? 'flex-1 overflow-y-auto pb-20 lg:pb-0' : 'pb-20 lg:pb-0' ?>">

        <!-- Mobile Search -->
        <section class="lg:hidden bg-[#F8FAFC] px-4 <?= $isLoggedIn ? 'pt-4' : 'pt-24' ?> pb-2">
            <form action="<?= base_url('search') ?>" method="GET" class="flex items-center gap-2 bg-white border border-slate-200 rounded-2xl px-4 py-3 shadow-sm focus-within:border-indigo-300 focus-within:ring-4 focus-within:ring-indigo-50 transition-all">
                <span class="material-symbols-outlined text-slate-400 text-xl">search</span>
                <input type="text" name="q" value="<?= esc($query ?? '') ?>" placeholder="Cari karya atau kreator..." class="min-w-0 flex-1 bg-transparent border-none outline-none text-sm text-slate-700 placeholder:text-slate-400">
                <?php if (!empty($query ?? '')): ?>
                    <a href="<?= base_url('explore') ?>" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center" aria-label="Hapus pencarian">
                        <span class="material-symbols-outlined text-base">close</span>
                    </a>
                <?php endif; ?>
            </form>
        </section>

        <?php
            $currentSort = $sort ?? 'latest';
            $currentType = $type ?? '';
            $currentGenre = $genre ?? '';
            $viewParam   = $_GET['view'] ?? 'grid';
            $currentView = in_array($viewParam, ['grid', 'list'], true) ? $viewParam : 'grid';

            // ── Hero Spotlight Dinamis ──────────────────────────────────────────
            $sl        = $spotlightWork ?? null;
            $slCover   = $sl ? base_url('image/cover/' . $sl['id']) : base_url('assets/icon/logonuss.png');
            $slTitle   = $sl['title']       ?? 'Jelajahi Karya Terbaik';
            $slDesc    = $sl['description'] ?? 'Temukan ribuan karya kreatif dari para kreator Indonesia.';
            $slType    = $sl['content_type'] ?? '';
            $slCreator = $sl['creator_name'] ?? '';
            $slViews   = (int)($sl['view_count'] ?? 0);

            if (!function_exists('exploreUrl')) {
                function exploreUrl(string $type = '', string $sort = 'latest', string $view = 'grid', string $genre = ''): string {
                    $params = [];
                    $base = match ($type) {
                        'story' => base_url('explore/story'),
                        'image' => base_url('explore/gallery'),
                        default => base_url('explore'),
                    };
                    if (!empty($type) && !in_array($type, ['story', 'image'], true)) $params['type'] = $type;
                    if ($sort !== 'latest') $params['sort'] = $sort;
                    if ($view !== 'grid') $params['view'] = $view;
                    if (!empty($genre)) $params['genre'] = $genre;
                    return empty($params) ? $base : $base . '?' . http_build_query($params);
                }
            }

            $types = [
                ''            => 'Semua',
                'story'       => 'Rak Buku',
                'novel'       => 'Novel',
                'light_novel' => 'Light Novel',
                'comic'       => 'Komik',
                'image'       => 'Galeri Seni',
            ];
            $sortLabels = [
                'latest'      => 'Terbaru',
                'popular'     => 'Terpopuler',
                'recommended' => 'Yang mungkin Anda sukai',
            ];

            // ── Kelompokkan works per tipe ──────────────────────────────────────
            $worksAll = $works ?? [];
            // comic masuk Rak Buku (bukan Galeri)
            $bookTypes    = ['novel', 'light_novel', 'comic'];
            $galleryTypes = ['image'];

            if (!empty($currentType)) {
                $cfgMap = [
                    'novel'       => ['label' => 'Rak Buku (Novel, LN & Komik)', 'icon' => 'auto_stories', 'color' => '#6366F1', 'bg' => '#EEF2FF', 'portrait' => true],
                    'light_novel' => ['label' => 'Rak Buku (Novel, LN & Komik)', 'icon' => 'auto_stories', 'color' => '#6366F1', 'bg' => '#EEF2FF', 'portrait' => true],
                    'comic'       => ['label' => 'Rak Buku (Novel, LN & Komik)', 'icon' => 'auto_stories', 'color' => '#6366F1', 'bg' => '#EEF2FF', 'portrait' => true],
                    'story'       => ['label' => 'Rak Buku (Novel, LN & Komik)', 'icon' => 'auto_stories', 'color' => '#6366F1', 'bg' => '#EEF2FF', 'portrait' => true],
                    'image'       => ['label' => 'Galeri Seni',                  'icon' => 'palette',      'color' => '#F59E0B', 'bg' => '#FFFBEB', 'portrait' => false],
                ];
                $cfg = $cfgMap[$currentType] ?? ['label' => 'Karya', 'icon' => 'grid_view', 'color' => '#6366F1', 'bg' => '#EEF2FF', 'portrait' => false];
                $groupedWorks = [
                    $currentType => [
                        'label'    => $cfg['label'],    'icon'    => $cfg['icon'],
                        'color'    => $cfg['color'],    'bg'      => $cfg['bg'],
                        'portrait' => $cfg['portrait'], 'works'   => $worksAll,
                    ]
                ];
            } else {
                $bookGroup    = [];
                $galleryGroup = [];
                $otherGroup   = [];
                foreach ($worksAll as $w) {
                    $ct = $w['content_type'] ?? '';
                    if (in_array($ct, $bookTypes))    $bookGroup[]    = $w;
                    elseif (in_array($ct, $galleryTypes)) $galleryGroup[] = $w;
                    else                               $otherGroup[]   = $w;
                }
                $groupedWorks = [];
                if (!empty($bookGroup))    $groupedWorks['books']   = ['label' => 'Rak Buku (Novel, LN & Komik)', 'icon' => 'auto_stories', 'color' => '#6366F1', 'bg' => '#EEF2FF', 'portrait' => true,  'works' => $bookGroup];
                if (!empty($galleryGroup)) $groupedWorks['gallery'] = ['label' => 'Galeri Seni',                  'icon' => 'palette',      'color' => '#F59E0B', 'bg' => '#FFFBEB', 'portrait' => false, 'works' => $galleryGroup];
                if (!empty($otherGroup))   $groupedWorks['other']   = ['label' => 'Konten Lainnya',               'icon' => 'grid_view',    'color' => '#64748B', 'bg' => '#F1F5F9', 'portrait' => false, 'works' => $otherGroup];
            }

            // ── Helper functions ────────────────────────────────────────────────
            function xTypeLabel(string $t): string {
                return match($t) { 'novel' => 'Novel', 'light_novel' => 'LN', 'comic' => 'Komik', 'image' => 'Art', 'text' => 'Teks', default => strtoupper($t) };
            }
            function xTypeBadge(string $t): string {
                return match($t) { 'novel' => 'bg-indigo-600', 'light_novel' => 'bg-violet-600', 'comic' => 'bg-amber-500', 'image' => 'bg-emerald-600', default => 'bg-slate-600' };
            }
            function xFmtViews(int $n): string {
                if ($n >= 1000000) return round($n/1000000,1).'jt';
                if ($n >= 1000)    return round($n/1000,1).'k';
                return number_format($n);
            }
            function xFmtPrice($isPaid, $price): string {
                if (!$isPaid || !$price) return 'Gratis';
                return number_format((int)$price).' CC';
            }
            function xRating(int $v): float {
                if ($v <= 0)     return 3.0;
                if ($v >= 10000) return 5.0;
                if ($v >= 5000)  return 4.8;
                if ($v >= 1000)  return 4.5;
                if ($v >= 500)   return 4.2;
                if ($v >= 100)   return 4.0;
                if ($v >= 50)    return 3.8;
                return 3.5;
            }
        ?>

        <!-- ── Hero Spotlight ──────────────────────────────────────────────── -->
        <section class="<?= $isLoggedIn ? 'pt-5' : 'pt-5 lg:pt-24' ?> px-4 md:px-6">
            <div class="max-w-7xl mx-auto">
                <div class="explore-spotlight rounded-2xl overflow-hidden border border-slate-900/10 shadow-md" style="--spotlight-image: url('<?= $slCover ?>');">
                    <div class="px-5 py-8 sm:px-10 sm:py-12 lg:px-14 lg:py-16 min-h-[280px] flex items-end">
                        <div class="max-w-2xl text-white">
                            <div class="flex items-center gap-3 mb-4 flex-wrap">
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-wider backdrop-blur border border-white/20">
                                    <span class="material-symbols-outlined text-sm">auto_awesome</span>
                                    Hero Spotlight
                                </span>
                                <?php if ($slType): ?>
                                <span class="inline-flex items-center gap-1 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-wider backdrop-blur border border-white/20">
                                    <span class="material-symbols-outlined text-sm">book</span>
                                    <?= xTypeLabel($slType) ?>
                                </span>
                                <?php endif; ?>
                            </div>
                            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold leading-tight tracking-tight drop-shadow-sm">
                                <?= htmlspecialchars($slTitle) ?>
                            </h1>
                            <?php if ($slCreator): ?>
                            <p class="mt-2 text-sm text-white/70 font-medium">oleh <?= htmlspecialchars($slCreator) ?></p>
                            <?php endif; ?>
                            <p class="mt-3 text-sm sm:text-base leading-relaxed text-indigo-100 max-w-xl line-clamp-2">
                                <?= htmlspecialchars($slDesc) ?>
                            </p>
                            <div class="mt-5 flex flex-wrap items-center gap-3">
                                <?php if ($sl): ?>
                                    <button type="button" onclick="openModal(<?= htmlspecialchars(json_encode([
                                        'id'          => $sl['id'],
                                        'title'       => $sl['title'],
                                        'type'        => $sl['content_type'],
                                        'image'       => $slCover,
                                        'views'       => $slViews,
                                        'viewsFmt'    => xFmtViews($slViews),
                                        'created_at'  => $sl['created_at'] ?? '',
                                        'description' => $sl['description'] ?? '',
                                        'status'      => $sl['status'] ?? '',
                                        'is_paid'     => !empty($sl['is_paid']),
                                        'price'       => $sl['price'] ?? 0,
                                        'creator'     => ['name' => $sl['creator_name'] ?? '', 'username' => $sl['creator_username'] ?? ''],
                                        'is_bookmarked' => $sl['is_bookmarked'] ?? false,
                                    ])) ?>)" class="inline-flex items-center gap-2 rounded-full bg-white px-5 py-2.5 text-sm font-bold text-slate-950 shadow-md hover:bg-indigo-50 transition-colors">
                                        <span class="material-symbols-outlined text-base">visibility</span>
                                        Lihat Detail
                                    </button>
                                    <a href="<?= base_url('works/' . $sl['id']) ?>" class="inline-flex items-center gap-2 rounded-full bg-white/15 backdrop-blur border border-white/30 px-5 py-2.5 text-sm font-semibold text-white hover:bg-white/25 transition-colors">
                                        <span class="material-symbols-outlined text-base">menu_book</span>
                                        Baca Sekarang
                                    </a>
                                <?php else: ?>
                                    <a href="<?= base_url('explore') ?>" class="inline-flex items-center gap-2 rounded-full bg-white px-5 py-2.5 text-sm font-bold text-slate-950 shadow-md hover:bg-indigo-50 transition-colors">
                                        <span class="material-symbols-outlined text-base">search</span>
                                        Jelajahi Karya
                                    </a>
                                <?php endif; ?>
                                <span class="text-xs sm:text-sm text-white/60"><?= xFmtViews($slViews) ?> tayangan</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── Filter Bar ──────────────────────────────────────────────────── -->
        <section class="<?= $isLoggedIn ? 'sticky top-0' : 'sticky top-16' ?> z-30 bg-[#F8FAFC]/95 backdrop-blur-sm border-b border-slate-200/60 py-3 mt-6">
            <div class="max-w-7xl mx-auto px-4 md:px-6 flex flex-col xl:flex-row justify-between items-start xl:items-center gap-3">
                <div class="flex gap-2 overflow-x-auto pb-1 xl:pb-0 w-full xl:w-auto hide-scrollbar">
                    <?php foreach ($types as $typeKey => $typeLabel2): ?>
                        <?php $isActive = ($currentType === $typeKey); ?>
                        <a href="<?= exploreUrl($typeKey, $currentSort, $currentView, $currentGenre) ?>"
                           class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors <?= $isActive ? 'bg-[#0F172A] text-white shadow-sm ring-1 ring-[#0F172A]' : 'bg-white text-[#64748B] border border-slate-200 hover:border-[#4F46E5] hover:text-[#4F46E5]' ?>">
                            <?= $typeLabel2 ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full xl:w-auto">
                    <label class="flex items-center gap-2 text-sm text-[#64748B]">
                        <span class="shrink-0">Genre:</span>
                        <select onchange="window.location.href=this.value" class="w-full sm:w-40 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-[#0F172A] outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-50">
                            <?php 
                                $genresList = ['' => 'Semua', 'Fantasy' => 'Fantasy', 'Sci-Fi' => 'Sci-Fi', 'Horror' => 'Horror', 'Mystery' => 'Mystery', 'Slice of Life' => 'Slice of Life', 'Literary' => 'Literary', 'Romance' => 'Romance', 'Action' => 'Action', 'Comedy' => 'Comedy', 'Drama' => 'Drama'];
                                foreach ($genresList as $gKey => $gName): 
                            ?>
                                <option value="<?= exploreUrl($currentType, $currentSort, $currentView, $gKey) ?>" <?= $currentGenre === $gKey ? 'selected' : '' ?>><?= $gName ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label class="flex items-center gap-2 text-sm text-[#64748B]">
                        <span class="shrink-0">Urutkan:</span>
                        <select onchange="window.location.href=this.value" class="w-full sm:w-48 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-[#0F172A] outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-50">
                            <?php foreach ($sortLabels as $sortKey => $sortName): ?>
                                <option value="<?= exploreUrl($currentType, $sortKey, $currentView, $currentGenre) ?>" <?= $currentSort === $sortKey ? 'selected' : '' ?>><?= $sortName ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </div>
            </div>
        </section>

    <!-- ── Main Content ──────────────────────────────────────────────────── -->
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
                        Akun User &amp; Kreator
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        <?php foreach ($users as $u): ?>
                            <a href="<?= base_url('user/' . $u['username']) ?>" class="flex items-center gap-4 bg-white p-4 rounded-2xl border border-slate-100 hover:border-indigo-200 hover:shadow-md transition-all group">
                                <div class="w-12 h-12 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold border-2 border-white shadow-sm shrink-0 overflow-hidden">
                                    <?php if (!empty($u['profile_image'])): ?>
                                        <img src="<?= base_url('image/profile/' . $u['profile_image']) ?>" alt="" class="w-full h-full object-cover">
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

        <!-- ── Grouped Work Sections ──────────────────────────────────────── -->
        <?php if (!empty($groupedWorks)): ?>
            <?php foreach ($groupedWorks as $groupKey => $group): ?>
            <section class="mb-14">
                <!-- Section Header -->
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-sm" style="background:<?= $group['bg'] ?>">
                            <span class="material-symbols-outlined text-lg" style="color:<?= $group['color'] ?>"><?= $group['icon'] ?></span>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900"><?= htmlspecialchars($group['label']) ?></h2>
                            <p class="text-xs text-slate-400 mt-0.5"><?= count($group['works']) ?> Karya</p>
                        </div>
                    </div>
                    <?php
                        $moreUrl = $groupKey === 'books'
                            ? base_url('explore/story')
                            : ($groupKey === 'gallery' ? base_url('explore/gallery') : '');
                    ?>
                    <?php if (!empty($moreUrl) && empty($currentType) && !isset($query)): ?>
                        <a href="<?= $moreUrl ?>" class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-600 shadow-sm transition-colors hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600">
                            Lihat selengkapnya
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Works Grid — 3 columns -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 gap-4 sm:gap-5">
                    <?php foreach ($group['works'] as $idx => $work): ?>
                    <?php
                        $coverUrl  = base_url('image/cover/' . $work['id']);
                        if (empty($work['cover_url'])) $coverUrl = base_url('assets/icon/logonuss.png');
                        $wViews    = (int)($work['view_count'] ?? 0);
                        $wRating   = xRating($wViews);
                        $wIsPaid   = !empty($work['is_paid']);
                        $wPrice    = xFmtPrice($wIsPaid, $work['price'] ?? 0);
                        $wType     = $work['content_type'] ?? '';
                        $wBadge    = xTypeBadge($wType);
                        $wLabel    = xTypeLabel($wType);
                        $wName     = $work['creator_name'] ?: ($work['creator_username'] ?? '?');
                        $delay     = $idx * 40;
                        // portrait untuk novel/light_novel/comic, landscape untuk image
                        $isPortrait  = $group['portrait'] ?? false;
                        $aspectClass = $isPortrait ? 'aspect-[2/3]' : 'aspect-[16/9]';
                    ?>
                    <div class="work-card group cursor-pointer bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-sm hover:shadow-lg hover:border-indigo-200 opacity-0"
                         style="animation: 0.45s ease <?= $delay ?>ms 1 normal forwards running fadeInUp;"
                         onclick="openModal(<?= htmlspecialchars(json_encode([
                             'id'          => $work['id'],
                             'title'       => $work['title'],
                             'type'        => $wType,
                             'image'       => $coverUrl,
                             'views'       => $wViews,
                             'viewsFmt'    => xFmtViews($wViews),
                             'created_at'  => $work['created_at'] ?? '',
                             'description' => $work['description'] ?? '',
                             'status'      => $work['status'] ?? '',
                             'is_paid'     => $wIsPaid,
                             'price'       => $work['price'] ?? 0,
                             'creator'     => ['name' => $wName, 'username' => $work['creator_username'] ?? ''],
                             'is_bookmarked' => $work['is_bookmarked'] ?? false,
                         ])) ?>)">

                        <!-- Cover Image -->
                        <div class="relative <?= $aspectClass ?> bg-slate-100 overflow-hidden">
                            <img src="<?= $coverUrl ?>"
                                 alt="<?= htmlspecialchars($work['title']) ?>"
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                 loading="lazy" decoding="async">
                            <!-- Type Badge (top-left) -->
                            <span class="absolute top-2.5 left-2.5 <?= $wBadge ?> text-white text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md shadow-sm">
                                <?= $wLabel ?>
                            </span>
                            <!-- Price Badge (top-right) -->
                            <span class="absolute top-2.5 right-2.5 <?= $wIsPaid ? 'bg-slate-900/80' : 'bg-emerald-600' ?> text-white text-[10px] font-bold px-2 py-0.5 rounded-md shadow-sm backdrop-blur">
                                <?= $wPrice ?>
                            </span>
                            <!-- Genre Badge (bottom-left) -->
                            <?php if (!empty($work['genre']) && $isPortrait): ?>
                            <span class="absolute bottom-2.5 left-2.5 bg-black/60 text-white text-[10px] font-bold px-2 py-0.5 rounded-md shadow-sm backdrop-blur">
                                <?= htmlspecialchars($work['genre']) ?>
                            </span>
                            <?php endif; ?>
                            <div class="absolute inset-0 bg-indigo-900/0 group-hover:bg-indigo-900/10 transition-colors duration-300"></div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-3">
                            <h3 class="font-bold text-slate-900 text-sm leading-snug line-clamp-2 group-hover:text-indigo-600 transition-colors mb-1">
                                <?= htmlspecialchars($work['title']) ?>
                            </h3>
                            <p class="text-xs text-slate-500 truncate mb-2.5">
                                <span class="material-symbols-outlined text-[12px] align-middle mr-0.5 text-slate-400">person</span>
                                <?= htmlspecialchars($wName) ?>
                            </p>
                            <!-- Bottom: Star Rating + Price -->
                            <div class="flex items-center justify-between gap-1">
                                <!-- Stars -->
                                <div class="flex items-center gap-1">
                                    <div class="flex items-center gap-0.5">
                                        <?php
                                            $full  = (int)floor($wRating);
                                            $half  = ($wRating - $full) >= 0.4;
                                            $empty = 5 - $full - ($half ? 1 : 0);
                                            for ($s = 0; $s < $full; $s++): ?>
                                                <svg class="w-3 h-3 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        <?php endfor; ?>
                                        <?php if ($half): ?>
                                            <svg class="w-3 h-3 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M10 1.5l1.922 5.915H18l-4.937 3.586 1.883 5.79L10 13.26l-4.945 3.531 1.883-5.79L2 7.415h6.078L10 1.5z"/></svg>
                                        <?php endif; ?>
                                        <?php for ($s = 0; $s < $empty; $s++): ?>
                                            <svg class="w-3 h-3 text-slate-200" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="text-[10px] font-bold text-amber-500"><?= number_format($wRating, 1) ?></span>
                                </div>
                                <!-- Price chip -->
                                <span class="text-[10px] font-bold <?= $wIsPaid ? 'text-indigo-600 bg-indigo-50 border border-indigo-100' : 'text-emerald-600 bg-emerald-50 border border-emerald-100' ?> px-2 py-0.5 rounded-full">
                                    <?= $wPrice ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="py-20 text-center">
                <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-3xl text-slate-400">search_off</span>
                </div>
                <p class="text-slate-500 font-medium">Belum ada karya yang ditemukan.</p>
                <a href="<?= base_url('explore') ?>" class="inline-flex items-center gap-2 mt-4 text-sm text-indigo-600 hover:underline">
                    <span class="material-symbols-outlined text-base">arrow_back</span>
                    Lihat semua karya
                </a>
            </div>
        <?php endif; ?>

        <?php if (!$isLoggedIn): ?>
        <!-- Soft Gate -->
        <div class="mt-16 mb-12 py-12 bg-white border border-slate-200 rounded-2xl text-center max-w-2xl mx-auto shadow-sm">
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

    <!-- Footer -->
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

    <!-- ── Detail Modal ────────────────────────────────────────────────────── -->
    <div id="detail-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity opacity-0" id="modal-backdrop"></div>

        <!-- Modal Card -->
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[92dvh] overflow-y-auto custom-scrollbar opacity-0 scale-95 transition-all duration-300 transform" id="modal-content">
            <button onclick="closeModal()" class="absolute top-4 right-4 z-10 p-2 bg-white/80 hover:bg-white rounded-full text-slate-500 hover:text-red-500 transition-colors shadow-sm backdrop-blur">
                <span class="material-symbols-outlined">close</span>
            </button>

            <div class="grid md:grid-cols-2">
                <!-- Left: Cover Image -->
                <div class="relative bg-slate-100 min-h-[260px] max-h-[42dvh] md:min-h-[500px] md:max-h-none overflow-hidden flex items-center justify-center" id="modal-image-wrap">
                    <img id="modal-image" src="" alt="Cover" class="w-full h-full object-cover transition-transform duration-700">
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
                <div class="p-5 sm:p-8 md:p-10 flex flex-col h-full bg-white">
                    <div class="mb-auto">
                        <span id="modal-type" class="inline-block px-3 py-1 rounded-full bg-indigo-50 text-[#4F46E5] text-xs font-bold uppercase tracking-wider mb-4">—</span>
                        <h2 id="detail-modal-title" class="text-2xl md:text-3xl font-bold text-[#0F172A] mb-3 leading-tight">—</h2>
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
                        <p id="detail-modal-desc" class="text-[#475569] text-sm leading-relaxed line-clamp-5">—</p>
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
        const modal         = document.getElementById('detail-modal');
        const modalContent  = document.getElementById('modal-content');
        const modalBackdrop = document.getElementById('modal-backdrop');
        const BASE_URL      = '<?= base_url() ?>';
        const IS_LOGGED_IN  = <?= $isLoggedIn ? 'true' : 'false' ?>;
        let currentWorkId   = null;
        let isProcessing    = false;

        function relativeDate(dateStr) {
            if (!dateStr) return 'Tanggal tidak diketahui';
            const d    = new Date(dateStr.replace(' ', 'T'));
            const now  = new Date();
            const diff = Math.floor((now - d) / 1000);
            if (diff < 60)      return 'Baru saja';
            if (diff < 3600)    return Math.floor(diff / 60) + ' menit lalu';
            if (diff < 86400)   return Math.floor(diff / 3600) + ' jam lalu';
            if (diff < 2592000) return Math.floor(diff / 86400) + ' hari lalu';
            if (diff < 31536000)return Math.floor(diff / 2592000) + ' bulan lalu';
            return Math.floor(diff / 31536000) + ' tahun lalu';
        }

        function fmtViews(n) {
            if (n >= 1000000) return (n / 1000000).toFixed(1).replace('.0','') + 'jt';
            if (n >= 1000)    return (n / 1000).toFixed(1).replace('.0','') + 'k';
            return n.toLocaleString('id');
        }

        const typeLabelMap = {
            text: 'Novel / Teks', image: 'Art & Image', pdf: 'PDF',
            novel: 'Novel', light_novel: 'Light Novel', comic: 'Komik'
        };

        function openModal(art) {
            document.getElementById('modal-image').src = art.image;
            document.getElementById('modal-type').textContent = typeLabelMap[art.type] || art.type;
            document.getElementById('detail-modal-title').textContent = art.title;
            document.getElementById('detail-modal-date').textContent  = relativeDate(art.created_at);
            document.getElementById('detail-modal-views').textContent = fmtViews(art.views) + ' tayangan';
            document.getElementById('detail-modal-desc').textContent  = art.description || 'Tidak ada deskripsi.';

            const initial = (art.creator.name || '?').charAt(0).toUpperCase();
            document.getElementById('modal-creator-avatar').textContent = initial;
            document.getElementById('modal-creator-name').textContent   = art.creator.name || '—';
            const creatorLink = document.getElementById('modal-creator-link');
            creatorLink.href = art.creator.username ? BASE_URL + 'creator/' + art.creator.username : '#';

            const readBtn = document.getElementById('modal-read-btn');
            if (readBtn) readBtn.href = BASE_URL + 'works/' + art.id;

            currentWorkId = art.id;
            const bookmarkBtn = document.getElementById('modal-bookmark-btn');
            if (bookmarkBtn) updateBookmarkUI(art.is_bookmarked);

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
            const btn  = document.getElementById('modal-bookmark-btn');
            const icon = document.getElementById('bookmark-icon');
            const text = document.getElementById('bookmark-text');
            if (!btn) return;
            if (isBookmarked) {
                btn.dataset.bookmarked = 'true';
                btn.classList.add('bg-indigo-50', 'text-indigo-600', 'border-indigo-100');
                btn.classList.remove('bg-slate-50', 'text-slate-600', 'border-slate-200');
                icon.textContent = 'bookmark_added';
                text.textContent = 'Dalam Koleksi';
            } else {
                btn.dataset.bookmarked = 'false';
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
            const isBookmarked = btn.dataset.bookmarked === 'true';
            const url = isBookmarked ? `${BASE_URL}bookmark/${currentWorkId}/remove` : `${BASE_URL}bookmark/${currentWorkId}`;
            try {
                const response = await fetch(url, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const result = await response.json();
                if (result.status === 'success') {
                    updateBookmarkUI(!isBookmarked);
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
    </script>

    <script>
        window.nusaAppData = { baseUrl: '<?= base_url() ?>/' };
    </script>
    <script src="<?= base_url('assets/js/main.js') ?>"></script>
    <script src="<?= base_url('assets/js/notifications.js') ?>"></script>
</body></html>
