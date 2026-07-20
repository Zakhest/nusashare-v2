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
        <header class="bg-white border-b border-slate-200 px-4 md:px-8 py-3 md:py-4 flex items-center justify-between">
            <h2 class="text-base md:text-lg font-bold text-slate-900">Penghasilan &amp; Monetisasi</h2>
            
            <div class="flex items-center gap-3">
                <div class="hidden md:flex items-center gap-3 border-l pl-6 border-slate-100">
                    <div class="text-right">
                        <p class="text-xs font-bold text-slate-900"><?= $creatorProfile['display_name'] ?? $username ?></p>
                        <p class="text-[10px] text-slate-500 italic">Mode Kreator</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Body -->
        <div class="flex-1 overflow-y-auto p-4 md:p-8 pb-24 lg:pb-8 custom-scrollbar">
            
            <?php if (session()->getFlashdata('success')): ?>
                <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl flex items-center gap-2">
                    <span class="material-symbols-outlined">check_circle</span>
                    <span class="text-sm font-medium"><?= session()->getFlashdata('success') ?></span>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="mb-4 p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl flex items-center gap-2">
                    <span class="material-symbols-outlined">error</span>
                    <span class="text-sm font-medium"><?= session()->getFlashdata('error') ?></span>
                </div>
            <?php endif; ?>

            <div class="mb-6 md:mb-10">
                <h1 class="text-xl md:text-2xl font-bold text-slate-900">Dashboard Keuangan</h1>
                <p class="text-slate-500 text-xs md:text-sm mt-1">Kelola saldo Cooling Credits dan tarik penghasilanmu.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-8 mb-6 md:mb-10">
                <!-- Balance Card -->
                <div class="lg:col-span-1">
                    <div class="bg-slate-900 rounded-[32px] p-6 md:p-8 text-white relative overflow-hidden shadow-xl shadow-slate-200">
                        <div class="relative z-10">
                            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-2">Saldo Saat Ini</p>
                            <div class="flex items-baseline gap-2 mb-8">
                                <span class="text-4xl font-black"><?= number_format($balance) ?></span>
                                <span class="text-indigo-400 font-bold text-sm uppercase">CC</span>
                            </div>
                            
                            <p class="text-slate-500 text-[10px] mb-6 italic">Setara dengan Rp <?= number_format($balance * 10) ?></p>

                            <button class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold text-sm transition-all flex items-center justify-center gap-2" onclick="openWithdrawModal()">
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
                <h3 class="font-bold text-slate-900 text-lg">Riwayat Transaksi <span class="text-slate-400 font-normal text-sm">(5 terakhir)</span></h3>
                <a href="<?= base_url('creator/monetization/history') ?>" class="flex items-center gap-1 text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors">
                    <span>Lihat Semua</span>
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
            
            <div class="bg-white rounded-[32px] shadow-sm border border-slate-100 overflow-hidden mb-6 md:mb-10">
                <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[480px]">
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
                                    <?php
                                        $desc    = $row['description'] ?? '';
                                        $cat     = strtolower($row['category'] ?? '');
                                        $isIn    = $row['type'] === 'in';

                                        // Pilih ikon berdasarkan kategori
                                        if ($cat === 'download') {
                                            $icon = 'download';
                                        } elseif ($cat === 'unlock') {
                                            $icon = $isIn ? 'lock_open' : 'lock';
                                        } else {
                                            $icon = $isIn ? 'arrow_circle_down' : 'arrow_circle_up';
                                        }

                                        // Warna ikon
                                        $iconColor = $isIn ? 'text-emerald-500' : 'text-rose-400';

                                        // Parse deskripsi untuk tampilan yang lebih kaya
                                        // Pola MASUK: "Karya diunduh oleh @user: Title"
                                        //              "Karya dibuka oleh @user: Title"
                                        //              "Bab dibuka oleh @user: Title (Karya: WorkTitle)"
                                        // Pola KELUAR baru: "Download karya @kreator: Title"
                                        //                    "Buka karya @kreator: Title"
                                        //                    "Buka bab @kreator: Title (Karya: WorkTitle)"
                                        $actionLabel = '';
                                        $userPart    = '';
                                        $titlePart   = '';

                                        if ($cat === 'topup' || $cat === 'withdraw') {
                                            $actionLabel = $cat === 'topup' ? 'Top Up' : 'Penarikan';
                                            $titlePart   = $desc;
                                        } elseif ($isIn) {
                                            // Coba extract pola "... oleh @user: title"
                                            if (preg_match('/^(Karya diunduh oleh|Karya dibuka oleh|Bab dibuka oleh)\s+(@\S+|user_id:\d+):\s*(.+)$/u', $desc, $m)) {
                                                $actionLabel = $m[1];
                                                $userPart    = $m[2];
                                                $titlePart   = $m[3];
                                            } else {
                                                // Fallback untuk data lama tanpa username
                                                if (str_starts_with($desc, 'Karya diunduh')) {
                                                    $actionLabel = 'Karya diunduh';
                                                } elseif (str_starts_with($desc, 'Bab dibuka')) {
                                                    $actionLabel = 'Bab dibuka';
                                                } else {
                                                    $actionLabel = 'Karya dibuka';
                                                }
                                                $titlePart = preg_replace('/^(Karya diunduh|Karya dibuka|Bab dibuka):\s*/u', '', $desc);
                                            }
                                        } else {
                                            // KELUAR baru: "Download karya @kreator: Title" | "Buka karya @kreator: Title" | "Buka bab @kreator: Title"
                                            if (preg_match('/^(Download karya|Buka karya|Buka bab)\s+(@\S+|user_id:\d+):\s*(.+)$/u', $desc, $m)) {
                                                $actionLabel = $m[1];
                                                $userPart    = '';
                                                $titlePart   = $m[3];
                                            } elseif (preg_match('/^(.+?):\s*(.+)$/u', $desc, $m)) {
                                                // Fallback data lama: "Download karya: Title" | "Membuka karya: Title"
                                                $actionLabel = $m[1];
                                                $titlePart   = $m[2];
                                            } else {
                                                $actionLabel = ucfirst($cat);
                                                $titlePart   = $desc;
                                            }
                                        }
                                    ?>
                                    <div class="flex items-start gap-2.5">
                                        <div class="w-7 h-7 rounded-xl <?= $isIn ? 'bg-emerald-50' : 'bg-rose-50' ?> flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <span class="material-symbols-outlined text-[15px] <?= $iconColor ?>"><?= $icon ?></span>
                                        </div>
                                        <div class="min-w-0">
                                            <?php if ($userPart): ?>
                                                <p class="text-xs font-bold text-slate-700 leading-snug">
                                                    <?= esc($actionLabel) ?>
                                                    <span class="<?= $isIn ? 'text-indigo-600' : 'text-rose-500' ?> font-extrabold"><?= esc($userPart) ?></span>
                                                </p>
                                                <p class="text-[11px] text-slate-900 font-semibold mt-0.5 line-clamp-2"><?= esc($titlePart) ?></p>
                                            <?php else: ?>
                                                <p class="text-xs font-bold text-slate-500 leading-snug"><?= esc($actionLabel) ?></p>
                                                <p class="text-[11px] text-slate-900 font-semibold mt-0.5 line-clamp-2"><?= esc($titlePart) ?></p>
                                            <?php endif; ?>
                                            <p class="text-[9px] text-slate-400 uppercase tracking-wider mt-1 font-bold"><?= esc(ucfirst($cat)) ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <p class="text-sm font-black <?= $isIn ? 'text-emerald-600' : 'text-slate-900' ?>">
                                        <?= $isIn ? '+' : '-' ?> <?= number_format($row['amount']) ?> 
                                        <span class="text-[10px] text-slate-400">CC</span>
                                    </p>
                                    <p class="text-[10px] text-slate-400">Rp <?= number_format($row['amount'] * 10) ?></p>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <?php if ($isIn): ?>
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
            </div>

            <!-- Monetization Tips -->
            <div class="bg-indigo-50 border border-indigo-100 rounded-[32px] p-5 md:p-8 flex flex-col md:flex-row items-start md:items-center gap-5 md:gap-8">
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
    <!-- Withdraw Modal -->
    <div id="withdrawModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl shadow-xl w-full max-w-md overflow-hidden transform transition-all scale-95 opacity-0" id="withdrawModalContent">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h3 class="font-bold text-slate-900 text-lg">Tarik Saldo</h3>
                <button type="button" onclick="closeWithdrawModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form action="<?= base_url('creator/monetization/withdraw') ?>" method="POST" class="p-6">
                <p class="text-sm text-slate-500 mb-6">Masukkan jumlah saldo (CC) yang ingin ditarik. Transaksi ini adalah simulasi (dummy).</p>
                
                <div class="mb-6">
                    <label for="amount" class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wide">Jumlah Penarikan (CC)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">CC</span>
                        <input type="number" id="amount" name="amount" min="1" max="<?= $balance ?>" required
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 pl-12 pr-4 text-slate-900 font-bold focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                               placeholder="0">
                    </div>
                    <p class="text-[10px] text-slate-500 mt-2 font-medium">Saldo maksimal yang dapat ditarik: <span class="font-bold text-slate-700"><?= number_format($balance) ?> CC</span></p>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeWithdrawModal()" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold text-sm transition-colors">Batal</button>
                    <button type="submit" class="flex-1 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-sm transition-colors">Proses Tarik</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openWithdrawModal() {
            const modal = document.getElementById('withdrawModal');
            const content = document.getElementById('withdrawModalContent');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeWithdrawModal() {
            const modal = document.getElementById('withdrawModal');
            const content = document.getElementById('withdrawModalContent');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 200);
        }
    </script>
</body>
</html>
