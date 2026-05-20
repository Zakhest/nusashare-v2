<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= $title ?? 'Monetisasi Kreator - NusaShare' ?></title>
    
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
        'activePage'     => 'monetization',
        'user'           => $user,
        'creatorProfile' => $creatorProfile,
        'username'       => $username
    ]) ?>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-full overflow-hidden bg-slate-50">
        <!-- Top Nav -->
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-900">Penghasilan & Monetisasi</h2>
            
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
                <h1 class="text-2xl font-bold text-slate-900">Dashboard Keuangan</h1>
                <p class="text-slate-500 text-sm mt-1">Kelola saldo Cooling Credits dan tarik penghasilanmu.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
                <!-- Balance Card -->
                <div class="lg:col-span-1">
                    <div class="bg-slate-900 rounded-[32px] p-8 text-white relative overflow-hidden shadow-xl shadow-slate-200">
                        <div class="relative z-10">
                            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-2">Saldo Saat Ini</p>
                            <div class="flex items-baseline gap-2 mb-8">
                                <span class="text-4xl font-black"><?= number_format($balance) ?></span>
                                <span class="text-indigo-400 font-bold text-sm uppercase">CC</span>
                            </div>
                            
                            <p class="text-slate-500 text-[10px] mb-6 italic">Setara dengan Rp <?= number_format($balance * 10) ?></p>

                            <button class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold text-sm transition-all flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-lg">account_balance_wallet</span>
                                Tarik Saldo
                            </button>
                        </div>
                        
                        <!-- Decorative Circles -->
                        <div class="absolute -right-16 -top-16 w-48 h-48 bg-indigo-500/10 rounded-full blur-3xl"></div>
                        <div class="absolute -left-16 -bottom-16 w-48 h-48 bg-purple-500/10 rounded-full blur-3xl"></div>
                    </div>
                </div>

                <!-- Monthly Summary -->
                <div class="lg:col-span-2">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 h-full">
                        <div class="bg-white p-8 rounded-[32px] shadow-sm border border-slate-100 flex flex-col justify-between">
                            <div>
                                <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-4">
                                    <span class="material-symbols-outlined">payments</span>
                                </div>
                                <p class="text-slate-500 text-[10px] font-bold uppercase tracking-wider mb-1">Pendapatan Bulan Ini</p>
                                <p class="text-2xl font-black text-slate-900">Rp <?= number_format($monthlyEarnings['total']) ?></p>
                            </div>
                            <div class="mt-4 flex flex-col gap-1">
                                <div class="flex items-center gap-1 text-emerald-500 font-bold text-[10px]">
                                    <span class="material-symbols-outlined text-sm">trending_up</span>
                                    <span><?= number_format($monthlyEarnings['total_cc']) ?> CC bulan ini</span>
                                </div>
                                <p class="text-[9px] text-slate-400 font-medium italic">* Estimasi: 1 CC = Rp 10</p>
                            </div>
                        </div>

                        <div class="bg-white p-8 rounded-[32px] shadow-sm border border-slate-100">
                            <h3 class="font-bold text-slate-900 text-sm mb-4">Sumber Penghasilan</h3>
                            <div class="space-y-4">
                                <?php foreach ($monthlyEarnings['breakdown'] as $source => $amount): ?>
                                    <div>
                                        <div class="flex items-center justify-between text-xs mb-2">
                                            <span class="text-slate-500"><?= $source ?></span>
                                            <span class="font-bold text-slate-900">Rp <?= number_format($amount) ?></span>
                                        </div>
                                        <div class="h-1.5 w-full bg-slate-50 rounded-full overflow-hidden">
                                            <div class="h-full bg-indigo-500" style="width: <?= $monthlyEarnings['total'] > 0 ? ($amount / $monthlyEarnings['total']) * 100 : 0 ?>%"></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction History -->
            <div class="mb-6 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-lg">Riwayat Transaksi</h3>
                <button class="text-xs font-bold text-indigo-600 hover:underline">Lihat Semua</button>
            </div>
            
            <div class="bg-white rounded-[32px] shadow-sm border border-slate-100 overflow-hidden mb-10">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-8 py-5 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Waktu & Tanggal</th>
                            <th class="px-8 py-5 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Keterangan</th>
                            <th class="px-8 py-5 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center">Jumlah</th>
                            <th class="px-8 py-5 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-right">Tipe</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php if (empty($history)): ?>
                            <tr>
                                <td colspan="4" class="px-8 py-10 text-center text-slate-400 text-sm italic">Belum ada riwayat transaksi.</td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($history as $row): ?>
                            <tr class="hover:bg-slate-50/30 transition-colors">
                                <td class="px-8 py-5">
                                    <p class="text-sm font-bold text-slate-900"><?= date('d M Y', strtotime($row['date'])) ?></p>
                                    <p class="text-[10px] text-slate-400 mt-0.5"><?= date('H:i', strtotime($row['date'])) ?> WIB</p>
                                </td>
                                <td class="px-8 py-5">
                                    <p class="text-sm text-slate-600 font-bold"><?= esc($row['description'] ?? ucfirst($row['category'])) ?></p>
                                    <p class="text-[10px] text-slate-400 uppercase tracking-tighter"><?= $row['category'] ?></p>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <p class="text-sm font-black text-slate-900">
                                        <?= $row['type'] === 'in' ? '+' : '-' ?> <?= number_format($row['amount']) ?> 
                                        <span class="text-[10px] text-slate-400">CC</span>
                                    </p>
                                    <p class="text-[10px] text-slate-400">Rp <?= number_format($row['amount'] * 10) ?></p>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <?php if ($row['type'] === 'in'): ?>
                                        <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-bold rounded-full uppercase tracking-tight">Masuk</span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 bg-rose-50 text-rose-600 text-[10px] font-bold rounded-full uppercase tracking-tight">Keluar</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Monetization Tips -->
            <div class="bg-indigo-50 border border-indigo-100 rounded-[32px] p-8 flex flex-col md:flex-row items-center gap-8">
                <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-indigo-600 shadow-sm flex-shrink-0">
                    <span class="material-symbols-outlined text-3xl">lightbulb</span>
                </div>
                <div>
                    <h3 class="font-bold text-indigo-900 mb-1">Tingkatkan Penghasilanmu!</h3>
                    <p class="text-sm text-indigo-700/80 leading-relaxed">
                        Karya dengan lebih dari 10 chapter atau 1 gambar cenderung mendapatkan 2.5x lebih banyak dukungan (Support) dari pembaca. Fokuslah pada kualitas cerita dan gambar untuk meningkatkan interaksi.
                    </p>
                </div>
                <button class="px-6 py-3 bg-white text-indigo-600 hover:bg-indigo-50 rounded-2xl font-bold text-xs shadow-sm transition-all whitespace-nowrap">
                    Tips Monetisasi
                </button>
            </div>

        </div>
    </main>
</body>
</html>
