<?php /** CART PAGE — NusaShare **/ ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Unduhan - NusaShare</title>
    <meta name="description" content="Keranjang unduhan karya NusaShare Anda. Checkout dan unduh karya favorit sebagai ZIP atau PDF.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">
    <link rel="icon" href="<?= base_url('assets/icon/logonus.png') ?>" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #F1F5F9; }

        .sidebar-link.active { background-color: #EEF2FF; color: #4F46E5; border-right: 4px solid #4F46E5; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #E2E8F0; border-radius: 10px; }

        /* Card hover */
        .cart-item-card { transition: all .3s cubic-bezier(.4,0,.2,1); }
        .cart-item-card:hover { box-shadow: 0 8px 24px -8px rgba(0,0,0,.12); }

        /* Removing animation */
        .removing { animation: removeItem .4s ease forwards; }
        @keyframes removeItem {
            to { opacity: 0; transform: translateX(40px) scale(.95); max-height: 0; margin: 0; padding: 0; overflow: hidden; }
        }

        /* Gradient CTA */
        .btn-checkout {
            background: linear-gradient(135deg, #4F46E5, #7C3AED);
            transition: all .3s ease;
        }
        .btn-checkout:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(79,70,229,.35);
        }
        .btn-checkout:disabled {
            background: #CBD5E1;
            cursor: not-allowed;
        }

        /* Pulse animation for summary card */
        @keyframes pulseGlow {
            0%,100% { box-shadow: 0 0 0 0 rgba(79,70,229,.15); }
            50%      { box-shadow: 0 0 0 12px rgba(79,70,229,0); }
        }
        .summary-card { animation: pulseGlow 3s ease infinite; }

        /* Success banner */
        @keyframes slideDown { from { transform:translateY(-20px); opacity:0; } to { transform:translateY(0); opacity:1; } }
        .success-banner { animation: slideDown .5s ease forwards; }

        /* Format badge */
        .badge-zip { background:#EEF2FF; color:#4F46E5; }
        .badge-pdf { background:#FEF3C7; color:#92400E; }
        .badge-free { background:#D1FAE5; color:#065F46; }
        .badge-paid { background:#FEF3C7; color:#92400E; }
    </style>
</head>
<body class="bg-[#F1F5F9] flex h-screen overflow-hidden">

    <?= view('dashboard/_sidebar', [
        'activePage'     => 'cart',
        'user'           => $user,
        'creatorProfile' => $creatorProfile
    ]) ?>

    <!-- Main Wrapper -->
    <div class="flex-1 flex flex-col h-full overflow-hidden">

        <!-- Topbar -->
        <nav class="bg-white border-b border-slate-200 px-8 py-4 flex items-center justify-between sticky top-0 z-40">
            <div class="flex items-center gap-4">
                <a href="<?= base_url('me/bookmarks') ?>" class="flex items-center gap-2 text-slate-500 hover:text-indigo-600 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                    <span class="text-sm font-medium hidden sm:block">Wishlist</span>
                </a>
                <div class="w-px h-5 bg-slate-200"></div>
                <h2 class="text-lg font-bold text-slate-900">Keranjang Unduhan</h2>
                <?php if (!empty($cartItems)): ?>
                    <span class="bg-indigo-100 text-indigo-700 text-xs font-black px-2.5 py-1 rounded-full"><?= count($cartItems) ?> item</span>
                <?php endif; ?>
            </div>

            <div class="flex items-center gap-4">
                <!-- Notifications -->
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
                    $creditModelNav = new \App\Models\CreditModel();
                    $userCreditNav  = $creditModelNav->find(session()->get('userId'));
                    $balanceNav     = $userCreditNav ? (int)$userCreditNav['balance'] : 0;
                ?>
                <a href="<?= base_url('topup') ?>" class="bg-indigo-50 px-3 py-1.5 rounded-full flex items-center gap-2 border border-indigo-100 hover:bg-indigo-100 transition-colors">
                    <span class="material-symbols-outlined text-indigo-600 text-lg">account_balance_wallet</span>
                    <span class="text-xs font-bold text-indigo-900" id="live-balance"><?= number_format($balanceNav) ?> CC</span>
                </a>
                <div class="flex items-center gap-3 border-l pl-4 border-slate-100">
                    <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-[#4F46E5] font-bold text-sm">
                        <?= strtoupper(substr($username, 0, 1)) ?>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto">
            <div class="max-w-7xl mx-auto px-6 py-10">

                <?php
                    // Flash messages
                    $successMsg = session()->getFlashdata('success');
                    $errorMsg   = session()->getFlashdata('error');
                    $downloadReady = $downloadReady ?? [];
                    $isCheckoutSuccess = isset($_GET['checkout']) && $_GET['checkout'] === 'success';
                ?>

                <!-- Success Banner -->
                <?php if ($successMsg || $isCheckoutSuccess): ?>
                <div class="success-banner mb-6 bg-emerald-50 border border-emerald-200 rounded-2xl p-5 flex items-start gap-4">
                    <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-emerald-600" style="font-variation-settings:'FILL' 1">check_circle</span>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-emerald-900 mb-1">Pembayaran Berhasil!</h4>
                        <p class="text-emerald-700 text-sm"><?= $successMsg ?: 'Silakan klik tombol Unduh di bawah untuk mengunduh karya Anda.' ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Error Banner -->
                <?php if ($errorMsg): ?>
                <div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-5 flex items-start gap-4">
                    <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-red-600">error</span>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-red-900 mb-1">Terjadi Kesalahan</h4>
                        <p class="text-red-700 text-sm"><?= $errorMsg ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($cartItems)): ?>
                <!-- Layout 2 kolom: item list + summary -->
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 items-start">

                    <!-- ─── Kolom Kiri: Item List ─── -->
                    <div class="xl:col-span-2 space-y-4" id="cart-list">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-base font-bold text-slate-700">Item di Keranjang</h3>
                            <span class="text-xs text-slate-400">Klik &times; untuk hapus item</span>
                        </div>

                        <?php foreach ($cartItems as $item): ?>
                        <?php
                            $t          = $item['content_type'];
                            $dlFormat   = ($t === 'image') ? 'ZIP' : 'PDF';
                            $dlIcon     = ($t === 'image') ? 'photo_library' : 'picture_as_pdf';
                            $typeLabel  = match(true) {
                                $t === 'novel'       => 'Novel',
                                $t === 'light_novel' => 'Light Novel',
                                $t === 'comic'       => 'Comic',
                                $t === 'text'        => 'Teks',
                                $t === 'image'       => 'Galeri Gambar',
                                default              => ucfirst($t),
                            };
                            $coverUrl   = base_url('image/cover/' . $item['work_id']);
                            $isInDownloadReady = in_array($item['work_id'], $downloadReady) || $isCheckoutSuccess;
                        ?>
                        <div class="cart-item-card bg-white rounded-2xl border border-slate-100 overflow-hidden"
                             id="cart-item-<?= $item['work_id'] ?>">
                            <div class="flex items-center gap-4 p-4">
                                <!-- Cover thumbnail -->
                                <div class="w-20 h-20 rounded-xl overflow-hidden bg-slate-100 flex-shrink-0 border border-slate-100">
                                    <img src="<?= $coverUrl ?>" alt="<?= htmlspecialchars($item['title']) ?>"
                                         class="w-full h-full object-cover"
                                         onerror="this.src='<?= base_url('assets/img/default-cover.jpg') ?>'"
                                         loading="lazy">
                                </div>

                                <!-- Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                                        <span class="text-[10px] font-bold uppercase tracking-wide px-2 py-0.5 rounded-md <?= $t === 'image' ? 'badge-zip' : 'text-amber-800 bg-amber-50' ?>">
                                            <?= $typeLabel ?>
                                        </span>
                                        <span class="flex items-center gap-1 text-[10px] font-bold uppercase tracking-wide px-2 py-0.5 rounded-md <?= $t === 'image' ? 'badge-zip' : 'badge-pdf' ?>">
                                            <span class="material-symbols-outlined text-[11px]"><?= $dlIcon ?></span>
                                            <?= $dlFormat ?>
                                        </span>
                                        <?php if ($item['is_paid']): ?>
                                            <span class="text-[10px] font-black px-2 py-0.5 rounded-md badge-paid"><?= number_format($item['price']) ?> CC</span>
                                        <?php else: ?>
                                            <span class="text-[10px] font-black px-2 py-0.5 rounded-md badge-free">GRATIS</span>
                                        <?php endif; ?>
                                    </div>
                                    <h4 class="font-bold text-slate-900 truncate text-sm mb-0.5"><?= htmlspecialchars($item['title']) ?></h4>
                                    <p class="text-xs text-slate-400">oleh <?= htmlspecialchars($item['creator_name']) ?></p>
                                </div>

                                <!-- Actions -->
                                <div class="flex flex-col items-end gap-2 flex-shrink-0">
                                    <?php if ($isInDownloadReady): ?>
                                        <a href="<?= base_url('cart/download/' . $item['work_id']) ?>"
                                           class="flex items-center gap-1.5 px-4 py-2 bg-emerald-600 text-white rounded-xl text-xs font-bold hover:bg-emerald-700 transition-all shadow-sm">
                                            <span class="material-symbols-outlined text-[14px]">download</span>
                                            Unduh <?= $dlFormat ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-sm font-bold text-slate-900">
                                            <?= $item['is_paid'] ? number_format($item['price']) . ' CC' : 'Gratis' ?>
                                        </span>
                                    <?php endif; ?>
                                    <button onclick="removeFromCart(<?= $item['work_id'] ?>)"
                                            class="flex items-center gap-1 text-slate-400 hover:text-red-500 transition-colors text-xs font-medium">
                                        <span class="material-symbols-outlined text-[14px]">delete</span>
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- ─── Kolom Kanan: Order Summary ─── -->
                    <div class="xl:col-span-1">
                        <div class="summary-card bg-white rounded-2xl border border-slate-100 overflow-hidden sticky top-24">
                            <!-- Header -->
                            <div class="bg-gradient-to-r from-indigo-600 to-violet-600 px-6 py-5">
                                <h3 class="text-white font-black text-lg">Ringkasan Pesanan</h3>
                                <p class="text-indigo-200 text-xs mt-0.5"><?= count($cartItems) ?> item &bull; <?= count(array_filter($cartItems, fn($i) => $i['is_paid'])) ?> berbayar</p>
                            </div>

                            <div class="p-6 space-y-4">
                                <!-- Item list summary -->
                                <div class="space-y-2 max-h-52 overflow-y-auto custom-scrollbar pr-1">
                                    <?php foreach ($cartItems as $item): ?>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-slate-600 truncate max-w-[160px]"><?= htmlspecialchars($item['title']) ?></span>
                                        <span class="font-bold text-slate-900 flex-shrink-0 ml-2">
                                            <?= $item['is_paid'] ? number_format($item['price']) . ' CC' : '<span class="text-emerald-600 text-xs font-black">GRATIS</span>' ?>
                                        </span>
                                    </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="border-t border-slate-100 pt-4">
                                    <!-- Total -->
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-slate-500 text-sm">Total Berbayar</span>
                                        <span class="font-black text-slate-900 text-lg" id="summary-total"><?= number_format($totalPrice) ?> CC</span>
                                    </div>

                                    <!-- Saldo -->
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-slate-500 text-sm">Saldo CC Anda</span>
                                        <span class="font-bold <?= $balance >= $totalPrice ? 'text-emerald-600' : 'text-red-500' ?>" id="summary-balance">
                                            <?= number_format($balance) ?> CC
                                        </span>
                                    </div>

                                    <?php if ($shortfall > 0): ?>
                                    <!-- Kekurangan -->
                                    <div class="mt-3 bg-red-50 border border-red-100 rounded-xl p-3 flex items-start gap-2">
                                        <span class="material-symbols-outlined text-red-500 text-[18px] flex-shrink-0">warning</span>
                                        <div>
                                            <p class="text-red-700 text-xs font-bold">Saldo kurang <?= number_format($shortfall) ?> CC</p>
                                            <p class="text-red-500 text-[11px] mt-0.5">Top up saldo CC untuk melanjutkan checkout</p>
                                        </div>
                                    </div>
                                    <?php else: ?>
                                    <div class="mt-3 bg-emerald-50 border border-emerald-100 rounded-xl p-3 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-emerald-500 text-[18px]" style="font-variation-settings:'FILL' 1">check_circle</span>
                                        <p class="text-emerald-700 text-xs font-bold">Saldo CC mencukupi</p>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <!-- CTA Buttons -->
                                <div class="space-y-3 pt-2">
                                    <?php if ($isCheckoutSuccess): ?>
                                        <!-- After checkout: show download all button -->
                                        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-center">
                                            <span class="material-symbols-outlined text-emerald-600 text-3xl" style="font-variation-settings:'FILL' 1">task_alt</span>
                                            <p class="text-emerald-800 font-bold text-sm mt-1">Pembayaran Selesai!</p>
                                            <p class="text-emerald-600 text-xs mt-0.5">Klik tombol Unduh pada setiap item</p>
                                        </div>
                                    <?php elseif ($shortfall > 0): ?>
                                        <!-- Top Up button -->
                                        <a href="<?= base_url('topup') ?>"
                                           class="w-full flex items-center justify-center gap-2 py-3.5 rounded-2xl font-bold text-sm bg-amber-500 text-white hover:bg-amber-600 transition-all shadow-lg">
                                            <span class="material-symbols-outlined">add_card</span>
                                            Top Up <?= number_format($shortfall) ?> CC
                                        </a>
                                        <form action="<?= base_url('cart/checkout') ?>" method="POST">
                                            <button type="submit" class="w-full py-3 rounded-2xl font-bold text-sm btn-checkout text-white" disabled>
                                                Saldo Tidak Cukup
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <!-- Checkout button -->
                                        <form action="<?= base_url('cart/checkout') ?>" method="POST" id="checkout-form">
                                            <button type="submit" id="checkout-btn"
                                                    class="w-full flex items-center justify-center gap-2 py-3.5 rounded-2xl font-black text-base btn-checkout text-white">
                                                <span class="material-symbols-outlined">shopping_cart_checkout</span>
                                                <?= $totalPrice > 0 ? 'Bayar & Download (' . number_format($totalPrice) . ' CC)' : 'Download Gratis' ?>
                                            </button>
                                        </form>
                                        <p class="text-center text-xs text-slate-400">
                                            Setelah checkout, file langsung tersedia untuk diunduh
                                        </p>
                                    <?php endif; ?>

                                    <!-- Continue Shopping -->
                                    <a href="<?= base_url('me/bookmarks') ?>"
                                       class="w-full flex items-center justify-center gap-2 py-2.5 rounded-2xl font-medium text-sm text-slate-600 hover:bg-slate-100 transition-all">
                                        <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                                        Kembali ke Wishlist
                                    </a>
                                </div>

                                <!-- Info box -->
                                <div class="border-t border-slate-100 pt-4">
                                    <div class="grid grid-cols-2 gap-2 text-center">
                                        <div class="bg-slate-50 rounded-xl p-3">
                                            <span class="material-symbols-outlined text-slate-400 text-xl">photo_library</span>
                                            <p class="text-[10px] font-bold text-slate-500 mt-1">Gambar → ZIP</p>
                                        </div>
                                        <div class="bg-slate-50 rounded-xl p-3">
                                            <span class="material-symbols-outlined text-slate-400 text-xl">picture_as_pdf</span>
                                            <p class="text-[10px] font-bold text-slate-500 mt-1">Novel → PDF</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div><!-- /grid -->

                <?php else: ?>
                <!-- Empty State -->
                <div class="flex flex-col items-center justify-center py-32 text-center">
                    <div class="relative mb-8">
                        <div class="w-28 h-28 bg-white rounded-3xl shadow-lg flex items-center justify-center border border-slate-100">
                            <span class="material-symbols-outlined text-6xl text-slate-300">shopping_cart</span>
                        </div>
                        <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-indigo-500 text-xl">add</span>
                        </div>
                    </div>
                    <h2 class="text-2xl font-black text-slate-900 mb-3">Keranjang Kosong</h2>
                    <p class="text-slate-500 max-w-sm mx-auto mb-8 leading-relaxed">
                        Belum ada karya di keranjang. Kunjungi Wishlist Anda dan klik <strong>+ Keranjang</strong> untuk menambahkan karya yang ingin diunduh.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="<?= base_url('me/bookmarks') ?>"
                           class="flex items-center gap-2 px-8 py-3.5 rounded-2xl font-bold text-sm bg-indigo-600 text-white hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200">
                            <span class="material-symbols-outlined">bookmark</span>
                            Buka Wishlist
                        </a>
                        <a href="<?= base_url('explore') ?>"
                           class="flex items-center gap-2 px-8 py-3.5 rounded-2xl font-bold text-sm bg-white text-slate-700 hover:bg-slate-50 transition-all border border-slate-200 shadow-sm">
                            <span class="material-symbols-outlined">explore</span>
                            Jelajahi Karya
                        </a>
                    </div>
                </div>
                <?php endif; ?>

            </div><!-- /max-w-7xl -->
        </div><!-- /overflow-y-auto -->
    </div><!-- /flex-1 -->

    <!-- Toast -->
    <div id="cart-toast" class="fixed top-6 right-6 z-[9999] flex items-center gap-3 bg-slate-900 text-white px-5 py-3.5 rounded-2xl shadow-2xl text-sm font-semibold opacity-0 pointer-events-none -translate-y-2 max-w-xs transition-all duration-300">
        <span class="material-symbols-outlined text-lg" id="cart-toast-icon">delete</span>
        <span id="cart-toast-msg">Item dihapus</span>
    </div>

    <script>
        window.nusaAppData = { baseUrl: '<?= base_url() ?>/' };

        const BASE_URL = '<?= rtrim(base_url(), '/') ?>';

        /* ── Toast ── */
        function showToast(msg, icon, bg) {
            const t = document.getElementById('cart-toast');
            document.getElementById('cart-toast-icon').textContent = icon || 'info';
            document.getElementById('cart-toast-msg').textContent  = msg;
            t.style.background = bg || '#0F172A';
            t.classList.remove('opacity-0','-translate-y-2','pointer-events-none');
            t.classList.add('opacity-100','translate-y-0');
            setTimeout(() => {
                t.classList.add('opacity-0','-translate-y-2','pointer-events-none');
                t.classList.remove('opacity-100','translate-y-0');
            }, 3000);
        }

        /* ── Remove from cart (AJAX) ── */
        async function removeFromCart(workId) {
            const card = document.getElementById('cart-item-' + workId);
            if (!card) return;

            try {
                const res  = await fetch(BASE_URL + '/cart/remove/' + workId, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();

                if (data.status === 'success') {
                    card.classList.add('removing');
                    setTimeout(() => {
                        card.remove();
                        // Cek apakah cart sudah kosong
                        const remaining = document.querySelectorAll('#cart-list .cart-item-card');
                        if (remaining.length === 0) {
                            location.reload();
                        }
                    }, 400);
                    showToast('Item dihapus dari keranjang.', 'delete', '#475569');
                } else {
                    showToast(data.message || 'Gagal menghapus.', 'error', '#EF4444');
                }
            } catch(e) {
                showToast('Gagal terhubung.', 'wifi_off', '#EF4444');
            }
        }

        /* ── Checkout button loading state ── */
        const checkoutForm = document.getElementById('checkout-form');
        const checkoutBtn  = document.getElementById('checkout-btn');
        if (checkoutForm && checkoutBtn) {
            checkoutForm.addEventListener('submit', () => {
                checkoutBtn.disabled = true;
                checkoutBtn.innerHTML = `<span class="material-symbols-outlined animate-spin">progress_activity</span> Memproses...`;
            });
        }
    </script>
    <script src="<?= base_url('assets/js/notifications.js') ?>"></script>
</body>
</html>
