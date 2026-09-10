<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= $title ?? 'Statistik Kreator - NusaShare' ?></title>
    <meta name="description" content="Pantau performa karya, pertumbuhan pengikut, dan tingkat interaksi pembaca secara real-time.">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,300;0,14..32,400;0,14..32,500;0,14..32,600;0,14..32,700;0,14..32,800;0,14..32,900;1,14..32,400&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" rel="stylesheet">
    <link rel="icon" href="<?= base_url('assets/icon/logonus.png') ?>" type="image/x-icon">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <!-- Chart.js date adapter -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3.0.0/dist/chartjs-adapter-date-fns.bundle.min.js"></script>

    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #F0F4FF; }

        /* ── Scrollbar ────────────────────────────────── */
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 99px; }

        /* ── Stat Cards ───────────────────────────────── */
        .stat-card {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid rgba(99,102,241,0.08);
            transition: transform 0.25s cubic-bezier(.34,1.56,.64,1), box-shadow 0.25s ease;
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.6) 0%, transparent 60%);
            pointer-events: none;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px -10px rgba(99,102,241,0.15);
        }

        /* ── Score Ring ───────────────────────────────── */
        .score-ring-wrapper {
            position: relative;
            width: 88px;
            height: 88px;
            flex-shrink: 0;
        }
        .score-ring-wrapper svg { transform: rotate(-90deg); }
        .score-ring-wrapper .score-text {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        /* ── Tab Pills ────────────────────────────────── */
        .chart-tab {
            padding: 6px 14px;
            font-size: 11px;
            font-weight: 700;
            border-radius: 99px;
            cursor: pointer;
            transition: all 0.2s;
            border: 1.5px solid transparent;
            color: #64748B;
            background: transparent;
        }
        .chart-tab.active {
            background: #4F46E5;
            color: #fff;
            border-color: #4F46E5;
        }
        .chart-tab:not(.active):hover {
            background: #EEF2FF;
            border-color: #C7D2FE;
            color: #4F46E5;
        }

        /* ── Progress Bar ─────────────────────────────── */
        .prog-bar {
            height: 6px;
            border-radius: 99px;
            background: #E2E8F0;
            overflow: hidden;
        }
        .prog-bar-fill {
            height: 100%;
            border-radius: 99px;
            transition: width 1.2s cubic-bezier(.23,1,.32,1);
        }

        /* ── Trend Badge ──────────────────────────────── */
        .trend-up   { background: #ECFDF5; color: #059669; }
        .trend-down { background: #FFF1F2; color: #E11D48; }
        .trend-neu  { background: #F1F5F9; color: #64748B; }

        /* ── Work Table Row ───────────────────────────── */
        .work-row {
            transition: background 0.15s;
        }
        .work-row:hover { background: #F8FAFF; }

        /* ── Engagement Badge ─────────────────────────── */
        .eng-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 10px;
            border-radius: 99px;
            font-size: 11px;
            font-weight: 700;
        }

        /* ── Top Banner ───────────────────────────────── */
        .top-banner {
            background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 50%, #EC4899 100%);
            position: relative;
            overflow: hidden;
        }
        .top-banner::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
            top: -80px;
            right: -60px;
        }
        .top-banner::after {
            content: '';
            position: absolute;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
            bottom: -50px;
            left: 20%;
        }

        /* ── Shine animation ──────────────────────────── */
        @keyframes shine {
            from { background-position: 200% center; }
            to   { background-position: -200% center; }
        }
        .shimmer-text {
            background: linear-gradient(90deg, #fff 20%, #c7d2fe 40%, #fff 60%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: shine 4s linear infinite;
        }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <?php
// PHP lookup tables used across multiple sections of this view
$typeLabels = [
    'text'  => 'Tulisan',
    'comic' => 'Komik',
    'image' => 'Gambar',
    'audio' => 'Audio',
    'video' => 'Video',
];
$typeColors = [
    'text'  => '#4F46E5',
    'comic' => '#EC4899',
    'image' => '#10B981',
    'audio' => '#F59E0B',
    'video' => '#6366F1',
];
?>

<!-- Sidebar -->
    <?= view('creator/_sidebar', [
        'activePage'     => 'stats',
        'user'           => $user,
        'creatorProfile' => $creatorProfile,
        'username'       => $username
    ]) ?>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-full overflow-hidden">
        <!-- Top Nav -->
        <header class="bg-white border-b border-slate-100 px-4 md:px-8 py-3 flex items-center justify-between flex-shrink-0 z-10">
            <div class="flex items-center gap-3">
                <h2 class="text-base md:text-lg font-bold text-slate-900">Analitik & Statistik</h2>
                <span class="hidden md:inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-50 text-indigo-600 rounded-full text-[10px] font-bold uppercase tracking-wide">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
                    Real-time
                </span>
            </div>
            <div class="flex items-center gap-3">
                <div class="hidden md:flex items-center gap-3 border-l pl-6 border-slate-100">
                    <div class="text-right">
                        <p class="text-xs font-bold text-slate-900"><?= esc($creatorProfile['display_name'] ?? $username) ?></p>
                        <p class="text-[10px] text-slate-400 italic">Mode Kreator</p>
                    </div>
                    <?php
                    $avatarUrl = $creatorProfile['profile_image'] ?? null;
                    ?>
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-xs font-bold overflow-hidden">
                        <?php if ($avatarUrl): ?>
                            <img src="<?= profile_url($avatarUrl) ?>" alt="avatar" class="w-full h-full object-cover">
                        <?php else: ?>
                            <?= strtoupper(substr($username, 0, 1)) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </header>

        <!-- Body Scroll Area -->
        <div class="flex-1 overflow-y-auto custom-scrollbar">

            <!-- ══ Hero / Summary Banner ═══════════════════════════════════════ -->
            <div class="top-banner px-6 md:px-10 py-8 md:py-10 relative z-0">
                <div class="relative z-10">
                    <p class="text-indigo-200 text-xs font-bold uppercase tracking-widest mb-2">Dashboard Analitik</p>
                    <h1 class="text-2xl md:text-3xl font-black text-white mb-1">
                        Halo, <span class="shimmer-text"><?= esc($creatorProfile['display_name'] ?? $username) ?>!</span>
                    </h1>
                    <p class="text-indigo-200 text-sm">
                        <?= $stats['total_works'] ?> karya &bull;
                        <?= $stats['published_works'] ?> terbit &bull;
                        <?= number_format($stats['followers']) ?> pengikut
                    </p>

                    <!-- Quick stat pills -->
                    <div class="flex flex-wrap gap-3 mt-6">
                        <div class="flex items-center gap-2 bg-white/15 backdrop-blur-sm rounded-xl px-4 py-2.5 border border-white/20">
                            <span class="material-symbols-outlined text-white" style="font-size:18px">visibility</span>
                            <div>
                                <p class="text-[10px] text-indigo-200 font-bold uppercase">Total Dibaca</p>
                                <p class="text-white font-black text-sm"><?= number_format($stats['total_views']) ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 bg-white/15 backdrop-blur-sm rounded-xl px-4 py-2.5 border border-white/20">
                            <span class="material-symbols-outlined text-white" style="font-size:18px">favorite</span>
                            <div>
                                <p class="text-[10px] text-indigo-200 font-bold uppercase">Total Suka</p>
                                <p class="text-white font-black text-sm"><?= number_format($stats['total_likes']) ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 bg-white/15 backdrop-blur-sm rounded-xl px-4 py-2.5 border border-white/20">
                            <span class="material-symbols-outlined text-white" style="font-size:18px">chat_bubble</span>
                            <div>
                                <p class="text-[10px] text-indigo-200 font-bold uppercase">Komentar</p>
                                <p class="text-white font-black text-sm"><?= number_format($stats['total_comments']) ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 bg-white/15 backdrop-blur-sm rounded-xl px-4 py-2.5 border border-white/20">
                            <span class="material-symbols-outlined text-white" style="font-size:18px">trending_up</span>
                            <div>
                                <p class="text-[10px] text-indigo-200 font-bold uppercase">Engagement</p>
                                <p class="text-white font-black text-sm"><?= $stats['engagement_rate'] ?>%</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-4 md:p-8 pb-28 lg:pb-10 space-y-6 md:space-y-8">

                <!-- ══ KPI Cards ═══════════════════════════════════════════════ -->
                <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">

                    <!-- Views -->
                    <div class="stat-card p-5 md:p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-11 h-11 rounded-2xl flex items-center justify-center bg-gradient-to-br from-indigo-500 to-indigo-600 shadow-lg shadow-indigo-200">
                                <span class="material-symbols-outlined text-white" style="font-size:20px">visibility</span>
                            </div>
                            <?php
                            $vThisMonth = $stats['total_views'];
                            // Views don't have timestamps easily, show total
                            ?>
                            <span class="trend-neu eng-badge text-[10px]">
                                Semua Waktu
                            </span>
                        </div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Pembaca</p>
                        <p class="text-2xl md:text-3xl font-black text-slate-900"><?= number_format($stats['total_views']) ?></p>
                        <div class="mt-3">
                            <div class="prog-bar">
                                <?php $vPct = $stats['total_works'] > 0 ? min(100, round($stats['total_views'] / max(1, $stats['total_views']) * 100)) : 0; ?>
                                <div class="prog-bar-fill bg-gradient-to-r from-indigo-400 to-indigo-600" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Followers -->
                    <div class="stat-card p-5 md:p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-11 h-11 rounded-2xl flex items-center justify-center bg-gradient-to-br from-purple-500 to-fuchsia-600 shadow-lg shadow-purple-200">
                                <span class="material-symbols-outlined text-white" style="font-size:20px">group</span>
                            </div>
                            <?php $fc = $stats['follower_change']; ?>
                            <span class="<?= $fc['up'] ? 'trend-up' : 'trend-down' ?> eng-badge text-[10px]">
                                <span class="material-symbols-outlined mr-0.5" style="font-size:12px"><?= $fc['up'] ? 'arrow_upward' : 'arrow_downward' ?></span>
                                <?= $fc['value'] ?>%
                            </span>
                        </div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Pengikut</p>
                        <p class="text-2xl md:text-3xl font-black text-slate-900"><?= number_format($stats['followers']) ?></p>
                        <p class="text-[10px] text-slate-400 mt-1">
                            +<?= $stats['new_followers_this_month'] ?> bulan ini
                        </p>
                        <div class="mt-2">
                            <div class="prog-bar">
                                <?php $fPct = $stats['new_followers_last_month'] > 0 ? min(100, round($stats['new_followers_this_month'] / $stats['new_followers_last_month'] * 100)) : ($stats['new_followers_this_month'] > 0 ? 100 : 5); ?>
                                <div class="prog-bar-fill bg-gradient-to-r from-purple-400 to-fuchsia-500" style="width: <?= $fPct ?>%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Bookmarks -->
                    <div class="stat-card p-5 md:p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-11 h-11 rounded-2xl flex items-center justify-center bg-gradient-to-br from-rose-500 to-pink-600 shadow-lg shadow-rose-200">
                                <span class="material-symbols-outlined text-white" style="font-size:20px">bookmark</span>
                            </div>
                            <?php $bc = $stats['bookmark_change']; ?>
                            <span class="<?= $bc['up'] ? 'trend-up' : 'trend-down' ?> eng-badge text-[10px]">
                                <span class="material-symbols-outlined mr-0.5" style="font-size:12px"><?= $bc['up'] ? 'arrow_upward' : 'arrow_downward' ?></span>
                                <?= $bc['value'] ?>%
                            </span>
                        </div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Disimpan</p>
                        <p class="text-2xl md:text-3xl font-black text-slate-900"><?= number_format($stats['total_bookmarks']) ?></p>
                        <p class="text-[10px] text-slate-400 mt-1">
                            +<?= $stats['bookmarks_this_month'] ?> bulan ini
                        </p>
                        <div class="mt-2">
                            <div class="prog-bar">
                                <?php $bPct = $stats['total_bookmarks'] > 0 ? min(100, round($stats['bookmarks_this_month'] / $stats['total_bookmarks'] * 100)) : 5; ?>
                                <div class="prog-bar-fill bg-gradient-to-r from-rose-400 to-pink-500" style="width: <?= max(5, $bPct) ?>%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Quality Score / Engagement -->
                    <div class="stat-card p-5 md:p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-11 h-11 rounded-2xl flex items-center justify-center bg-gradient-to-br from-amber-400 to-orange-500 shadow-lg shadow-amber-200">
                                <span class="material-symbols-outlined text-white" style="font-size:20px">star</span>
                            </div>
                            <span class="trend-neu eng-badge text-[10px]">Skor</span>
                        </div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Skor Kualitas</p>
                        <p class="text-2xl md:text-3xl font-black text-slate-900"><?= $stats['quality_score'] ?><span class="text-base text-slate-400 font-bold">/5</span></p>
                        <p class="text-[10px] font-bold text-amber-600 mt-1"><?= $stats['quality_label'] ?></p>
                        <div class="mt-2">
                            <div class="prog-bar">
                                <div class="prog-bar-fill bg-gradient-to-r from-amber-400 to-orange-500" style="width: <?= min(100, $stats['quality_score'] / 5 * 100) ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ══ Charts Row ══════════════════════════════════════════════ -->
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 md:gap-6">

                    <!-- Growth Chart (spans 2 cols) -->
                    <div class="xl:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm p-5 md:p-7">
                        <div class="flex flex-wrap items-start justify-between gap-3 mb-6">
                            <div>
                                <h3 class="font-bold text-slate-900">Aktivitas 30 Hari Terakhir</h3>
                                <p class="text-[11px] text-slate-400 mt-0.5">Likes, simpan, dan komentar harian dari semua karya</p>
                            </div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <div class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-500">
                                    <span class="w-3 h-1 rounded-full bg-indigo-500 inline-block"></span>Suka
                                </div>
                                <div class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-500">
                                    <span class="w-3 h-1 rounded-full bg-rose-400 inline-block"></span>Simpan
                                </div>
                                <div class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-500">
                                    <span class="w-3 h-1 rounded-full bg-amber-400 inline-block"></span>Komentar
                                </div>
                            </div>
                        </div>
                        <div class="h-52 md:h-64">
                            <canvas id="growthChart"></canvas>
                        </div>
                    </div>

                    <!-- Distribution Donut (1 col) -->
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 md:p-7">
                        <h3 class="font-bold text-slate-900 mb-1">Distribusi Pembaca</h3>
                        <p class="text-[11px] text-slate-400 mb-4">Perbandingan interaksi total</p>
                        <div class="h-44 md:h-52 flex items-center justify-center">
                            <canvas id="distChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- ══ Engagement + Content Type Row ══════════════════════════ -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-6">

                    <!-- Engagement Rate Card -->
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 md:p-7 flex items-center gap-5">
                        <!-- Score Ring -->
                        <div class="score-ring-wrapper">
                            <?php
                            $eng    = (float)$stats['engagement_rate'];
                            $r      = 38;
                            $circ   = 2 * M_PI * $r;
                            $offset = $circ - ($eng / 100) * $circ;
                            ?>
                            <svg viewBox="0 0 88 88" width="88" height="88">
                                <circle cx="44" cy="44" r="<?= $r ?>" fill="none" stroke="#EEF2FF" stroke-width="8"/>
                                <circle cx="44" cy="44" r="<?= $r ?>" fill="none"
                                    stroke="url(#engGrad)" stroke-width="8"
                                    stroke-dasharray="<?= $circ ?>"
                                    stroke-dashoffset="<?= $offset ?>"
                                    stroke-linecap="round"/>
                                <defs>
                                    <linearGradient id="engGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#4F46E5"/>
                                        <stop offset="100%" stop-color="#EC4899"/>
                                    </linearGradient>
                                </defs>
                            </svg>
                            <div class="score-text">
                                <span class="text-xl font-black text-slate-900"><?= $stats['engagement_rate'] ?></span>
                                <span class="text-[9px] font-bold text-slate-400 uppercase">%</span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Engagement Rate</p>
                            <p class="font-black text-slate-900 text-lg mt-0.5"><?= $stats['engagement_rate'] ?>%</p>
                            <p class="text-xs text-slate-500 mt-1">
                                <?php if ($eng >= 10): ?>
                                    <span class="text-emerald-600 font-bold">Luar biasa!</span> Pembaca sangat aktif.
                                <?php elseif ($eng >= 5): ?>
                                    <span class="text-indigo-600 font-bold">Sangat baik</span> untuk platform baca.
                                <?php elseif ($eng >= 2): ?>
                                    <span class="text-amber-600 font-bold">Cukup baik.</span> Terus tingkatkan.
                                <?php else: ?>
                                    <span class="text-rose-600 font-bold">Perlu ditingkatkan.</span> Coba konten interaktif.
                                <?php endif; ?>
                            </p>
                            <div class="mt-3 space-y-1.5">
                                <div class="flex items-center justify-between text-[10px]">
                                    <span class="text-slate-400">Suka</span>
                                    <span class="font-bold text-slate-700"><?= number_format($stats['total_likes']) ?></span>
                                </div>
                                <div class="flex items-center justify-between text-[10px]">
                                    <span class="text-slate-400">Simpan</span>
                                    <span class="font-bold text-slate-700"><?= number_format($stats['total_bookmarks']) ?></span>
                                </div>
                                <div class="flex items-center justify-between text-[10px]">
                                    <span class="text-slate-400">Komentar</span>
                                    <span class="font-bold text-slate-700"><?= number_format($stats['total_comments']) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quality Score Detail -->
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 md:p-7">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Skor Kualitas Konten</p>
                        <div class="flex items-end gap-3 mb-4">
                            <span class="text-5xl font-black text-slate-900"><?= $stats['quality_score'] ?></span>
                            <div class="pb-1.5">
                                <span class="text-lg text-slate-300 font-bold">/ 5.0</span>
                                <p class="text-xs font-bold text-amber-500"><?= $stats['quality_label'] ?></p>
                            </div>
                        </div>
                        <!-- Stars -->
                        <div class="flex gap-1 mb-4">
                            <?php
                            $qScore = (float)$stats['quality_score'];
                            for ($s = 1; $s <= 5; $s++):
                                if ($qScore >= $s):
                            ?>
                                <span class="material-symbols-outlined text-amber-400" style="font-size:20px;font-variation-settings:'FILL' 1">star</span>
                            <?php elseif ($qScore >= $s - 0.5): ?>
                                <span class="material-symbols-outlined text-amber-400" style="font-size:20px;font-variation-settings:'FILL' 1">star_half</span>
                            <?php else: ?>
                                <span class="material-symbols-outlined text-slate-200" style="font-size:20px;font-variation-settings:'FILL' 1">star</span>
                            <?php endif; endfor; ?>
                        </div>
                        <div class="space-y-2 text-[11px] text-slate-500">
                            <div class="flex items-center justify-between">
                                <span>Bobot Suka</span>
                                <div class="flex-1 mx-3 prog-bar"><div class="prog-bar-fill bg-indigo-400" style="width: <?= min(100, $stats['total_likes'] / max(1, $stats['total_views']) * 1000) ?>%"></div></div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Bobot Komentar</span>
                                <div class="flex-1 mx-3 prog-bar"><div class="prog-bar-fill bg-amber-400" style="width: <?= min(100, $stats['total_comments'] / max(1, $stats['total_views']) * 2000) ?>%"></div></div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Bobot Simpan</span>
                                <div class="flex-1 mx-3 prog-bar"><div class="prog-bar-fill bg-rose-400" style="width: <?= min(100, $stats['total_bookmarks'] / max(1, $stats['total_views']) * 3000) ?>%"></div></div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Type Breakdown -->
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 md:p-7">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Jenis Konten</p>
                        <div class="h-36 flex items-center justify-center">
                            <canvas id="typeChart"></canvas>
                        </div>
                        <div class="flex flex-wrap gap-x-4 gap-y-1 mt-3 justify-center">
                            <?php foreach ($typeBreakdown as $type => $cnt):
                                $col = $typeColors[$type] ?? '#94A3B8';
                                $lbl = $typeLabels[$type] ?? ucfirst($type);
                            ?>
                            <div class="flex items-center gap-1.5 text-[10px] font-semibold text-slate-500">
                                <span class="w-2 h-2 rounded-full" style="background:<?= $col ?>"></span>
                                <?= $lbl ?> (<?= $cnt ?>)
                            </div>
                            <?php endforeach; ?>
                            <?php if (empty($typeBreakdown)): ?>
                            <p class="text-xs text-slate-400">Belum ada karya.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- ══ Per-Work Table ══════════════════════════════════════════ -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-bold text-slate-900">Performa Per Karya</h3>
                            <p class="text-[11px] text-slate-400 mt-0.5">Diurutkan berdasarkan jumlah pembaca terbanyak</p>
                        </div>
                        <?php if (!empty($worksWithStats)): ?>
                        <a href="<?= base_url('creator/content') ?>" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 transition-colors flex items-center gap-1">
                            Kelola Karya
                            <span class="material-symbols-outlined" style="font-size:14px">arrow_forward</span>
                        </a>
                        <?php endif; ?>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="overflow-x-auto custom-scrollbar">
                            <table class="w-full text-left min-w-[700px]">
                                <thead>
                                    <tr class="border-b border-slate-100">
                                        <th class="px-5 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest w-8">#</th>
                                        <th class="px-5 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Karya</th>
                                        <th class="px-4 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Dibaca</th>
                                        <th class="px-4 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Suka</th>
                                        <th class="px-4 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Simpan</th>
                                        <th class="px-4 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Komentar</th>
                                        <th class="px-4 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Engagement</th>
                                        <th class="px-4 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Status</th>
                                        <th class="px-4 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    <?php if (empty($worksWithStats)): ?>
                                        <tr>
                                            <td colspan="9" class="px-6 py-14 text-center">
                                                <span class="material-symbols-outlined text-slate-200 block mb-3" style="font-size:48px">bar_chart</span>
                                                <p class="text-slate-400 text-sm font-medium">Belum ada karya untuk ditampilkan.</p>
                                                <a href="<?= base_url('creator/content/create') ?>" class="inline-flex items-center gap-1.5 mt-3 text-indigo-600 text-xs font-bold hover:text-indigo-800">
                                                    <span class="material-symbols-outlined" style="font-size:14px">add</span>
                                                    Buat Karya Pertama
                                                </a>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($worksWithStats as $rank => $work): ?>
                                            <?php
                                            $eng = (float)$work['engagement'];
                                            if ($eng >= 10) {
                                                $engClass = 'bg-emerald-50 text-emerald-700';
                                            } elseif ($eng >= 5) {
                                                $engClass = 'bg-indigo-50 text-indigo-700';
                                            } elseif ($eng >= 2) {
                                                $engClass = 'bg-amber-50 text-amber-700';
                                            } else {
                                                $engClass = 'bg-slate-50 text-slate-500';
                                            }
                                            ?>
                                            <tr class="work-row">
                                                <td class="px-5 py-4">
                                                    <span class="text-xs font-black <?= $rank === 0 ? 'text-amber-500' : 'text-slate-300' ?>">
                                                        <?= $rank + 1 ?>
                                                    </span>
                                                </td>
                                                <td class="px-5 py-4">
                                                    <div class="flex items-center gap-3">
                                                        <!-- Cover thumbnail -->
                                                        <div class="w-9 h-11 rounded-lg overflow-hidden bg-gradient-to-br from-indigo-100 to-purple-100 flex-shrink-0 flex items-center justify-center">
                                                            <?php if (!empty($work['cover_url'])): ?>
                                                                <img src="<?= base_url($work['cover_url']) ?>" alt="" class="w-full h-full object-cover">
                                                            <?php else: ?>
                                                                <span class="material-symbols-outlined text-indigo-300" style="font-size:16px">auto_stories</span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="min-w-0">
                                                            <a href="<?= base_url('creator/stats/works/' . $work['id']) ?>" class="font-bold text-slate-900 text-sm line-clamp-1 hover:text-indigo-600 transition-colors"><?= esc($work['title']) ?></a>
                                                            <p class="text-[10px] text-slate-400 capitalize mt-0.5"><?= $typeLabels[$work['content_type']] ?? ucfirst($work['content_type']) ?></p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-4 text-right">
                                                    <span class="font-black text-slate-800 text-sm"><?= number_format($work['views']) ?></span>
                                                </td>
                                                <td class="px-4 py-4 text-right">
                                                    <span class="font-semibold text-slate-600 text-sm"><?= number_format($work['likes']) ?></span>
                                                </td>
                                                <td class="px-4 py-4 text-right">
                                                    <span class="font-semibold text-slate-600 text-sm"><?= number_format($work['bookmarks']) ?></span>
                                                </td>
                                                <td class="px-4 py-4 text-right">
                                                    <span class="font-semibold text-slate-600 text-sm"><?= number_format($work['comments']) ?></span>
                                                </td>
                                                <td class="px-4 py-4 text-center">
                                                    <span class="eng-badge <?= $engClass ?>"><?= $work['engagement'] ?>%</span>
                                                </td>
                                                <td class="px-4 py-4 text-center">
                                                    <?php if ($work['status'] === 'published'): ?>
                                                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 text-[9px] font-black rounded-full uppercase tracking-wide">Terbit</span>
                                                    <?php elseif ($work['status'] === 'curated'): ?>
                                                        <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 text-[9px] font-black rounded-full uppercase tracking-wide">Unggulan</span>
                                                    <?php else: ?>
                                                        <span class="px-2.5 py-1 bg-amber-50 text-amber-700 text-[9px] font-black rounded-full uppercase tracking-wide">Draft</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="px-4 py-4 text-right">
                                                    <div class="flex items-center justify-end gap-2.5">
                                                        <a href="<?= base_url('creator/stats/works/' . $work['id']) ?>"
                                                           class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 transition-colors">
                                                            Insight
                                                        </a>
                                                        <span class="text-slate-200">|</span>
                                                        <a href="<?= base_url('creator/content/' . $work['id'] . '/edit') ?>"
                                                           class="text-[11px] font-bold text-slate-400 hover:text-slate-600 transition-colors">
                                                            Edit
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


                <!-- ══ Purchase / Unlock Analytics ═══════════════════════════ -->
                <div class="mt-2">

                    <!-- Section header -->
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-200">
                            <span class="material-symbols-outlined text-white" style="font-size:18px">paid</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-lg">Analisis Pembelian & Unlock</h3>
                            <p class="text-[11px] text-slate-400">Pola pembeli, pendapatan, dan bab paling diminati</p>
                        </div>
                        <?php if ($purchase['total_unlocks'] === 0 && $purchase['total_transactions'] === 0): ?>
                        <span class="ml-auto px-3 py-1 bg-slate-100 text-slate-400 text-[10px] font-bold rounded-full uppercase">Belum ada transaksi</span>
                        <?php endif; ?>
                    </div>

                    <!-- ── 4 Revenue KPI Cards ── -->
                    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

                        <!-- Total Pendapatan -->
                        <div class="stat-card p-5 md:p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="w-11 h-11 rounded-2xl flex items-center justify-center bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-200">
                                    <span class="material-symbols-outlined text-white" style="font-size:20px">payments</span>
                                </div>
                                <?php $rc = $purchase['revenue_change']; ?>
                                <span class="<?= $rc['up'] ? 'trend-up' : 'trend-down' ?> eng-badge text-[10px]">
                                    <span class="material-symbols-outlined mr-0.5" style="font-size:12px"><?= $rc['up'] ? 'arrow_upward' : 'arrow_downward' ?></span>
                                    <?= $rc['value'] ?>%
                                </span>
                            </div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Pendapatan</p>
                            <p class="text-xl md:text-2xl font-black text-slate-900"><?= number_format($purchase['total_revenue_cc']) ?> <span class="text-sm font-bold text-slate-400">CC</span></p>
                            <p class="text-[10px] text-slate-400 mt-0.5">≈ Rp <?= number_format($purchase['total_revenue_idr']) ?></p>
                            <p class="text-[10px] text-emerald-600 font-bold mt-1">Bulan ini: <?= number_format($purchase['revenue_this_month_cc']) ?> CC</p>
                        </div>

                        <!-- Total Unlock -->
                        <div class="stat-card p-5 md:p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="w-11 h-11 rounded-2xl flex items-center justify-center bg-gradient-to-br from-violet-500 to-purple-600 shadow-lg shadow-violet-200">
                                    <span class="material-symbols-outlined text-white" style="font-size:20px">lock_open</span>
                                </div>
                                <?php $uc = $purchase['unlock_change']; ?>
                                <span class="<?= $uc['up'] ? 'trend-up' : 'trend-down' ?> eng-badge text-[10px]">
                                    <span class="material-symbols-outlined mr-0.5" style="font-size:12px"><?= $uc['up'] ? 'arrow_upward' : 'arrow_downward' ?></span>
                                    <?= $uc['value'] ?>%
                                </span>
                            </div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Unlock Bab</p>
                            <p class="text-xl md:text-2xl font-black text-slate-900"><?= number_format($purchase['total_unlocks']) ?></p>
                            <p class="text-[10px] text-slate-400 mt-0.5">Bulan ini: +<?= $purchase['unlocks_this_month'] ?></p>
                            <div class="mt-2"><div class="prog-bar"><div class="prog-bar-fill bg-gradient-to-r from-violet-400 to-purple-500" style="width: <?= $purchase['total_unlocks'] > 0 ? min(100, $purchase['unlocks_this_month'] / $purchase['total_unlocks'] * 100) : 5 ?>%"></div></div></div>
                        </div>

                        <!-- Unique Buyers -->
                        <div class="stat-card p-5 md:p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="w-11 h-11 rounded-2xl flex items-center justify-center bg-gradient-to-br from-cyan-500 to-blue-600 shadow-lg shadow-cyan-200">
                                    <span class="material-symbols-outlined text-white" style="font-size:20px">shopping_bag</span>
                                </div>
                                <span class="trend-neu eng-badge text-[10px]">Unik</span>
                            </div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Pembeli Unik</p>
                            <p class="text-xl md:text-2xl font-black text-slate-900"><?= number_format($purchase['unique_buyers']) ?></p>
                            <p class="text-[10px] text-slate-400 mt-0.5"><?= $purchase['total_transactions'] ?> total transaksi</p>
                            <div class="mt-2"><div class="prog-bar"><div class="prog-bar-fill bg-gradient-to-r from-cyan-400 to-blue-500" style="width: <?= $purchase['total_transactions'] > 0 ? min(100, round($purchase['unique_buyers'] / $purchase['total_transactions'] * 100)) : 5 ?>%"></div></div></div>
                        </div>

                        <!-- Conversion Rate -->
                        <div class="stat-card p-5 md:p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="w-11 h-11 rounded-2xl flex items-center justify-center bg-gradient-to-br from-orange-500 to-red-500 shadow-lg shadow-orange-200">
                                    <span class="material-symbols-outlined text-white" style="font-size:20px">conversion_path</span>
                                </div>
                                <span class="trend-neu eng-badge text-[10px]">Rate</span>
                            </div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Konversi Pembaca</p>
                            <p class="text-xl md:text-2xl font-black text-slate-900"><?= $purchase['conversion_rate'] ?><span class="text-sm font-bold text-slate-400">%</span></p>
                            <p class="text-[10px] text-slate-400 mt-0.5">Jam puncak: <?= esc($purchase['peak_hour']) ?></p>
                            <div class="mt-2"><div class="prog-bar"><div class="prog-bar-fill bg-gradient-to-r from-orange-400 to-red-500" style="width: <?= min(100, $purchase['conversion_rate'] * 20) ?>%"></div></div></div>
                        </div>

                    </div><!-- /revenue kpi -->

                    <!-- ── Charts Row ── -->
                    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 md:gap-6 mb-6">

                        <!-- Daily Revenue + Unlock Trend (2 cols) -->
                        <div class="xl:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm p-5 md:p-7">
                            <div class="flex flex-wrap items-start justify-between gap-3 mb-5">
                                <div>
                                    <h4 class="font-bold text-slate-900">Tren Pendapatan & Unlock Harian</h4>
                                    <p class="text-[11px] text-slate-400 mt-0.5">30 hari terakhir</p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-500">
                                        <span class="w-3 h-1 rounded-full bg-emerald-500 inline-block"></span>Pendapatan (CC)
                                    </div>
                                    <div class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-500">
                                        <span class="w-3 h-1 rounded-full bg-violet-500 inline-block"></span>Unlock
                                    </div>
                                </div>
                            </div>
                            <div class="h-52 md:h-64">
                                <canvas id="revenueChart"></canvas>
                            </div>
                        </div>

                        <!-- Hour-of-day Heatmap -->
                        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 md:p-7">
                            <h4 class="font-bold text-slate-900 mb-1">Jam Paling Aktif Beli</h4>
                            <p class="text-[11px] text-slate-400 mb-4">Distribusi unlock per jam dalam sehari</p>
                            <div class="h-52 md:h-64">
                                <canvas id="hourChart"></canvas>
                            </div>
                        </div>

                    </div>

                    <!-- ── Day-of-week + Top Revenue Works ── -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-6">

                        <!-- Day-of-week breakdown -->
                        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 md:p-7">
                            <h4 class="font-bold text-slate-900 mb-1">Hari Paling Ramai Pembelian</h4>
                            <p class="text-[11px] text-slate-400 mb-4">Total unlock berdasarkan hari dalam seminggu</p>
                            <div class="h-44">
                                <canvas id="dayChart"></canvas>
                            </div>
                        </div>

                        <!-- Top Revenue Works -->
                        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 md:p-7">
                            <h4 class="font-bold text-slate-900 mb-4">Karya Penghasil Terbanyak</h4>
                            <?php if (empty($topRevenueWorks)): ?>
                            <div class="flex flex-col items-center justify-center py-8 text-center">
                                <span class="material-symbols-outlined text-slate-200 mb-2" style="font-size:40px">receipt_long</span>
                                <p class="text-slate-400 text-sm">Belum ada pendapatan dari karya berbayar.</p>
                            </div>
                            <?php else: ?>
                            <div class="space-y-3">
                                <?php foreach ($topRevenueWorks as $ri => $rw): ?>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-black w-4 text-center <?= $ri === 0 ? 'text-amber-500' : 'text-slate-300' ?>"><?= $ri + 1 ?></span>
                                    <div class="w-8 h-10 rounded-lg overflow-hidden bg-gradient-to-br from-emerald-100 to-teal-100 flex-shrink-0 flex items-center justify-center">
                                        <?php if (!empty($rw['cover_url'])): ?>
                                            <img src="<?= base_url($rw['cover_url']) ?>" alt="" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <span class="material-symbols-outlined text-emerald-300" style="font-size:14px">auto_stories</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-bold text-slate-900 text-xs line-clamp-1"><?= esc($rw['title']) ?></p>
                                        <p class="text-[10px] text-slate-400"><?= $rw['tx_count'] ?> transaksi</p>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <p class="font-black text-emerald-600 text-sm"><?= number_format($rw['revenue_cc']) ?> CC</p>
                                        <p class="text-[10px] text-slate-400">≈ Rp <?= number_format($rw['revenue_cc'] * 10) ?></p>
                                    </div>
                                </div>
                                <?php if ($ri < count($topRevenueWorks) - 1): ?>
                                <div class="border-b border-slate-50"></div>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>

                    </div>

                    <!-- ── Top Unlocked Chapters + Recent Activity ── -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">

                        <!-- Top Unlocked Chapters -->
                        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                            <div class="px-5 py-4 border-b border-slate-50">
                                <h4 class="font-bold text-slate-900">Bab Paling Banyak Di-unlock</h4>
                                <p class="text-[11px] text-slate-400 mt-0.5">Konten berbayar paling diminati pembaca</p>
                            </div>
                            <?php if (empty($topUnlockedChapters)): ?>
                            <div class="flex flex-col items-center justify-center py-10 text-center px-5">
                                <span class="material-symbols-outlined text-slate-200 mb-2" style="font-size:40px">lock</span>
                                <p class="text-slate-400 text-sm">Belum ada bab yang di-unlock pembaca.</p>
                                <p class="text-[11px] text-slate-300 mt-1">Coba tambahkan bab berbayar pada karyamu.</p>
                            </div>
                            <?php else: ?>
                            <div class="divide-y divide-slate-50">
                                <?php foreach ($topUnlockedChapters as $ci => $ch): ?>
                                <div class="px-5 py-3.5 flex items-center gap-3">
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 <?= $ci === 0 ? 'bg-amber-100' : 'bg-slate-100' ?>">
                                        <span class="text-[10px] font-black <?= $ci === 0 ? 'text-amber-600' : 'text-slate-400' ?>"><?= $ci + 1 ?></span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-bold text-slate-900 text-xs line-clamp-1"><?= esc($ch['chapter_title']) ?></p>
                                        <p class="text-[10px] text-slate-400 line-clamp-1"><?= esc($ch['work_title']) ?> &bull; Bab <?= $ch['order_num'] ?></p>
                                    </div>
                                    <div class="flex-shrink-0 text-right">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-violet-50 text-violet-700 text-[10px] font-black rounded-full">
                                            <span class="material-symbols-outlined" style="font-size:11px">lock_open</span>
                                            <?= $ch['unlocks'] ?>x
                                        </span>
                                        <?php if ($ch['price'] > 0): ?>
                                        <p class="text-[10px] text-slate-400 mt-0.5"><?= $ch['price'] ?> CC</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Recent Unlock Activity -->
                        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                            <div class="px-5 py-4 border-b border-slate-50">
                                <h4 class="font-bold text-slate-900">Aktivitas Unlock Terbaru</h4>
                                <p class="text-[11px] text-slate-400 mt-0.5">10 transaksi terakhir dari pembaca</p>
                            </div>
                            <?php if (empty($recentUnlocks)): ?>
                            <div class="flex flex-col items-center justify-center py-10 text-center px-5">
                                <span class="material-symbols-outlined text-slate-200 mb-2" style="font-size:40px">manage_history</span>
                                <p class="text-slate-400 text-sm">Belum ada aktivitas unlock.</p>
                            </div>
                            <?php else: ?>
                            <div class="divide-y divide-slate-50 max-h-80 overflow-y-auto custom-scrollbar">
                                <?php foreach ($recentUnlocks as $ru): ?>
                                <div class="px-5 py-3 flex items-start gap-3">
                                    <!-- Avatar -->
                                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center flex-shrink-0 text-white text-[10px] font-black">
                                        <?= strtoupper(substr($ru['buyer_name'] ?? '?', 0, 1)) ?>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold text-slate-900">
                                            <span class="text-indigo-600"><?= esc($ru['buyer_name'] ?? 'Anonim') ?></span>
                                            membuka bab
                                        </p>
                                        <p class="text-[10px] text-slate-500 line-clamp-1"><?= esc($ru['chapter_title']) ?> &bull; <?= esc($ru['work_title']) ?></p>
                                        <p class="text-[9px] text-slate-300 mt-0.5"><?= date('d M Y, H:i', strtotime($ru['created_at'])) ?></p>
                                    </div>
                                    <?php if (!empty($ru['chapter_price']) && $ru['chapter_price'] > 0): ?>
                                    <span class="flex-shrink-0 text-[10px] font-black text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">
                                        +<?= $ru['chapter_price'] ?> CC
                                    </span>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>

                    </div><!-- /chapters + activity -->

                </div><!-- /purchase section -->

            </div><!-- /inner padding -->
        </div><!-- /scroll area -->
    </main>

    <script>
    // ── PHP Data ────────────────────────────────────────────────────────────
    const growthLabels       = <?= json_encode($growthLabels) ?>;
    const growthLikes        = <?= json_encode($growthLikes) ?>;
    const growthBmarks       = <?= json_encode($growthBmarks) ?>;
    const growthComments     = <?= json_encode($growthComments) ?>;
    const distData           = <?= json_encode($distributionData) ?>;
    const typeBreakdown      = <?= json_encode(!empty($typeBreakdown) ? $typeBreakdown : (object)[]) ?>;
    // Purchase analytics data
    const revLabels          = <?= json_encode($revenueDailyLabels) ?>;
    const revDailyCC         = <?= json_encode($revenueDailyCC) ?>;
    const revDailyUnlocks    = <?= json_encode($revenueDailyUnlocks) ?>;
    const unlocksByHour      = <?= json_encode($unlocksByHour) ?>;
    const unlocksByDay       = <?= json_encode($unlocksByDay) ?>;
    const dayNames           = <?= json_encode($dayNames) ?>;

    // ── Global Defaults ─────────────────────────────────────────────────────
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color       = '#94A3B8';

    // ── Growth Chart ────────────────────────────────────────────────────────
    const ctxGrowth = document.getElementById('growthChart').getContext('2d');

    // Gradient fills
    const gradLikes = ctxGrowth.createLinearGradient(0, 0, 0, 200);
    gradLikes.addColorStop(0, 'rgba(79,70,229,0.18)');
    gradLikes.addColorStop(1, 'rgba(79,70,229,0)');

    const gradBmarks = ctxGrowth.createLinearGradient(0, 0, 0, 200);
    gradBmarks.addColorStop(0, 'rgba(244,63,94,0.14)');
    gradBmarks.addColorStop(1, 'rgba(244,63,94,0)');

    const gradComments = ctxGrowth.createLinearGradient(0, 0, 0, 200);
    gradComments.addColorStop(0, 'rgba(245,158,11,0.12)');
    gradComments.addColorStop(1, 'rgba(245,158,11,0)');

    new Chart(ctxGrowth, {
        type: 'line',
        data: {
            labels: growthLabels,
            datasets: [
                {
                    label: 'Suka',
                    data: growthLikes,
                    borderColor: '#4F46E5',
                    backgroundColor: gradLikes,
                    borderWidth: 2.5,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: '#4F46E5',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2,
                },
                {
                    label: 'Simpan',
                    data: growthBmarks,
                    borderColor: '#F43F5E',
                    backgroundColor: gradBmarks,
                    borderWidth: 2.5,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: '#F43F5E',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2,
                },
                {
                    label: 'Komentar',
                    data: growthComments,
                    borderColor: '#F59E0B',
                    backgroundColor: gradComments,
                    borderWidth: 2.5,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: '#F59E0B',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15,23,42,0.95)',
                    titleFont: { size: 11, weight: '700' },
                    bodyFont: { size: 11 },
                    padding: 12,
                    cornerRadius: 10,
                    borderColor: 'rgba(255,255,255,0.08)',
                    borderWidth: 1,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(226,232,240,0.5)', drawBorder: false },
                    ticks: {
                        font: { size: 10, weight: '600' },
                        padding: 8,
                        maxTicksLimit: 5,
                        callback: v => v % 1 === 0 ? v : '',
                    },
                    border: { display: false }
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { size: 10, weight: '600' },
                        maxTicksLimit: 10,
                        maxRotation: 0,
                    },
                    border: { display: false }
                }
            }
        }
    });

    // ── Distribution Donut ──────────────────────────────────────────────────
    const ctxDist = document.getElementById('distChart').getContext('2d');
    new Chart(ctxDist, {
        type: 'doughnut',
        data: {
            labels: ['Pembaca Biasa', 'Suka', 'Disimpan', 'Komentar'],
            datasets: [{
                data: distData,
                backgroundColor: ['#4F46E5', '#10B981', '#F43F5E', '#F59E0B'],
                borderWidth: 3,
                borderColor: '#ffffff',
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        pointStyleWidth: 8,
                        padding: 12,
                        font: { size: 10, weight: '600' },
                        color: '#64748B',
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(15,23,42,0.95)',
                    titleFont: { size: 11, weight: '700' },
                    bodyFont: { size: 11 },
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: ctx => {
                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            const pct   = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                            return ` ${ctx.label}: ${ctx.parsed} (${pct}%)`;
                        }
                    }
                }
            }
        }
    });

    // ── Content Type Bar Chart ──────────────────────────────────────────────
    const typeCtx = document.getElementById('typeChart');
    if (typeCtx) {
        const typeKeys   = Object.keys(typeBreakdown);
        const typeVals   = Object.values(typeBreakdown);
        const typeColors = { text: '#4F46E5', comic: '#EC4899', image: '#10B981', audio: '#F59E0B', video: '#6366F1' };
        const typeBg     = typeKeys.map(k => typeColors[k] || '#94A3B8');
        const labelMap   = { text: 'Tulisan', comic: 'Komik', image: 'Gambar', audio: 'Audio', video: 'Video' };

        new Chart(typeCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: typeKeys.map(k => labelMap[k] || k),
                datasets: [{
                    data: typeVals,
                    backgroundColor: typeBg,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15,23,42,0.95)',
                        padding: 10,
                        cornerRadius: 8,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(226,232,240,0.4)' },
                        ticks: { font: { size: 10, weight: '600' }, maxTicksLimit: 4, callback: v => v % 1 === 0 ? v : '' },
                        border: { display: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10, weight: '600' } },
                        border: { display: false }
                    }
                }
            }
        });
    }

    // ── Revenue + Unlock Trend Chart ────────────────────────────────────────
    const ctxRevenue = document.getElementById('revenueChart');
    if (ctxRevenue) {
        const gradRev = ctxRevenue.getContext('2d').createLinearGradient(0, 0, 0, 220);
        gradRev.addColorStop(0, 'rgba(16,185,129,0.2)');
        gradRev.addColorStop(1, 'rgba(16,185,129,0)');

        new Chart(ctxRevenue.getContext('2d'), {
            type: 'line',
            data: {
                labels: revLabels,
                datasets: [
                    {
                        label: 'Pendapatan (CC)',
                        data: revDailyCC,
                        borderColor: '#10B981',
                        backgroundColor: gradRev,
                        borderWidth: 2.5,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 0,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: '#10B981',
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 2,
                        yAxisID: 'yCC',
                    },
                    {
                        label: 'Unlock',
                        data: revDailyUnlocks,
                        borderColor: '#8B5CF6',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        borderDash: [5, 3],
                        tension: 0.4,
                        fill: false,
                        pointRadius: 0,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: '#8B5CF6',
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 2,
                        yAxisID: 'yUnlocks',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15,23,42,0.95)',
                        titleFont: { size: 11, weight: '700' },
                        bodyFont: { size: 11 },
                        padding: 12,
                        cornerRadius: 10,
                        callbacks: {
                            label: ctx => {
                                if (ctx.datasetIndex === 0) return ` Pendapatan: ${ctx.parsed.y} CC`;
                                return ` Unlock: ${ctx.parsed.y}x`;
                            }
                        }
                    }
                },
                scales: {
                    yCC: {
                        type: 'linear', position: 'left', beginAtZero: true,
                        grid: { color: 'rgba(226,232,240,0.5)' },
                        ticks: { font: { size: 10, weight: '600' }, maxTicksLimit: 5, callback: v => v % 1 === 0 ? v + ' CC' : '' },
                        border: { display: false }
                    },
                    yUnlocks: {
                        type: 'linear', position: 'right', beginAtZero: true,
                        grid: { display: false },
                        ticks: { font: { size: 10, weight: '600' }, maxTicksLimit: 5, callback: v => v % 1 === 0 ? v + 'x' : '' },
                        border: { display: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10, weight: '600' }, maxTicksLimit: 10, maxRotation: 0 },
                        border: { display: false }
                    }
                }
            }
        });
    }

    // ── Hour-of-day Heatmap ──────────────────────────────────────────────────
    const ctxHour = document.getElementById('hourChart');
    if (ctxHour) {
        const peakHourIdx = unlocksByHour.indexOf(Math.max(...unlocksByHour));
        const hourColors  = unlocksByHour.map((_, i) =>
            i === peakHourIdx ? '#8B5CF6' : 'rgba(139,92,246,0.25)'
        );
        const hourLabels  = Array.from({length: 24}, (_, i) => `${String(i).padStart(2,'0')}:00`);

        new Chart(ctxHour.getContext('2d'), {
            type: 'bar',
            data: {
                labels: hourLabels,
                datasets: [{
                    label: 'Unlock',
                    data: unlocksByHour,
                    backgroundColor: hourColors,
                    borderRadius: 4,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15,23,42,0.95)',
                        padding: 10, cornerRadius: 8,
                        callbacks: { label: ctx => ` ${ctx.parsed.y} unlock` }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(226,232,240,0.4)' },
                        ticks: { font: { size: 9, weight: '600' }, maxTicksLimit: 4, callback: v => v % 1 === 0 ? v : '' },
                        border: { display: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 8, weight: '600' },
                            maxRotation: 0,
                            callback: (val, idx) => idx % 3 === 0 ? hourLabels[idx] : ''
                        },
                        border: { display: false }
                    }
                }
            }
        });
    }

    // ── Day-of-week Chart ────────────────────────────────────────────────────
    const ctxDay = document.getElementById('dayChart');
    if (ctxDay) {
        const maxDayVal  = Math.max(...unlocksByDay);
        const dayColors  = unlocksByDay.map(v => v === maxDayVal && maxDayVal > 0 ? '#10B981' : 'rgba(16,185,129,0.25)');

        new Chart(ctxDay.getContext('2d'), {
            type: 'bar',
            data: {
                labels: dayNames,
                datasets: [{
                    label: 'Unlock',
                    data: unlocksByDay,
                    backgroundColor: dayColors,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15,23,42,0.95)',
                        padding: 10, cornerRadius: 8,
                        callbacks: { label: ctx => ` ${ctx.parsed.y} unlock` }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(226,232,240,0.4)' },
                        ticks: { font: { size: 10, weight: '600' }, maxTicksLimit: 4, callback: v => v % 1 === 0 ? v : '' },
                        border: { display: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10, weight: '700' } },
                        border: { display: false }
                    }
                }
            }
        });
    }

    // ── Animate progress bars ────────────────────────────────────────────────
    (function() {
        const fills = document.querySelectorAll('.prog-bar-fill');
        fills.forEach(el => {
            const target = el.style.width;
            el.style.width = '0%';
            setTimeout(() => { el.style.width = target; }, 400);
        });
    })();
    </script>
</body>
</html>
