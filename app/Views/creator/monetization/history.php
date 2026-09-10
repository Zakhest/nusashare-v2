<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= $title ?? 'Riwayat Transaksi - NusaShare' ?></title>

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">
    <link rel="icon" href="<?= base_url('assets/icon/logonus.png') ?>" type="image/x-icon">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F8FAFC; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #E2E8F0; border-radius: 10px; }

        /* DataTables overrides */
        #txTable_wrapper { font-family: 'Inter', sans-serif; }
        #txTable_filter { display: none; } /* we use custom search */
        #txTable_length { display: none; }
        #txTable_info { font-size: 11px; color: #94a3b8; padding: 10px 0 0; }
        #txTable_paginate { padding-top: 10px; }
        #txTable_paginate .paginate_button {
            display: inline-flex; align-items: center; justify-content: center;
            width: 32px; height: 32px; border-radius: 10px; font-size: 12px; font-weight: 700;
            border: 1px solid #e2e8f0 !important; background: white !important;
            color: #64748b !important; margin: 0 2px; cursor: pointer;
            transition: all .2s;
        }
        #txTable_paginate .paginate_button:hover { background: #eef2ff !important; color: #4f46e5 !important; border-color: #c7d2fe !important; }
        #txTable_paginate .paginate_button.current { background: #4f46e5 !important; color: white !important; border-color: #4f46e5 !important; }
        #txTable_paginate .paginate_button.disabled { opacity: .35; cursor: not-allowed; }
        table.dataTable { border-collapse: collapse !important; }
        table.dataTable thead th { border-bottom: 1px solid #f1f5f9 !important; }
        table.dataTable.no-footer { border-bottom: none !important; }
        table.dataTable tbody tr:hover td { background: #f8fafc !important; }
        table.dataTable tbody tr td { border-bottom: 1px solid #f8fafc !important; vertical-align: middle; }

        /* Detail modal */
        #detailModal { transition: opacity .25s, transform .25s; }
        #detailModal.open { opacity: 1; pointer-events: all; }
        #detailModal.closed { opacity: 0; pointer-events: none; }
        #modalCard { transition: transform .25s; }
        #detailModal.open #modalCard { transform: scale(1); }
        #detailModal.closed #modalCard { transform: scale(.95); }

        #reportModal { transition: opacity .2s; }
        #reportModal.open { opacity: 1; pointer-events: all; }
        #reportModal.closed { opacity: 0; pointer-events: none; }

        /* Mobile transaction card */
        .tx-card {
            background: white;
            border-radius: 20px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 1px 4px rgba(0,0,0,.04);
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: box-shadow .2s, transform .15s;
            cursor: pointer;
        }
        .tx-card:active { transform: scale(.985); box-shadow: 0 0 0 2px #e0e7ff; }
        .tx-card-icon {
            width: 44px; height: 44px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .tx-card-body { flex: 1; min-width: 0; }
        .tx-card-right { display: flex; flex-direction: column; align-items: flex-end; gap: 6px; flex-shrink: 0; }

        /* Mobile search filter visibility */
        #mobileCardList .no-result-mobile {
            text-align: center; color: #94a3b8; font-size: 13px;
            padding: 32px 0; display: none;
        }

        /* Struk print */
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
        }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <?= view('creator/_sidebar', [
        'activePage'     => 'monetization',
        'user'           => $user,
        'creatorProfile' => $creatorProfile,
        'username'       => $username
    ]) ?>

    <!-- Main -->
    <main class="flex-1 flex flex-col h-full overflow-hidden bg-slate-50">

        <!-- Top Nav -->
        <header class="bg-white border-b border-slate-200 px-4 md:px-8 py-3 md:py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="<?= base_url('creator/monetization') ?>" class="p-1.5 hover:bg-slate-50 rounded-xl transition-all text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <div class="min-w-0">
                    <h2 class="text-base md:text-lg font-bold text-slate-900">Riwayat Transaksi</h2>
                    <p class="text-[10px] text-slate-400">Semua transaksi CC kamu tercatat di sini</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <!-- Export Buttons -->
                <a id="btnExportExcel" href="<?= base_url('creator/monetization/export/excel') ?>"
                   class="no-print flex items-center gap-1.5 px-3 py-2 bg-emerald-600 text-white rounded-xl font-bold text-xs hover:bg-emerald-700 transition-all">
                    <span class="material-symbols-outlined text-sm">table_view</span>
                    <span class="hidden sm:inline">Excel</span>
                </a>
                <button id="btnExportPdf" type="button"
                   class="no-print flex items-center gap-1.5 px-3 py-2 bg-rose-600 text-white rounded-xl font-bold text-xs hover:bg-rose-700 transition-all">
                    <span class="material-symbols-outlined text-sm">picture_as_pdf</span>
                    <span class="hidden sm:inline">PDF</span>
                </button>
                <div class="hidden md:flex items-center gap-3 border-l pl-4 border-slate-100">
                    <div class="text-right">
                        <p class="text-xs font-bold text-slate-900"><?= $creatorProfile['display_name'] ?? $username ?></p>
                        <p class="text-[10px] text-slate-500 italic">Mode Kreator</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Body -->
        <div class="flex-1 overflow-y-auto p-4 md:p-8 pb-24 lg:pb-8 custom-scrollbar">

            <!-- Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-5 mb-6">
                <div class="bg-white rounded-2xl p-4 md:p-5 shadow-sm border border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Transaksi</p>
                    <p class="text-2xl font-black text-slate-900"><?= count($history) ?></p>
                </div>
                <div class="bg-emerald-50 rounded-2xl p-4 md:p-5 shadow-sm border border-emerald-100">
                    <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest mb-1">Total Masuk</p>
                    <p class="text-xl md:text-2xl font-black text-emerald-700">+<?= number_format($totalIn) ?> <span class="text-sm font-bold">CC</span></p>
                    <p class="text-[10px] text-emerald-500 mt-1">Rp <?= number_format($totalIn * 10) ?></p>
                </div>
                <div class="bg-rose-50 rounded-2xl p-4 md:p-5 shadow-sm border border-rose-100">
                    <p class="text-[10px] font-bold text-rose-400 uppercase tracking-widest mb-1">Total Keluar</p>
                    <p class="text-xl md:text-2xl font-black text-rose-600">-<?= number_format($totalOut) ?> <span class="text-sm font-bold">CC</span></p>
                    <p class="text-[10px] text-rose-400 mt-1">Rp <?= number_format($totalOut * 10) ?></p>
                </div>
                <div class="bg-indigo-50 rounded-2xl p-4 md:p-5 shadow-sm border border-indigo-100">
                    <p class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest mb-1">Saldo Saat Ini</p>
                    <p class="text-xl md:text-2xl font-black text-indigo-700"><?= number_format($balance) ?> <span class="text-sm font-bold">CC</span></p>
                    <p class="text-[10px] text-indigo-400 mt-1">Rp <?= number_format($balance * 10) ?></p>
                </div>
            </div>

            <!-- Filter Bar -->
            <form id="filterForm" method="GET" action="<?= base_url('creator/monetization/history') ?>"
                  class="bg-white rounded-2xl p-3 md:p-4 shadow-sm border border-slate-100 mb-5 flex flex-col sm:flex-row flex-wrap gap-3 items-end">

                <!-- Search -->
                <div class="flex items-center gap-2 bg-slate-50 px-4 py-2 rounded-xl flex-1 min-w-[180px]">
                    <span class="material-symbols-outlined text-slate-400 text-lg">search</span>
                    <input id="globalSearch" type="text" placeholder="Cari keterangan..." value=""
                           class="bg-transparent border-none focus:outline-none text-sm w-full text-slate-600">
                </div>

                <!-- Type filter -->
                <div class="flex flex-col gap-1 min-w-[130px]">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider pl-1">Tipe</label>
                    <select name="type" id="filterType" class="bg-slate-50 px-3 py-2 rounded-xl text-sm font-medium text-slate-600 focus:outline-none border border-slate-100">
                        <option value="">Semua</option>
                        <option value="in"  <?= ($filters['type'] ?? '') === 'in'  ? 'selected' : '' ?>>Masuk</option>
                        <option value="out" <?= ($filters['type'] ?? '') === 'out' ? 'selected' : '' ?>>Keluar</option>
                    </select>
                </div>

                <!-- Category filter -->
                <div class="flex flex-col gap-1 min-w-[130px]">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider pl-1">Kategori</label>
                    <select name="category" id="filterCat" class="bg-slate-50 px-3 py-2 rounded-xl text-sm font-medium text-slate-600 focus:outline-none border border-slate-100">
                        <option value="">Semua</option>
                        <option value="download" <?= ($filters['category'] ?? '') === 'download' ? 'selected' : '' ?>>Download</option>
                        <option value="unlock"   <?= ($filters['category'] ?? '') === 'unlock'   ? 'selected' : '' ?>>Unlock</option>
                        <option value="topup"    <?= ($filters['category'] ?? '') === 'topup'    ? 'selected' : '' ?>>Top Up</option>
                        <option value="withdraw" <?= ($filters['category'] ?? '') === 'withdraw' ? 'selected' : '' ?>>Penarikan</option>
                    </select>
                </div>

                <!-- Date range -->
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider pl-1">Dari</label>
                    <input type="date" name="date_from" value="<?= esc($filters['date_from'] ?? '') ?>"
                           class="bg-slate-50 px-3 py-2 rounded-xl text-sm font-medium text-slate-600 focus:outline-none border border-slate-100">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider pl-1">Sampai</label>
                    <input type="date" name="date_to" value="<?= esc($filters['date_to'] ?? '') ?>"
                           class="bg-slate-50 px-3 py-2 rounded-xl text-sm font-medium text-slate-600 focus:outline-none border border-slate-100">
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-xl font-bold text-xs hover:bg-indigo-700 transition-all flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">filter_alt</span>
                        Filter
                    </button>
                    <?php if (!empty(array_filter($filters))): ?>
                    <a href="<?= base_url('creator/monetization/history') ?>"
                       class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl font-bold text-xs hover:bg-slate-200 transition-all flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">close</span>
                        Reset
                    </a>
                    <?php endif; ?>
                </div>
            </form>

            <!-- ══ MOBILE CARD LIST (only on < md) ══ -->
            <div id="mobileCardList" class="block md:hidden space-y-3 mb-6">
                <?php if (!empty($history)): ?>
                <?php foreach ($history as $row):
                    $isIn      = $row['type'] === 'in';
                    $cat       = strtolower($row['category'] ?? '');
                    $icon      = $cat === 'download' ? 'download' : ($cat === 'unlock' ? ($isIn ? 'lock_open' : 'lock') : ($isIn ? 'arrow_circle_down' : 'arrow_circle_up'));
                    $iconBg    = $isIn ? 'bg-emerald-50' : 'bg-rose-50';
                    $iconColor = $isIn ? 'text-emerald-500' : 'text-rose-400';
                    $desc      = $row['description'] ?? '';

                    // Parse description
                    $actionLabel = ''; $userPart = ''; $titlePart = '';
                    if ($cat === 'topup' || $cat === 'withdraw') {
                        $actionLabel = $cat === 'topup' ? 'Top Up' : 'Penarikan';
                        $titlePart   = $desc;
                    } elseif ($isIn) {
                        if (preg_match('/^(Karya diunduh oleh|Karya dibuka oleh|Bab dibuka oleh)\s+(@\S+|user_id:\d+):\s*(.+)$/u', $desc, $m)) {
                            $actionLabel = $m[1]; $userPart = $m[2]; $titlePart = $m[3];
                        } else {
                            if (str_starts_with($desc, 'Karya diunduh')) { $actionLabel = 'Karya diunduh'; }
                            elseif (str_starts_with($desc, 'Bab dibuka'))   { $actionLabel = 'Bab dibuka'; }
                            else                                             { $actionLabel = 'Karya dibuka'; }
                            $titlePart = preg_replace('/^(Karya diunduh|Karya dibuka|Bab dibuka):\s*/u', '', $desc);
                        }
                    } else {
                        if (preg_match('/^(Download karya|Buka karya|Buka bab)\s+(@\S+|user_id:\d+):\s*(.+)$/u', $desc, $m)) {
                            $actionLabel = $m[1]; $userPart = '';
                            $titlePart = $m[3];
                        } elseif (preg_match('/^(.+?):\s*(.+)$/u', $desc, $m)) {
                            $actionLabel = $m[1]; $titlePart = $m[2];
                        } else {
                            $actionLabel = ucfirst($cat); $titlePart = $desc;
                        }
                    }
                    $userColor = $isIn ? 'text-indigo-600' : 'text-rose-500';
                    $txCode    = 'NST-' . str_pad($row['id'], 8, '0', STR_PAD_LEFT);
                ?>
                <div class="tx-card"
                     data-id="<?= $row['id'] ?>"
                     data-date="<?= date('d M Y', strtotime($row['date'])) ?>"
                     data-time="<?= date('H:i:s', strtotime($row['date'])) ?>"
                     data-type="<?= $row['type'] ?>"
                     data-cat="<?= esc($row['category']) ?>"
                     data-amount="<?= $row['amount'] ?>"
                     data-amountrp="<?= $row['amount_rp'] ?>"
                     data-desc="<?= esc($desc) ?>"
                     data-txcode="<?= $txCode ?>"
                     data-searchtext="<?= esc(strtolower($actionLabel . ' ' . $userPart . ' ' . $titlePart . ' ' . $cat)) ?>"
                     onclick="openDetailFromCard(this)"
                >
                    <!-- Icon -->
                    <div class="tx-card-icon <?= $iconBg ?>">
                        <span class="material-symbols-outlined text-[20px] <?= $iconColor ?>"><?= $icon ?></span>
                    </div>

                    <!-- Body -->
                    <div class="tx-card-body">
                        <div class="flex items-center gap-1.5 mb-0.5">
                            <?php if ($userPart): ?>
                                <p class="text-[11px] font-bold text-slate-500 leading-tight truncate">
                                    <?= esc($actionLabel) ?>
                                    <span class="<?= $userColor ?> font-extrabold"><?= esc($userPart) ?></span>
                                </p>
                            <?php else: ?>
                                <p class="text-[11px] font-bold text-slate-500 leading-tight"><?= esc($actionLabel) ?></p>
                            <?php endif; ?>
                        </div>
                        <p class="text-[13px] font-semibold text-slate-900 leading-snug line-clamp-1"><?= esc($titlePart) ?></p>
                        <div class="flex items-center gap-1.5 mt-1">
                            <span class="text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full
                                <?= $isIn ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-500' ?>">
                                <?= esc(ucfirst($cat)) ?>
                            </span>
                            <span class="text-[10px] text-slate-400"><?= date('d M Y · H:i', strtotime($row['date'])) ?></span>
                        </div>
                    </div>

                    <!-- Right: amount + receipt btn -->
                    <div class="tx-card-right">
                        <div class="text-right">
                            <p class="text-sm font-black <?= $isIn ? 'text-emerald-600' : 'text-rose-600' ?> whitespace-nowrap">
                                <?= $isIn ? '+' : '-' ?><?= number_format($row['amount']) ?> <span class="text-[9px] text-slate-400">CC</span>
                            </p>
                            <p class="text-[10px] text-slate-400">Rp <?= number_format($row['amount_rp']) ?></p>
                        </div>
                        <a href="<?= base_url('creator/monetization/receipt/' . $row['id']) ?>" target="_blank"
                           onclick="event.stopPropagation()"
                           class="no-print flex items-center justify-center w-7 h-7 rounded-lg bg-slate-100 text-slate-400 hover:bg-indigo-50 hover:text-indigo-500 transition-all" title="Cetak Struk">
                            <span class="material-symbols-outlined text-[15px]">receipt</span>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <div class="text-center py-12 text-slate-400 text-sm">
                    <span class="material-symbols-outlined text-4xl mb-2 block">receipt_long</span>
                    Belum ada transaksi
                </div>
                <?php endif; ?>
                <p class="no-result-mobile" id="noResultMobile">Tidak ada hasil yang cocok.</p>
            </div>

            <!-- ══ DESKTOP TABLE (hidden on mobile) ══ -->
            <div class="hidden md:block bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                <table id="txTable" class="w-full text-left border-collapse min-w-[720px]">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-5 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest w-10">#</th>
                            <th class="px-5 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Waktu</th>
                            <th class="px-5 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Keterangan</th>
                            <th class="px-5 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-right">Jumlah</th>
                            <th class="px-5 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center">Tipe</th>
                            <th class="px-5 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center no-print">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php if (!empty($history)): ?>
                        <?php foreach ($history as $row):
                            $isIn      = $row['type'] === 'in';
                            $cat       = strtolower($row['category'] ?? '');
                            $icon      = $cat === 'download' ? 'download' : ($cat === 'unlock' ? ($isIn ? 'lock_open' : 'lock') : ($isIn ? 'arrow_circle_down' : 'arrow_circle_up'));
                            $iconBg    = $isIn ? 'bg-emerald-50' : 'bg-rose-50';
                            $iconColor = $isIn ? 'text-emerald-500' : 'text-rose-400';
                            $desc      = $row['description'] ?? '';

                            // Parse description
                            $actionLabel = ''; $userPart = ''; $titlePart = '';
                            if ($cat === 'topup' || $cat === 'withdraw') {
                                $actionLabel = $cat === 'topup' ? 'Top Up' : 'Penarikan';
                                $titlePart   = $desc;
                            } elseif ($isIn) {
                                if (preg_match('/^(Karya diunduh oleh|Karya dibuka oleh|Bab dibuka oleh)\s+(@\S+|user_id:\d+):\s*(.+)$/u', $desc, $m)) {
                                    $actionLabel = $m[1]; $userPart = $m[2]; $titlePart = $m[3];
                                } else {
                                    if (str_starts_with($desc, 'Karya diunduh')) { $actionLabel = 'Karya diunduh'; }
                                    elseif (str_starts_with($desc, 'Bab dibuka'))  { $actionLabel = 'Bab dibuka'; }
                                    else                                           { $actionLabel = 'Karya dibuka'; }
                                    $titlePart = preg_replace('/^(Karya diunduh|Karya dibuka|Bab dibuka):\s*/u', '', $desc);
                                }
                            } else {
                                if (preg_match('/^(Download karya|Buka karya|Buka bab)\s+(@\S+|user_id:\d+):\s*(.+)$/u', $desc, $m)) {
                                    $actionLabel = $m[1]; $userPart = '';
                                    $titlePart = $m[3];
                                } elseif (preg_match('/^(.+?):\s*(.+)$/u', $desc, $m)) {
                                    $actionLabel = $m[1]; $titlePart = $m[2];
                                } else {
                                    $actionLabel = ucfirst($cat); $titlePart = $desc;
                                }
                            }
                            $userColor = $isIn ? 'text-indigo-600' : 'text-rose-500';
                            $txCode    = 'NST-' . str_pad($row['id'], 8, '0', STR_PAD_LEFT);
                        ?>
                        <tr class="hover:bg-slate-50/50 transition-colors cursor-pointer"
                            data-id="<?= $row['id'] ?>"
                            data-date="<?= date('d M Y', strtotime($row['date'])) ?>"
                            data-time="<?= date('H:i:s', strtotime($row['date'])) ?>"
                            data-type="<?= $row['type'] ?>"
                            data-cat="<?= esc($row['category']) ?>"
                            data-amount="<?= $row['amount'] ?>"
                            data-amountrp="<?= $row['amount_rp'] ?>"
                            data-desc="<?= esc($desc) ?>"
                            data-txcode="<?= $txCode ?>"
                        >
                            <td class="px-5 py-4 text-xs text-slate-400 font-bold"><?= $row['no'] ?></td>
                            <td class="px-5 py-4">
                                <p class="text-sm font-bold text-slate-900 whitespace-nowrap"><?= date('d M Y', strtotime($row['date'])) ?></p>
                                <p class="text-[10px] text-slate-400"><?= date('H:i', strtotime($row['date'])) ?> WIB</p>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-start gap-2.5">
                                    <div class="w-7 h-7 rounded-xl <?= $iconBg ?> flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <span class="material-symbols-outlined text-[15px] <?= $iconColor ?>"><?= $icon ?></span>
                                    </div>
                                    <div class="min-w-0">
                                        <?php if ($userPart): ?>
                                            <p class="text-xs font-bold text-slate-700 leading-snug">
                                                <?= esc($actionLabel) ?>
                                                <span class="<?= $userColor ?> font-extrabold"><?= esc($userPart) ?></span>
                                            </p>
                                            <p class="text-[11px] text-slate-900 font-semibold mt-0.5 line-clamp-1"><?= esc($titlePart) ?></p>
                                        <?php else: ?>
                                            <p class="text-xs font-bold text-slate-500 leading-snug"><?= esc($actionLabel) ?></p>
                                            <p class="text-[11px] text-slate-900 font-semibold mt-0.5 line-clamp-1"><?= esc($titlePart) ?></p>
                                        <?php endif; ?>
                                        <p class="text-[9px] text-slate-400 uppercase tracking-wider mt-0.5 font-bold"><?= esc(ucfirst($cat)) ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <p class="text-sm font-black <?= $isIn ? 'text-emerald-600' : 'text-slate-800' ?> whitespace-nowrap">
                                    <?= $isIn ? '+' : '-' ?><?= number_format($row['amount']) ?> <span class="text-[10px] text-slate-400">CC</span>
                                </p>
                                <p class="text-[10px] text-slate-400">Rp <?= number_format($row['amount_rp']) ?></p>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <?php if ($isIn): ?>
                                    <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-bold rounded-full uppercase">Masuk</span>
                                <?php else: ?>
                                    <span class="px-3 py-1 bg-rose-50 text-rose-600 text-[10px] font-bold rounded-full uppercase">Keluar</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-4 no-print">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button onclick="openDetail(this)" data-row="<?= $row['no'] ?>"
                                            class="btn-detail p-2 text-indigo-500 hover:bg-indigo-50 rounded-lg transition-all" title="Lihat Detail">
                                        <span class="material-symbols-outlined text-lg">visibility</span>
                                    </button>
                                    <a href="<?= base_url('creator/monetization/receipt/' . $row['id']) ?>" target="_blank"
                                       class="p-2 text-slate-400 hover:bg-slate-50 hover:text-slate-600 rounded-lg transition-all" title="Cetak Struk">
                                        <span class="material-symbols-outlined text-lg">receipt</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                </div>

                <!-- DataTables info + pagination injected here -->
                <div id="dtFooter" class="px-5 py-3 border-t border-slate-50 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2"></div>
            </div> <!-- /desktop table -->

        </div><!-- /body -->
    </main>

    <div id="reportModal" class="closed fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm no-print">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6">
            <div class="flex items-start justify-between gap-4 mb-5">
                <div>
                    <p class="text-base font-bold text-slate-900">Cetak laporan PDF</p>
                    <p class="text-xs text-slate-400 mt-1">Pilih cakupan transaksi yang ingin dicetak.</p>
                </div>
                <button type="button" onclick="closeReportModal()" class="p-1 text-slate-400 hover:text-slate-700" title="Tutup">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="space-y-3">
                <button type="button" onclick="printCurrentReport()" class="w-full flex items-center gap-3 text-left p-4 border border-slate-200 rounded-xl hover:border-indigo-300 hover:bg-indigo-50 transition-all">
                    <span class="material-symbols-outlined text-indigo-600">print</span>
                    <span><span class="block text-sm font-bold text-slate-800">Cetak saja</span><span class="block text-[11px] text-slate-400 mt-0.5">Cetak transaksi sesuai filter yang aktif.</span></span>
                </button>

                <div class="p-4 border border-slate-200 rounded-xl">
                    <label for="reportMonth" class="block text-sm font-bold text-slate-800">Cetak periode per bulan</label>
                    <input id="reportMonth" type="month" class="mt-3 w-full bg-slate-50 px-3 py-2 rounded-lg text-sm text-slate-700 border border-slate-200 focus:outline-none focus:border-indigo-400">
                    <button type="button" onclick="printMonthlyReport()" class="mt-3 w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-rose-600 text-white rounded-lg font-bold text-xs hover:bg-rose-700 transition-all">
                        <span class="material-symbols-outlined text-sm">calendar_month</span>
                        Cetak periode ini
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════
         DETAIL MODAL
    ══════════════════════════════════════════════ -->
    <div id="detailModal" class="closed fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm no-print">
        <div id="modalCard" class="bg-white rounded-[28px] shadow-2xl w-full max-w-md overflow-hidden">
            <!-- Header -->
            <div id="modalHeader" class="p-6 pb-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div id="modalIconBox" class="w-10 h-10 rounded-2xl flex items-center justify-center">
                        <span id="modalIcon" class="material-symbols-outlined text-xl"></span>
                    </div>
                    <div>
                        <p id="modalType" class="text-xs font-bold uppercase tracking-widest"></p>
                        <p id="modalCode" class="text-[10px] text-slate-400 font-mono mt-0.5"></p>
                    </div>
                </div>
                <button onclick="closeDetail()" class="p-2 hover:bg-slate-100 rounded-xl transition-all text-slate-400">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <!-- Amount -->
            <div class="px-6 py-4 bg-slate-50 mx-6 rounded-2xl mb-4">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Jumlah</p>
                <p id="modalAmountCC" class="text-3xl font-black"></p>
                <p id="modalAmountRp" class="text-sm text-slate-400 mt-0.5"></p>
            </div>

            <!-- Details -->
            <div class="px-6 space-y-3 pb-4">
                <div class="flex justify-between items-start gap-3">
                    <span class="text-xs text-slate-400 font-semibold flex-shrink-0">Tanggal</span>
                    <span id="modalDate" class="text-xs font-bold text-slate-900 text-right"></span>
                </div>
                <div class="flex justify-between items-start gap-3">
                    <span class="text-xs text-slate-400 font-semibold flex-shrink-0">Waktu</span>
                    <span id="modalTime" class="text-xs font-bold text-slate-900 text-right"></span>
                </div>
                <div class="flex justify-between items-start gap-3">
                    <span class="text-xs text-slate-400 font-semibold flex-shrink-0">Kategori</span>
                    <span id="modalCat" class="text-xs font-bold text-slate-900 text-right"></span>
                </div>
                <div class="flex justify-between items-start gap-3">
                    <span class="text-xs text-slate-400 font-semibold flex-shrink-0">Keterangan</span>
                    <span id="modalDesc" class="text-xs font-bold text-slate-900 text-right max-w-[240px]"></span>
                </div>
                <div class="flex justify-between items-start gap-3">
                    <span class="text-xs text-slate-400 font-semibold flex-shrink-0">Status</span>
                    <span class="text-xs font-bold text-emerald-600">&#10003; Selesai</span>
                </div>
            </div>

            <!-- Actions -->
            <div class="px-6 py-4 border-t border-slate-100 flex gap-2">
                <a id="modalReceiptLink" href="#" target="_blank"
                   class="flex-1 flex items-center justify-center gap-2 py-3 bg-indigo-600 text-white rounded-2xl font-bold text-sm hover:bg-indigo-700 transition-all">
                    <span class="material-symbols-outlined text-base">receipt</span>
                    Cetak Struk
                </a>
                <button onclick="closeDetail()"
                        class="px-5 py-3 bg-slate-100 text-slate-600 rounded-2xl font-bold text-sm hover:bg-slate-200 transition-all">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
    // ─── DataTables Init (desktop only) ─────────────────────────────────────────
    $(document).ready(function () {
        const table = $('#txTable').DataTable({
            pageLength: 20,
            order: [],
            columnDefs: [
                { orderable: false, targets: [5] }, // Aksi column
                { searchable: false, targets: [4, 5] }
            ],
            language: {
                emptyTable: 'Tidak ada transaksi ditemukan.',
                zeroRecords: 'Tidak ada hasil yang cocok.',
                info: 'Menampilkan _START_–_END_ dari _TOTAL_ transaksi',
                infoEmpty: '0 transaksi',
                infoFiltered: '(difilter dari _MAX_ total)',
                paginate: { previous: '&#8249;', next: '&#8250;' },
            },
            dom: 'tip',  // t=table, i=info, p=pagination — we control search ourselves
            drawCallback: function () {
                // Move info and pagination to our footer div
                $('#dtFooter').html('');
                $('#txTable_info').appendTo('#dtFooter');
                $('#txTable_paginate').appendTo('#dtFooter');
            }
        });

        // Custom search box — drives both desktop table and mobile cards
        $('#globalSearch').on('input', function () {
            const q = this.value.toLowerCase().trim();
            // Desktop: DataTable search
            table.search(q).draw();
            // Mobile: filter cards
            filterMobileCards(q);
        });

        // Update export links with current server-side filters on every search
        function updateExportLinks() {
            const params = new URLSearchParams(window.location.search);
            const base_excel = '<?= base_url('creator/monetization/export/excel') ?>';
            $('#btnExportExcel').attr('href', base_excel + (params.toString() ? '?' + params.toString() : ''));
        }
        updateExportLinks();

        $('#btnExportPdf').on('click', function () {
            openReportModal();
        });
    });

    function openReportModal() {
        const activeMonth = new URLSearchParams(window.location.search).get('date_from') || '';
        document.getElementById('reportMonth').value = activeMonth ? activeMonth.slice(0, 7) : new Date().toISOString().slice(0, 7);
        document.getElementById('reportModal').classList.remove('closed');
        document.getElementById('reportModal').classList.add('open');
    }

    function closeReportModal() {
        document.getElementById('reportModal').classList.remove('open');
        document.getElementById('reportModal').classList.add('closed');
    }

    function openPrintReport(params) {
        const base = '<?= base_url('creator/monetization/report/print') ?>';
        window.open(base + (params.toString() ? '?' + params.toString() : ''), '_blank', 'noopener');
        closeReportModal();
    }

    function printCurrentReport() {
        openPrintReport(new URLSearchParams(window.location.search));
    }

    function printMonthlyReport() {
        const month = document.getElementById('reportMonth').value;
        if (!month) return;

        const params = new URLSearchParams(window.location.search);
        const [year, monthNumber] = month.split('-').map(Number);
        const lastDay = new Date(year, monthNumber, 0).getDate();
        params.set('date_from', month + '-01');
        params.set('date_to', month + '-' + String(lastDay).padStart(2, '0'));
        openPrintReport(params);
    }

    // ─── Mobile card search filter ───────────────────────────────────────────────
    function filterMobileCards(q) {
        const cards = document.querySelectorAll('#mobileCardList .tx-card');
        let visible = 0;
        cards.forEach(c => {
            const text = (c.dataset.searchtext || '').toLowerCase();
            const show = !q || text.includes(q);
            c.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        const noRes = document.getElementById('noResultMobile');
        if (noRes) noRes.style.display = (q && visible === 0) ? 'block' : 'none';
    }

    // ─── Detail Modal ────────────────────────────────────────────────────────────
    // Called from desktop table button
    function openDetail(btn) {
        const row = btn.closest('tr');
        openDetailFromData(row.dataset);
    }

    // Called from mobile card (onclick="openDetailFromCard(this)")
    function openDetailFromCard(card) {
        openDetailFromData(card.dataset);
    }

    // Core modal populate — works for both table rows and mobile cards
    function openDetailFromData(d) {
        const id       = d.id;
        const date     = d.date;
        const time     = d.time;
        const type     = d.type;
        const cat      = d.cat;
        const amount   = parseInt(d.amount);
        const amountrp = parseInt(d.amountrp);
        const desc     = d.desc;
        const txcode   = d.txcode;

        const isIn = type === 'in';
        const sign = isIn ? '+' : '-';

        // Icon
        let icon = 'receipt';
        if (cat === 'download') icon = 'download';
        else if (cat === 'unlock') icon = isIn ? 'lock_open' : 'lock';

        document.getElementById('modalIcon').textContent = icon;
        document.getElementById('modalIconBox').className =
            'w-10 h-10 rounded-2xl flex items-center justify-center ' + (isIn ? 'bg-emerald-50 text-emerald-500' : 'bg-rose-50 text-rose-400');
        document.getElementById('modalIcon').className =
            'material-symbols-outlined text-xl ' + (isIn ? 'text-emerald-500' : 'text-rose-400');

        document.getElementById('modalType').textContent  = (isIn ? '▲ MASUK' : '▼ KELUAR') + ' · ' + cat.toUpperCase();
        document.getElementById('modalType').className    = 'text-xs font-bold uppercase tracking-widest ' + (isIn ? 'text-emerald-600' : 'text-rose-500');
        document.getElementById('modalCode').textContent  = txcode;

        document.getElementById('modalAmountCC').textContent = sign + amount.toLocaleString('id-ID') + ' CC';
        document.getElementById('modalAmountCC').className   = 'text-3xl font-black ' + (isIn ? 'text-emerald-600' : 'text-rose-600');
        document.getElementById('modalAmountRp').textContent = '≈ Rp ' + amountrp.toLocaleString('id-ID');

        document.getElementById('modalDate').textContent = date;
        document.getElementById('modalTime').textContent = time + ' WIB';
        document.getElementById('modalCat').textContent  = cat.charAt(0).toUpperCase() + cat.slice(1);
        document.getElementById('modalDesc').textContent = desc;

        document.getElementById('modalReceiptLink').href = '<?= base_url('creator/monetization/receipt/') ?>' + id;

        const modal = document.getElementById('detailModal');
        modal.classList.remove('closed');
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeDetail() {
        const modal = document.getElementById('detailModal');
        modal.classList.remove('open');
        modal.classList.add('closed');
        document.body.style.overflow = '';
    }

    // Close on backdrop click
    document.getElementById('detailModal').addEventListener('click', function(e) {
        if (e.target === this) closeDetail();
    });

    // ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeDetail();
    });
    </script>
</body>
</html>
