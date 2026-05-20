<?php /** INTEGRATED SIDEBAR & TOPUP VIEW **/ ?>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= $title ?? 'Top Up' ?> - NusaShare</title>
    
    <!-- Fonts: Inter -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Material Icons -->
    <link rel="icon" href="<?= base_url('assets/icon/logonus.png') ?>" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">

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
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #E2E8F0; border-radius: 10px; }

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

        .pkg-card {
            background: var(--lp-surface);
            border: 1px solid #E2E8F0;
            border-radius: 20px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .pkg-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: #4F46E5;
        }

        .pkg-badge {
            position: absolute;
            top: 12px;
            right: -32px;
            background: #4F46E5;
            color: white;
            padding: 4px 40px;
            transform: rotate(45deg);
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    </style>
</head>
<body class="bg-[#F8FAFC] flex h-screen overflow-hidden">

    <?= view('dashboard/_sidebar', [
        'activePage'     => 'topup',
        'user'           => $user,
        'creatorProfile' => $creatorProfile
    ]) ?>

    <!-- Main Wrapper -->
    <div class="flex-1 flex flex-col h-full overflow-hidden">

        <!-- Top Bar -->
        <nav class="bg-white border-b border-slate-200 px-8 py-4 flex items-center justify-between sticky top-0 z-40">
            <div class="w-full flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <h2 class="text-lg font-bold text-slate-900 hidden lg:block">Top Up CC</h2>
                    <div class="flex items-center gap-4 lg:hidden">
                        <img src="<?= base_url('assets/icon/logonus.png') ?>" alt="Logo" class="w-8 h-8">
                    </div>
                </div>

                <div class="flex items-center gap-6">
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
                    <a href="<?= base_url('topup') ?>" class="bg-indigo-50 px-3 py-1.5 rounded-full flex items-center gap-2 border border-indigo-100 hover:bg-indigo-100 transition-colors">
                        <span class="material-symbols-outlined text-indigo-600 text-lg">account_balance_wallet</span>
                        <span class="text-xs font-bold text-indigo-900"><?= number_format($balance) ?> CC</span>
                    </a>
                    
                    <div class="flex items-center gap-3 border-l pl-6 border-slate-100">
                        <div class="text-right">
                            <p class="text-xs font-bold text-slate-900"><?= $username ?></p>
                            <p class="text-[10px] text-slate-500"><?= $user['starsoul_status'] ?? 'Anggota NusaShare' ?></p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-[#4F46E5] font-bold">
                            <?= strtoupper(substr($username, 0, 1)) ?>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Content Container -->
        <div class="flex-1 overflow-y-auto custom-scrollbar">
            
            <header class="pt-12 pb-8 px-8">
                <div class="max-w-6xl mx-auto">
                    <h1 class="text-3xl font-bold text-[#0F172A] mb-2 tracking-tight">Isi Ulang Cooling Credit</h1>
                    <p class="text-[#64748B]">Buka kunci karya eksklusif dan dukung kreator favorit Anda dengan Cooling Credit (CC).</p>
                </div>
            </header>

            <main class="max-w-6xl mx-auto px-8 py-8 mb-20">
                
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="mb-8 p-4 bg-indigo-50 border border-indigo-200 text-indigo-700 rounded-2xl flex items-center gap-3">
                        <span class="material-symbols-outlined">info</span>
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <!-- Pricing Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach ($packages as $pkg): ?>
                        <div class="pkg-card p-8 flex flex-col items-center text-center">
                            <?php if ($pkg['cc'] >= 10000): ?>
                                <div class="pkg-badge">HOT</div>
                            <?php endif; ?>

                            <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center text-[#4F46E5] mb-6">
                                <span class="material-symbols-outlined text-4xl">paid</span>
                            </div>
                            
                            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-1"><?= $pkg['label'] ?></h3>
                            <div class="flex items-center gap-2 mb-4">
                                <span class="text-4xl font-extrabold text-slate-900"><?= number_format($pkg['cc']) ?></span>
                                <span class="text-xl font-bold text-indigo-600">CC</span>
                            </div>

                            <div class="w-full h-px bg-slate-100 mb-6"></div>

                            <div class="mb-10 text-slate-700">
                                <p class="text-sm font-medium opacity-60 mb-1">Total Pembayaran</p>
                                <p class="text-2xl font-bold">Rp <?= number_format($pkg['price'], 0, ',', '.') ?></p>
                            </div>

                            <form action="<?= base_url('topup/checkout') ?>" method="POST" class="w-full">
                                <input type="hidden" name="package_id" value="<?= $pkg['id'] ?>">
                                <button type="submit" class="w-full btn-primary py-4 rounded-xl font-bold text-sm shadow-lg shadow-indigo-500/20 flex items-center justify-center gap-2">
                                    <span class="material-symbols-outlined text-lg">shopping_bag</span>
                                    Beli Sekarang
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Info Section -->
                <div class="mt-16 bg-white p-8 rounded-3xl border border-slate-200">
                    <h3 class="font-bold text-lg mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#4F46E5]">help</span>
                        Informasi Top Up
                    </h3>
                    <div class="grid md:grid-cols-3 gap-8">
                        <div>
                            <p class="font-bold text-slate-900 text-sm mb-2">Konversi CC</p>
                            <p class="text-xs text-slate-500 leading-relaxed">1 Cooling Credit setara dengan Rp 10. Saldo CC dapat digunakan untuk membuka konten berbayar di NusaShare.</p>
                        </div>
                        <div>
                            <p class="font-bold text-slate-900 text-sm mb-2">Metode Pembayaran</p>
                            <p class="text-xs text-slate-500 leading-relaxed">Kami mendukung berbagai metode pembayaran mulai dari QRIS, E-Wallet (OVO, GoPay, Dana), hingga Transfer Bank.</p>
                        </div>
                        <div>
                            <p class="font-bold text-slate-900 text-sm mb-2">Bantuan</p>
                            <p class="text-xs text-slate-500 leading-relaxed">Jika saldo belum masuk dalam 1x24 jam, hubungi dukungan pelanggan kami dengan melampirkan bukti transaksi.</p>
                        </div>
                    </div>
                </div>

            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-slate-200 pt-16 pb-8">
                <div class="max-w-6xl mx-auto px-8 text-center md:text-left">
                    <p class="text-xs text-[#94A3B8]">©<?php echo date("Y"); ?> NusaShare. Seluruh transaksi diproses dengan aman.</p>
                </div>
            </footer>

        </div>
    </div>
    <script>
        window.nusaAppData = { baseUrl: '<?= base_url() ?>/' };
    </script>
    <script src="<?= base_url('assets/js/notifications.js') ?>"></script>
</body>
</html>
