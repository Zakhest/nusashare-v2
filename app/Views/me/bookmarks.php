<?php /** INTEGRATED SIDEBAR & BOOKMARKS / WISHLIST VIEW **/ ?>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Wishlist Saya - NusaShare</title>
    
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

        @keyframes fadeInUp {
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-in {
            animation: 0.5s ease forwards running fadeInUp;
        }

        /* Cart CTA Bar */
        #cart-cta-bar {
            transition: transform 0.4s cubic-bezier(0.4,0,0.2,1);
        }
        /* Toast */
        #cart-toast {
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="bg-[#F8FAFC] flex h-screen overflow-hidden">

    <?= view('dashboard/_sidebar', [
        'activePage'     => 'bookmarks',
        'user'           => $user,
        'creatorProfile' => $creatorProfile
    ]) ?>

    <!-- Main Wrapper -->
    <div class="flex-1 flex flex-col h-full overflow-hidden">

        <!-- Top Bar -->
        <nav class="bg-white border-b border-slate-200 px-8 py-4 flex items-center justify-between sticky top-0 z-40">
            <div class="w-full flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <h2 class="text-lg font-bold text-slate-900 hidden lg:block">Wishlist Saya</h2>
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
                    <!-- Cart Icon Link -->
                    <a href="<?= base_url('me/cart') ?>" class="relative text-slate-500 hover:text-indigo-600 transition-colors hidden md:flex">
                        <span class="material-symbols-outlined">shopping_cart</span>
                        <?php
                            $cartWorkIds = $cartWorkIds ?? [];
                            $cartCount   = count($cartWorkIds);
                        ?>
                        <?php if ($cartCount > 0): ?>
                            <span class="absolute -top-1 -right-1 w-4 h-4 bg-indigo-600 text-white text-[9px] font-black rounded-full flex items-center justify-center"><?= $cartCount ?></span>
                        <?php endif; ?>
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
        <div class="flex-1 overflow-y-auto">
            
            <header class="pt-12 pb-8 px-8">
                <div class="max-w-7xl mx-auto flex items-end justify-between gap-4 flex-wrap">
                    <div>
                        <h1 class="text-3xl font-bold text-[#0F172A] mb-2">Wishlist Karya Anda</h1>
                        <p class="text-[#64748B]">Semua karya yang telah Anda simpan ke Wishlist. Tambahkan ke Keranjang untuk mengunduh.</p>
                    </div>
                    <?php if ($cartCount > 0): ?>
                    <a href="<?= base_url('me/cart') ?>" class="flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white rounded-xl font-bold text-sm hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200">
                        <span class="material-symbols-outlined text-lg">shopping_cart</span>
                        Lihat Keranjang (<?= $cartCount ?>)
                    </a>
                    <?php endif; ?>
                </div>
            </header>

            <main class="max-w-7xl mx-auto px-8 py-8 min-h-screen pb-36">
                <div id="art-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 md:gap-8">
                    <?php if (!empty($works)): ?>
                        <?php foreach ($works as $index => $work): ?>
                            <?php 
                                $delay     = $index * 50; 
                                $coverUrl  = base_url('image/cover/' . $work['id']);
                                if (empty($work['cover_url'])) {
                                    $coverUrl = base_url('assets/img/default-cover.jpg');
                                }
                                $t = $work['content_type'];
                                $typeLabel = match(true) {
                                    $t === 'novel'       => 'Novel',
                                    $t === 'light_novel' => 'Light Novel',
                                    $t === 'comic'       => 'Comic',
                                    $t === 'text'        => 'Teks',
                                    $t === 'image'       => 'Galeri',
                                    default              => ucfirst($t),
                                };
                                $dlFormat = ($t === 'image') ? 'ZIP' : 'PDF';
                                $dlIcon   = ($t === 'image') ? 'photo_library' : 'picture_as_pdf';
                                $inCart   = in_array($work['id'], $cartWorkIds);
                            ?>
                            <div class="art-card group opacity-0 translate-y-4 animate-in"
                                 style="animation-delay: <?= $delay ?>ms;"
                                 data-work-id="<?= $work['id'] ?>">
                                <div class="relative aspect-[4/3] bg-slate-100 overflow-hidden border-b border-slate-100">
                                    <img src="<?= $coverUrl ?>" alt="<?= htmlspecialchars($work['title']) ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy">

                                    <!-- Top-left badges -->
                                    <div class="absolute top-3 left-3 flex flex-col gap-1.5">
                                        <span class="bg-white/90 backdrop-blur px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider text-[#4F46E5] shadow-sm">
                                            <?= $typeLabel ?>
                                        </span>
                                        <span class="flex items-center gap-1 bg-slate-900/80 backdrop-blur px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider text-white shadow-sm">
                                            <span class="material-symbols-outlined text-[11px]"><?= $dlIcon ?></span>
                                            <?= $dlFormat ?>
                                        </span>
                                    </div>

                                    <!-- Price badge bottom-left -->
                                    <?php if ($work['is_paid']): ?>
                                        <div class="absolute bottom-3 left-3 bg-amber-500 text-white px-2 py-1 rounded-md text-[10px] font-black shadow">
                                            <?= number_format($work['price']) ?> CC
                                        </div>
                                    <?php else: ?>
                                        <div class="absolute bottom-3 left-3 bg-emerald-500 text-white px-2 py-1 rounded-md text-[10px] font-black shadow">
                                            GRATIS
                                        </div>
                                    <?php endif; ?>

                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors duration-300"></div>

                                    <!-- Hapus dari Wishlist -->
                                    <div class="absolute top-3 right-3 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <form action="<?= base_url('bookmark/' . $work['id'] . '/remove') ?>" method="POST" onsubmit="return confirm('Hapus dari Wishlist?')">
                                            <button type="submit" class="w-8 h-8 rounded-full bg-white/90 backdrop-blur flex items-center justify-center text-red-500 shadow-sm hover:bg-red-50 transition-colors" title="Hapus dari Wishlist">
                                                <span class="material-symbols-outlined text-[18px]">bookmark_remove</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div class="p-4">
                                    <h3 class="font-bold text-[#0F172A] text-base leading-tight mb-2 truncate group-hover:text-[#4F46E5] transition-colors"><?= htmlspecialchars($work['title']) ?></h3>
                                    <div class="flex items-center gap-2 mb-3">
                                        <div class="w-5 h-5 rounded-full bg-slate-200 flex items-center justify-center text-[9px] font-bold text-slate-500">
                                            <?= strtoupper(substr($work['creator_name'] ?? 'U', 0, 1)) ?>
                                        </div>
                                        <span class="text-xs text-[#475569] font-medium truncate max-w-[120px]"><?= htmlspecialchars($work['creator_name'] ?? 'Unknown') ?></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <!-- Tombol Keranjang AJAX -->
                                        <button
                                            id="cart-btn-<?= $work['id'] ?>"
                                            class="cart-btn flex-1 flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl text-[11px] font-bold transition-all <?= $inCart ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-indigo-600 text-white hover:bg-indigo-700' ?>"
                                            data-work-id="<?= $work['id'] ?>"
                                            data-in-cart="<?= $inCart ? '1' : '0' ?>"
                                            onclick="toggleCart(this, <?= $work['id'] ?>)">
                                            <span class="material-symbols-outlined text-[14px] cart-icon"><?= $inCart ? 'shopping_cart_checkout' : 'add_shopping_cart' ?></span>
                                            <span class="cart-label"><?= $inCart ? 'Di Keranjang' : '+ Keranjang' ?></span>
                                        </button>
                                        <a href="<?= base_url('works/' . $work['id']) ?>"
                                           class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-indigo-50 hover:text-indigo-600 transition-colors"
                                           title="Lihat Karya">
                                            <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-full py-20 text-center flex flex-col items-center justify-center">
                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mb-4 border-2 border-dashed border-slate-200">
                                <span class="material-symbols-outlined text-4xl">bookmark</span>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-2">Wishlist Anda Kosong</h3>
                            <p class="text-slate-500 max-w-sm mx-auto mb-8">Anda belum menyimpan karya apa pun ke Wishlist. Mulai jelajahi karya kreator hebat di NusaShare dan klik tombol <strong>Simpan</strong> untuk menambahkan.</p>
                            <a href="<?= base_url('explore') ?>" class="btn-primary px-8 py-3 rounded-xl font-bold text-sm shadow-lg shadow-indigo-500/20">
                                Jelajahi Sekarang
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-slate-200 pt-16 pb-8">
                <div class="max-w-7xl mx-auto px-8">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-6 mb-8">
                        <div class="flex items-center gap-2">
                            <img src="<?= base_url('assets/icon/logonus.png') ?>" alt="Logo N" class="w-8 h-8">
                            <span class="font-bold text-[#0F172A]">NusaShare</span>
                        </div>
                        <div class="flex gap-8 text-sm text-[#64748B]">
                            <a href="#" class="hover:text-[#4F46E5]">Tentang</a>
                            <a href="#" class="hover:text-[#4F46E5]">Kebijakan Privasi</a>
                            <a href="#" class="hover:text-[#4F46E5]">Bantuan</a>
                        </div>
                    </div>
                    <div class="text-center md:text-left text-xs text-[#94A3B8]">
                        ©<?php echo date("Y"); ?> NusaShare. Platform Kreator Indonesia.
                    </div>
                </div>
            </footer>

        </div><!-- /flex-1 overflow-y-auto -->
    </div><!-- /main wrapper -->

    <!-- ─── Sticky Cart CTA Bar ─────────────────────────── -->
    <?php if (!empty($works)): ?>
    <div id="cart-cta-bar" class="fixed bottom-0 left-0 right-0 z-50 lg:left-64">
        <div class="px-8 pb-6 pt-2 max-w-7xl mx-auto">
            <div class="bg-slate-900 rounded-2xl shadow-2xl shadow-slate-900/40 px-6 py-4 flex items-center gap-4">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-white text-xl">shopping_cart</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-white font-bold text-sm" id="cta-text">
                        <?php if ($cartCount > 0): ?>
                            <span class="text-indigo-400"><?= $cartCount ?> item</span> siap diunduh di Keranjang
                        <?php else: ?>
                            Tambahkan karya ke Keranjang untuk mengunduh
                        <?php endif; ?>
                    </p>
                    <p class="text-slate-400 text-xs mt-0.5">Gambar &rarr; ZIP &nbsp;&bull;&nbsp; Novel / Teks &rarr; PDF</p>
                </div>
                <a href="<?= base_url('me/cart') ?>" id="cart-cta-btn"
                   class="flex-shrink-0 flex items-center gap-2 px-6 py-3 rounded-xl font-bold text-sm transition-all
                          <?= $cartCount > 0 ? 'bg-indigo-600 text-white hover:bg-indigo-700 shadow-lg shadow-indigo-900/30' : 'bg-slate-700 text-slate-500 pointer-events-none' ?>">
                    <span class="material-symbols-outlined">shopping_cart_checkout</span>
                    <span>Checkout</span>
                    <span id="cta-count-badge" class="bg-white/20 text-white text-[10px] font-black px-2 py-0.5 rounded-full <?= $cartCount > 0 ? '' : 'hidden' ?>"><?= $cartCount ?></span>
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Toast -->
    <div id="cart-toast" class="fixed top-6 right-6 z-[9999] flex items-center gap-3 bg-slate-900 text-white px-5 py-3.5 rounded-2xl shadow-2xl text-sm font-semibold opacity-0 pointer-events-none -translate-y-2 max-w-xs">
        <span class="material-symbols-outlined text-lg" id="cart-toast-icon">shopping_cart</span>
        <span id="cart-toast-msg">Ditambahkan!</span>
    </div>

    <script>
        window.nusaAppData = { baseUrl: '<?= base_url() ?>/' };

        const BASE_URL  = '<?= rtrim(base_url(), '/') ?>';
        let   cartCount = <?= $cartCount ?>;

        /* ── Toast ── */
        function showCartToast(msg, icon, bg) {
            const t = document.getElementById('cart-toast');
            document.getElementById('cart-toast-icon').textContent = icon || 'shopping_cart';
            document.getElementById('cart-toast-msg').textContent  = msg;
            t.style.background = bg || '#0F172A';
            t.classList.remove('opacity-0', '-translate-y-2', 'pointer-events-none');
            t.classList.add('opacity-100', 'translate-y-0');
            setTimeout(() => {
                t.classList.add('opacity-0', '-translate-y-2', 'pointer-events-none');
                t.classList.remove('opacity-100', 'translate-y-0');
            }, 3000);
        }

        /* ── Update CTA Bar ── */
        function updateCtaBar(count) {
            cartCount = count;
            const ctaText  = document.getElementById('cta-text');
            const ctaBtn   = document.getElementById('cart-cta-btn');
            const ctaBadge = document.getElementById('cta-count-badge');
            // Cart icon in topbar
            const cartIconBadge = document.querySelector('.cart-topbar-badge');

            if (count > 0) {
                ctaText.innerHTML = `<span class="text-indigo-400">${count} item</span> siap diunduh di Keranjang`;
                ctaBtn.classList.remove('bg-slate-700','text-slate-500','pointer-events-none');
                ctaBtn.classList.add('bg-indigo-600','text-white','hover:bg-indigo-700','shadow-lg','shadow-indigo-900/30');
                ctaBadge.textContent = count;
                ctaBadge.classList.remove('hidden');
            } else {
                ctaText.innerHTML = 'Tambahkan karya ke Keranjang untuk mengunduh';
                ctaBtn.classList.add('bg-slate-700','text-slate-500','pointer-events-none');
                ctaBtn.classList.remove('bg-indigo-600','text-white','hover:bg-indigo-700','shadow-lg','shadow-indigo-900/30');
                ctaBadge.classList.add('hidden');
            }
        }

        /* ── Toggle Cart ── */
        async function toggleCart(btn, workId) {
            const inCart = btn.dataset.inCart === '1';
            const url    = inCart
                ? BASE_URL + '/cart/remove/' + workId
                : BASE_URL + '/cart/add/'    + workId;

            const icon  = btn.querySelector('.cart-icon');
            const label = btn.querySelector('.cart-label');

            // Optimistic UI
            if (!inCart) {
                btn.dataset.inCart = '1';
                icon.textContent   = 'shopping_cart_checkout';
                label.textContent  = 'Di Keranjang';
                btn.className = btn.className
                    .replace('bg-indigo-600','bg-emerald-50')
                    .replace('text-white','text-emerald-700')
                    .replace('hover:bg-indigo-700','');
                btn.classList.add('border','border-emerald-200');
            } else {
                btn.dataset.inCart = '0';
                icon.textContent   = 'add_shopping_cart';
                label.textContent  = '+ Keranjang';
                btn.classList.remove('bg-emerald-50','text-emerald-700','border','border-emerald-200');
                btn.classList.add('bg-indigo-600','text-white','hover:bg-indigo-700');
            }

            try {
                const res  = await fetch(url, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();

                if (data.status === 'success' || data.status === 'already') {
                    updateCtaBar(data.count ?? cartCount);
                    if (!inCart) {
                        showCartToast('Ditambahkan ke Keranjang!', 'add_shopping_cart', '#4F46E5');
                    } else {
                        showCartToast('Dihapus dari Keranjang.', 'remove_shopping_cart', '#475569');
                    }
                } else {
                    // Revert
                    btn.dataset.inCart = inCart ? '1' : '0';
                    showCartToast(data.message || 'Gagal.', 'error', '#EF4444');
                }
            } catch(e) {
                showCartToast('Gagal terhubung.', 'wifi_off', '#EF4444');
            }
        }
    </script>
    <script src="<?= base_url('assets/js/notifications.js') ?>"></script>
</body>
</html>
