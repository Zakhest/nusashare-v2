<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= $title ?? 'Statistik Karya - NusaShare' ?></title>
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

        /* ── Trend Badges ─────────────────────────────── */
        .trend-up   { background: #ECFDF5; color: #059669; }
        .trend-down { background: #FFF1F2; color: #E11D48; }
        .trend-neu  { background: #F1F5F9; color: #64748B; }
        .eng-badge  {
            display: inline-flex;
            align-items: center;
            padding: 2px 10px;
            border-radius: 99px;
            font-size: 11px;
            font-weight: 700;
        }

        /* ── Hero Banner ──────────────────────────────── */
        .top-banner {
            background: linear-gradient(135deg, #1E1B4B 0%, #312E81 50%, #4F46E5 100%);
            position: relative;
            overflow: hidden;
        }
        .top-banner::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
            top: -80px;
            right: -60px;
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
    <main class="flex-1 flex flex-col h-full overflow-hidden">
        <!-- Top Nav -->
        <header class="bg-white border-b border-slate-100 px-4 md:px-8 py-3 flex items-center justify-between flex-shrink-0 z-10">
            <div class="flex items-center gap-3">
                <a href="<?= base_url('creator/stats') ?>" class="p-2 hover:bg-slate-50 rounded-xl transition-all text-slate-400 hover:text-slate-600 flex items-center justify-center">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <h2 class="text-base md:text-lg font-bold text-slate-900">Statistik Karya</h2>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="hidden md:flex items-center gap-3">
                    <div class="text-right">
                        <p class="text-xs font-bold text-slate-900"><?= esc($creatorProfile['display_name'] ?? $username) ?></p>
                        <p class="text-[10px] text-slate-400 italic">Mode Kreator</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Scrollable Body -->
        <div class="flex-1 overflow-y-auto custom-scrollbar">

            <!-- ══ Work Hero Banner ═══════════════════════════════════════════ -->
            <div class="top-banner px-6 md:px-8 py-6 md:py-8 relative z-0">
                <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center gap-6">
                    <!-- Cover Thumbnail -->
                    <div class="w-20 h-28 bg-white/10 backdrop-blur rounded-xl overflow-hidden shadow-2xl flex-shrink-0 border border-white/20">
                        <?php 
                        $coverUrl = !empty($work['cover_url']) ? base_url($work['cover_url']) : base_url('assets/icon/logonuss.png');
                        ?>
                        <img src="<?= $coverUrl ?>" alt="Cover" class="w-full h-full object-cover">
                    </div>
                    <!-- Metadata -->
                    <div class="flex-1 min-w-0">
                        <p class="text-indigo-200 text-xs font-bold uppercase tracking-widest mb-1">Analisis Per Karya</p>
                        <h1 class="text-xl md:text-2xl font-black text-white leading-tight mb-2"><?= esc($work['title']) ?></h1>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-2.5 py-0.5 bg-white/10 backdrop-blur text-white border border-white/10 rounded-full text-[10px] font-bold uppercase tracking-wider">
                                <?= esc($work['content_type']) ?>
                            </span>
                            <?php if ($work['status'] === 'published'): ?>
                                <span class="px-2.5 py-0.5 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 rounded-full text-[10px] font-bold uppercase tracking-wider">Terbit</span>
                            <?php else: ?>
                                <span class="px-2.5 py-0.5 bg-amber-500/20 text-amber-300 border border-amber-500/30 rounded-full text-[10px] font-bold uppercase tracking-wider">Draft</span>
                            <?php endif; ?>
                            <?php if ($work['is_paid']): ?>
                                <span class="px-2.5 py-0.5 bg-rose-500/20 text-rose-300 border border-rose-500/30 rounded-full text-[10px] font-bold uppercase tracking-wider">Premium</span>
                            <?php endif; ?>
                            <span class="text-xs text-indigo-200 font-medium ml-1">Dirilis: <?= date('d M Y', strtotime($work['created_at'])) ?></span>
                        </div>
                        <p class="text-xs text-indigo-100/70 mt-3 max-w-xl line-clamp-2"><?= esc($work['description']) ?></p>
                    </div>
                </div>
            </div>

            <div class="p-4 md:p-8 pb-28 space-y-6 md:space-y-8">

                <!-- ══ KPI Cards Grid ══════════════════════════════════════════ -->
                <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">

                    <!-- Pembaca -->
                    <div class="stat-card p-5">
                        <div class="flex items-start justify-between mb-3">
                            <div class="w-10 h-10 rounded-2xl flex items-center justify-center bg-gradient-to-br from-indigo-500 to-indigo-600 shadow-md shadow-indigo-200 text-white">
                                <span class="material-symbols-outlined" style="font-size:20px">visibility</span>
                            </div>
                            <span class="trend-neu eng-badge text-[10px]">Semua Waktu</span>
                        </div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Pembaca</p>
                        <p class="text-2xl font-black text-slate-900"><?= number_format($stats['views']) ?></p>
                        <div class="mt-3"><div class="prog-bar"><div class="prog-bar-fill bg-gradient-to-r from-indigo-400 to-indigo-600" style="width: 100%"></div></div></div>
                    </div>

                    <!-- Disukai -->
                    <div class="stat-card p-5">
                        <div class="flex items-start justify-between mb-3">
                            <div class="w-10 h-10 rounded-2xl flex items-center justify-center bg-gradient-to-br from-rose-500 to-pink-600 shadow-md shadow-rose-200 text-white">
                                <span class="material-symbols-outlined" style="font-size:20px">favorite</span>
                            </div>
                            <span class="trend-neu eng-badge text-[10px]">Suka</span>
                        </div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Disukai</p>
                        <p class="text-2xl font-black text-slate-900"><?= number_format($stats['likes']) ?></p>
                        <div class="mt-3"><div class="prog-bar"><div class="prog-bar-fill bg-gradient-to-r from-rose-400 to-pink-500" style="width: <?= $stats['views'] > 0 ? min(100, $stats['likes'] / $stats['views'] * 1000) : 5 ?>%"></div></div></div>
                    </div>

                    <!-- Bookmarks -->
                    <div class="stat-card p-5">
                        <div class="flex items-start justify-between mb-3">
                            <div class="w-10 h-10 rounded-2xl flex items-center justify-center bg-gradient-to-br from-emerald-500 to-teal-600 shadow-md shadow-emerald-200 text-white">
                                <span class="material-symbols-outlined" style="font-size:20px">bookmark</span>
                            </div>
                            <span class="trend-neu eng-badge text-[10px]">Disimpan</span>
                        </div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Disimpan</p>
                        <p class="text-2xl font-black text-slate-900"><?= number_format($stats['bookmarks']) ?></p>
                        <div class="mt-3"><div class="prog-bar"><div class="prog-bar-fill bg-gradient-to-r from-emerald-400 to-teal-500" style="width:  <?= $stats['views'] > 0 ? min(100, $stats['bookmarks'] / $stats['views'] * 1000) : 5 ?>%"></div></div></div>
                    </div>

                    <!-- Quality Score -->
                    <div class="stat-card p-5">
                        <div class="flex items-start justify-between mb-3">
                            <div class="w-10 h-10 rounded-2xl flex items-center justify-center bg-gradient-to-br from-amber-400 to-orange-500 shadow-md shadow-amber-200 text-white">
                                <span class="material-symbols-outlined" style="font-size:20px">star</span>
                            </div>
                            <span class="trend-neu eng-badge text-[10px]">Skor</span>
                        </div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Skor Kualitas</p>
                        <p class="text-2xl font-black text-slate-900"><?= $stats['quality_score'] ?><span class="text-sm font-bold text-slate-400">/5</span></p>
                        <div class="mt-3"><div class="prog-bar"><div class="prog-bar-fill bg-gradient-to-r from-amber-400 to-orange-500" style="width: <?= min(100, $stats['quality_score'] / 5 * 100) ?>%"></div></div></div>
                    </div>

                </div>

                <!-- ══ Charts Row ══════════════════════════════════════════════ -->
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

                    <!-- Growth Trend (2 cols) -->
                    <div class="xl:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm p-5 md:p-7">
                        <div class="flex flex-wrap items-start justify-between gap-3 mb-6">
                            <div>
                                <h3 class="font-bold text-slate-900">Aktivitas 30 Hari Terakhir</h3>
                                <p class="text-[11px] text-slate-400 mt-0.5">Likes, simpan, dan komentar harian pada karya ini</p>
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
                        <div class="h-56 md:h-64">
                            <canvas id="growthChart"></canvas>
                        </div>
                    </div>

                    <!-- Distribution -->
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 md:p-7">
                        <h3 class="font-bold text-slate-900 mb-1">Distribusi Pembaca</h3>
                        <p class="text-[11px] text-slate-400 mb-4">Perbandingan interaksi pada karya ini</p>
                        <div class="h-44 md:h-52 flex items-center justify-center">
                            <canvas id="distChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- ══ Engagement Rate & Quality Score details ════════════════ -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Engagement Detail -->
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 md:p-7 flex items-center gap-5">
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
                                    <span class="text-emerald-600 font-bold">Luar biasa!</span> Pembaca merespon dengan sangat baik.
                                <?php elseif ($eng >= 5): ?>
                                    <span class="text-indigo-600 font-bold">Sangat baik</span> untuk karya jenis ini.
                                <?php elseif ($eng >= 2): ?>
                                    <span class="text-amber-600 font-bold">Cukup baik.</span> Terus kembangkan alur cerita.
                                <?php else: ?>
                                    <span class="text-rose-600 font-bold">Perlu ditingkatkan.</span> Tambahkan interaksi atau info menarik.
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>

                    <!-- Quality score summary -->
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 md:p-7">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Penilaian Kualitas Karya</p>
                        <div class="flex items-end gap-3 mb-4">
                            <span class="text-5xl font-black text-slate-900"><?= $stats['quality_score'] ?></span>
                            <div class="pb-1.5">
                                <span class="text-lg text-slate-300 font-bold">/ 5.0</span>
                                <p class="text-xs font-bold text-amber-500"><?= $stats['quality_label'] ?></p>
                            </div>
                        </div>
                        <div class="flex gap-1 mb-2">
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
                    </div>
                </div>

                <!-- ══ Purchase & Unlock Analytics for Premium Works ══════════ -->
                <div class="border-t border-slate-100 pt-8">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-200">
                            <span class="material-symbols-outlined text-white" style="font-size:18px">paid</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-lg">Analisis Pembelian & Unlock Bab</h3>
                            <p class="text-[11px] text-slate-400">Pola pembelian koin khusus untuk karya ini</p>
                        </div>
                    </div>

                    <!-- KPI grid -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <!-- Revenue -->
                        <div class="stat-card p-5">
                            <div class="flex items-start justify-between mb-3">
                                <div class="w-10 h-10 rounded-2xl flex items-center justify-center bg-gradient-to-br from-emerald-500 to-teal-600 text-white">
                                    <span class="material-symbols-outlined" style="font-size:20px">payments</span>
                                </div>
                                <?php $ic = $stats['income_change']; ?>
                                <span class="<?= $ic['up'] ? 'trend-up' : 'trend-down' ?> eng-badge text-[10px]">
                                    <span class="material-symbols-outlined mr-0.5" style="font-size:12px"><?= $ic['up'] ? 'arrow_upward' : 'arrow_downward' ?></span>
                                    <?= $ic['value'] ?>%
                                </span>
                            </div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Pendapatan Karya</p>
                            <p class="text-xl font-black text-slate-900"><?= number_format($stats['total_income']) ?> <span class="text-xs text-slate-400 font-bold">CC</span></p>
                            <p class="text-[10px] text-slate-400 mt-0.5">≈ Rp <?= number_format($stats['total_income_idr']) ?></p>
                        </div>

                        <!-- Unlocks count -->
                        <div class="stat-card p-5">
                            <div class="flex items-start justify-between mb-3">
                                <div class="w-10 h-10 rounded-2xl flex items-center justify-center bg-gradient-to-br from-violet-500 to-purple-600 text-white">
                                    <span class="material-symbols-outlined" style="font-size:20px">lock_open</span>
                                </div>
                                <span class="trend-neu eng-badge text-[10px]">Unlock</span>
                            </div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Unlock</p>
                            <p class="text-xl font-black text-slate-900"><?= number_format($stats['total_unlocks']) ?>x</p>
                        </div>

                        <!-- Unique Buyers -->
                        <div class="stat-card p-5">
                            <div class="flex items-start justify-between mb-3">
                                <div class="w-10 h-10 rounded-2xl flex items-center justify-center bg-gradient-to-br from-cyan-500 to-blue-600 text-white">
                                    <span class="material-symbols-outlined" style="font-size:20px">shopping_bag</span>
                                </div>
                                <span class="trend-neu eng-badge text-[10px]">Pembeli</span>
                            </div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Pembeli Unik</p>
                            <p class="text-xl font-black text-slate-900"><?= number_format($stats['unique_buyers']) ?></p>
                        </div>

                        <!-- Conversion -->
                        <div class="stat-card p-5">
                            <div class="flex items-start justify-between mb-3">
                                <div class="w-10 h-10 rounded-2xl flex items-center justify-center bg-gradient-to-br from-orange-500 to-red-500 text-white">
                                    <span class="material-symbols-outlined" style="font-size:20px">conversion_path</span>
                                </div>
                                <span class="trend-neu eng-badge text-[10px]">Konversi</span>
                            </div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Rasio Konversi</p>
                            <p class="text-xl font-black text-slate-900"><?= $stats['conversion_rate'] ?>%</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">Puncak: <?= esc($stats['peak_hour']) ?></p>
                        </div>
                    </div>

                    <!-- Revenue chart and heatmap charts -->
                    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
                        <!-- Revenue Trend -->
                        <div class="xl:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm p-5 md:p-7">
                            <h4 class="font-bold text-slate-900 mb-4">Tren Pendapatan & Unlock Bab (Harian)</h4>
                            <div class="h-52">
                                <canvas id="revenueChart"></canvas>
                            </div>
                        </div>
                        <!-- Hourly heatmap -->
                        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 md:p-7">
                            <h4 class="font-bold text-slate-900 mb-4">Waktu Pembelian Terpopuler</h4>
                            <div class="h-52">
                                <canvas id="hourChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Weekly patterns & Chapters list -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Day of week pattern -->
                        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 md:p-7">
                            <h4 class="font-bold text-slate-900 mb-4">Pembelian Berdasarkan Hari</h4>
                            <div class="h-44">
                                <canvas id="dayChart"></canvas>
                            </div>
                        </div>

                        <!-- Chapter performa list -->
                        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden flex flex-col justify-between">
                            <div class="p-5 border-b border-slate-50">
                                <h4 class="font-bold text-slate-900">Performa Bab (Unlock)</h4>
                                <p class="text-[11px] text-slate-400 mt-0.5">Daftar unlock per bab karya ini</p>
                            </div>
                            <?php if (empty($chaptersWithStats)): ?>
                            <div class="flex-1 flex flex-col items-center justify-center py-10 text-center px-5">
                                <span class="material-symbols-outlined text-slate-200 mb-2" style="font-size:36px">lock</span>
                                <p class="text-slate-400 text-sm">Karya ini belum memiliki bab yang di-lock atau dibeli.</p>
                            </div>
                            <?php else: ?>
                            <div class="divide-y divide-slate-50 max-h-60 overflow-y-auto custom-scrollbar flex-1">
                                <?php foreach ($chaptersWithStats as $cws): ?>
                                <div class="px-5 py-3 flex items-center justify-between">
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-900 text-xs truncate">Bab <?= $cws['order_num'] ?>: <?= esc($cws['title']) ?></p>
                                        <p class="text-[10px] text-slate-400"><?= $cws['price'] ?> CC &bull; <?= $cws['is_locked'] ? 'Premium' : 'Gratis' ?></p>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-violet-50 text-violet-700 text-[10px] font-black rounded-full">
                                            <?= $cws['unlocks'] ?>x unlock
                                        </span>
                                        <p class="text-[10px] text-emerald-600 font-bold mt-0.5"><?= number_format($cws['revenue']) ?> CC</p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Recent unlocks for this work -->
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-50">
                            <h4 class="font-bold text-slate-900">Unlock Bab Terbaru</h4>
                            <p class="text-[11px] text-slate-400 mt-0.5">Pembelian bab terbaru pada karya ini</p>
                        </div>
                        <?php if (empty($recentUnlocks)): ?>
                        <div class="flex flex-col items-center justify-center py-12 text-center px-5">
                            <span class="material-symbols-outlined text-slate-200 mb-2" style="font-size:36px">history</span>
                            <p class="text-slate-400 text-sm">Belum ada riwayat unlock untuk karya ini.</p>
                        </div>
                        <?php else: ?>
                        <div class="divide-y divide-slate-50 max-h-80 overflow-y-auto custom-scrollbar">
                            <?php foreach ($recentUnlocks as $ru): ?>
                            <div class="px-5 py-3.5 flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center flex-shrink-0 text-white text-xs font-black">
                                    <?= strtoupper(substr($ru['buyer_name'] ?? '?', 0, 1)) ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-slate-900">
                                        <span class="text-indigo-600"><?= esc($ru['buyer_name'] ?? 'Anonim') ?></span>
                                        membuka bab <?= $ru['order_num'] ?>
                                    </p>
                                    <p class="text-[10px] text-slate-500 truncate"><?= esc($ru['chapter_title']) ?></p>
                                    <p class="text-[9px] text-slate-300 mt-0.5"><?= date('d M Y, H:i', strtotime($ru['created_at'])) ?></p>
                                </div>
                                <span class="flex-shrink-0 text-[10px] font-black text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">
                                    +<?= $ru['price'] ?> CC
                                </span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- ══ Recent Comments ════════════════════════════════════════ -->
                <div>
                    <h3 class="font-bold text-slate-900 text-lg flex items-center gap-2 mb-4">
                        <span class="material-symbols-outlined text-indigo-600">forum</span> 
                        Komentar Terbaru
                    </h3>
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        <?php if (empty($recentComments)): ?>
                            <div class="p-8 text-center text-slate-400">
                                <span class="material-symbols-outlined text-4xl mb-2">speaker_notes_off</span>
                                <p class="text-sm">Belum ada komentar untuk karya ini.</p>
                            </div>
                        <?php else: ?>
                            <ul class="divide-y divide-slate-100">
                                <?php foreach ($recentComments as $comment): ?>
                                <li class="p-5 hover:bg-slate-50/50 transition-colors">
                                    <div class="flex items-start gap-4">
                                        <div class="w-9 h-9 bg-slate-100 text-slate-500 font-bold rounded-full flex items-center justify-center shrink-0">
                                            <?= strtoupper(substr($comment['username'], 0, 1)) ?>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-1">
                                                <h4 class="font-bold text-slate-900 text-xs">
                                                    <?= esc($comment['username']) ?>
                                                    <?php if ($comment['role'] === 'creator'): ?>
                                                        <span class="ml-2 px-2 py-0.5 bg-indigo-100 text-indigo-600 rounded text-[9px] font-black uppercase">Kreator</span>
                                                    <?php endif; ?>
                                                </h4>
                                                <span class="text-[10px] font-medium text-slate-400"><?= date('d M Y, H:i', strtotime($comment['created_at'])) ?></span>
                                            </div>
                                            <p class="text-slate-600 text-xs leading-relaxed"><?= nl2br(esc($comment['content'])) ?></p>
                                        </div>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>

            </div><!-- /padding -->
        </div><!-- /scroll area -->
    </main>

    <script>
    // ── PHP Data ────────────────────────────────────────────────────────────
    const growthLabels   = <?= json_encode($growthLabels) ?>;
    const growthLikes    = <?= json_encode($growthLikes) ?>;
    const growthBmarks   = <?= json_encode($growthBmarks) ?>;
    const growthComments = <?= json_encode($growthComments) ?>;
    const distData       = <?= json_encode($distributionData) ?>;

    // Purchase analytics data
    const revLabels          = <?= json_encode($revenueDailyLabels) ?>;
    const revDailyCC         = <?= json_encode($revenueDailyCC) ?>;
    const revDailyUnlocks    = <?= json_encode($revenueDailyUnlocks) ?>;
    const unlocksByHour      = <?= json_encode($unlocksByHour) ?>;
    const unlocksByDay       = <?= json_encode($unlocksByDay) ?>;
    const dayNames           = <?= json_encode($dayNames) ?>;

    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color       = '#94A3B8';

    // ── Interaction Growth Chart ─────────────────────────────────────────────
    const ctxGrowth = document.getElementById('growthChart').getContext('2d');
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
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15,23,42,0.95)',
                    titleFont: { size: 11, weight: '700' },
                    bodyFont: { size: 11 },
                    padding: 12,
                    cornerRadius: 10,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(226,232,240,0.5)', drawBorder: false },
                    ticks: { font: { size: 10, weight: '600' }, padding: 8, maxTicksLimit: 5, callback: v => v % 1 === 0 ? v : '' },
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

    // ── Interaction Distribution Donut ───────────────────────────────────────
    const ctxDist = document.getElementById('distChart').getContext('2d');
    new Chart(ctxDist, {
        type: 'doughnut',
        data: {
            labels: ['Pembaca Pasif', 'Suka', 'Disimpan', 'Komentar'],
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

    // ── Revenue + Unlock Trend Chart ────────────────────────────────────────
    const ctxRevenue = document.getElementById('revenueChart');
    if (ctxRevenue) {
        const gradRev = ctxRevenue.getContext('2d').createLinearGradient(0, 0, 0, 200);
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
                    }
                },
                scales: {
                    yCC: {
                        type: 'linear', position: 'left', beginAtZero: true,
                        grid: { color: 'rgba(226,232,240,0.5)' },
                        ticks: { font: { size: 10, weight: '600' }, callback: v => v % 1 === 0 ? v + ' CC' : '' },
                        border: { display: false }
                    },
                    yUnlocks: {
                        type: 'linear', position: 'right', beginAtZero: true,
                        grid: { display: false },
                        ticks: { font: { size: 10, weight: '600' }, callback: v => v % 1 === 0 ? v + 'x' : '' },
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

    // ── Hour of day Heatmap ──────────────────────────────────────────────────
    const ctxHour = document.getElementById('hourChart');
    if (ctxHour) {
        const peakHourIdx = unlocksByHour.indexOf(Math.max(...unlocksByHour));
        const hourColors  = unlocksByHour.map((_, i) => i === peakHourIdx ? '#8B5CF6' : 'rgba(139,92,246,0.25)');
        const hourLabels  = Array.from({length: 24}, (_, i) => `${String(i).padStart(2,'0')}:00`);

        new Chart(ctxHour.getContext('2d'), {
            type: 'bar',
            data: {
                labels: hourLabels,
                datasets: [{
                    data: unlocksByHour,
                    backgroundColor: hourColors,
                    borderRadius: 4,
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
                            callback: (val, idx) => idx % 4 === 0 ? hourLabels[idx] : ''
                        },
                        border: { display: false }
                    }
                }
            }
        });
    }

    // ── Day of week Chart ────────────────────────────────────────────────────
    const ctxDay = document.getElementById('dayChart');
    if (ctxDay) {
        const maxDayVal = Math.max(...unlocksByDay);
        const dayColors = unlocksByDay.map(v => v === maxDayVal && maxDayVal > 0 ? '#10B981' : 'rgba(16,185,129,0.25)');

        new Chart(ctxDay.getContext('2d'), {
            type: 'bar',
            data: {
                labels: dayNames,
                datasets: [{
                    data: unlocksByDay,
                    backgroundColor: dayColors,
                    borderRadius: 8,
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

    // ── Animate progress bars on scroll ──────────────────────────────────────
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
