<!-- Bottom Navigation for Mobile -->
<?php
    $activePage = $activePage ?? '';
    $navItems = [
        ['href' => base_url('dashboard'),    'icon' => 'dashboard',               'label' => 'Beranda',   'page' => 'dashboard'],
        ['href' => base_url('explore'),      'icon' => 'explore',                 'label' => 'Jelajah',   'page' => 'explore'],
        ['href' => base_url('me/follows'),   'icon' => 'group',                   'label' => 'Ikuti',     'page' => 'follows'],
        ['href' => base_url('me/bookmarks'), 'icon' => 'bookmark',                'label' => 'Wishlist',  'page' => 'bookmarks'],
        ['href' => base_url('me/cart'),      'icon' => 'shopping_cart',           'label' => 'Keranjang', 'page' => 'cart'],
        ['href' => base_url('me/profile'),   'icon' => 'person',                  'label' => 'Profil',    'page' => 'profile'],
        ['href' => base_url('topup'),        'icon' => 'account_balance_wallet',  'label' => 'Top Up',    'page' => 'topup'],
        ['href' => base_url('logout'),       'icon' => 'logout',                  'label' => 'Keluar',    'page' => 'logout', 'danger' => true],
    ];
?>
<nav class="lg:hidden fixed bottom-0 inset-x-0 z-50 bg-white/95 backdrop-blur-md border-t border-slate-200 flex items-stretch overflow-x-auto safe-bottom">
    <?php foreach ($navItems as $item): ?>
        <?php if ($item['page'] === 'logout'): ?>
            <div class="notification-widget flex-none w-16">
                <button type="button" data-notification-bell class="relative w-full h-full flex flex-col items-center justify-center py-2 gap-0.5 text-xs font-medium text-slate-400 active:text-indigo-600 transition-all duration-200">
                    <span class="relative">
                        <span class="material-symbols-outlined text-[22px]">notifications</span>
                        <span data-notification-badge class="absolute -top-0.5 -right-1 min-w-4 h-4 px-1 bg-red-500 text-white text-[9px] font-black rounded-full hidden items-center justify-center leading-none"></span>
                    </span>
                    <span class="text-[10px] font-semibold">Notif</span>
                </button>

                <div data-notification-dropdown class="fixed left-3 right-3 bottom-[4.75rem] max-h-[70dvh] bg-white rounded-2xl shadow-2xl border border-slate-100 hidden z-[70] transform opacity-0 scale-95 transition-all origin-bottom">
                    <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50 rounded-t-2xl">
                        <h3 class="font-bold text-slate-900">Notifikasi</h3>
                        <button type="button" data-mark-all-read class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Tandai semua dibaca</button>
                    </div>
                    <div data-notification-list class="max-h-[54dvh] overflow-y-auto custom-scrollbar">
                        <div class="p-6 text-center text-slate-400 text-sm">Memuat notifikasi...</div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php $isActive = $activePage === $item['page']; ?>
        <a href="<?= $item['href'] ?>" class="flex-none w-16 flex flex-col items-center justify-center py-2 gap-0.5 text-xs font-medium transition-all duration-200 <?= ($item['danger'] ?? false) ? 'text-red-400 active:text-red-600' : ($isActive ? 'text-indigo-600' : 'text-slate-400 active:text-indigo-600') ?>">
            <span class="material-symbols-outlined text-[22px]" style="font-variation-settings: 'FILL' <?= $isActive ? '1' : '0' ?>"><?= $item['icon'] ?></span>
            <span class="text-[10px] font-semibold"><?= $item['label'] ?></span>
        </a>
    <?php endforeach; ?>
</nav>

<!-- Sidebar for Desktop -->
<aside class="w-64 bg-white border-r border-slate-200 hidden lg:flex flex-col flex-shrink-0 h-full">
    <div class="p-6 border-b border-slate-100 flex items-center gap-3">
        <img src="<?= base_url('assets/icon/logonus.png') ?>" alt="Logo" class="w-8 h-8">
        <span class="font-bold text-xl text-slate-900 tracking-tight">NusaShare</span>
    </div>
    
    <nav class="flex-1 p-4 space-y-2 mt-4 overflow-y-auto custom-scrollbar">
        <a href="<?= base_url('dashboard') ?>" class="sidebar-link <?= ($activePage ?? '') == 'dashboard' ? 'active' : '' ?> flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all <?= ($activePage ?? '') == 'dashboard' ? '' : 'text-slate-600 hover:bg-slate-50' ?>">
            <span class="material-symbols-outlined">dashboard</span> Dashboard
        </a>
        <a href="<?= base_url('explore') ?>" class="sidebar-link <?= ($activePage ?? '') == 'explore' ? 'active' : '' ?> flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all <?= ($activePage ?? '') == 'explore' ? '' : 'text-slate-600 hover:bg-slate-50' ?>">
            <span class="material-symbols-outlined">explore</span> Explore Karya
        </a>
        <a href="<?= base_url('me/follows') ?>" class="sidebar-link <?= ($activePage ?? '') == 'follows' ? 'active' : '' ?> flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all <?= ($activePage ?? '') == 'follows' ? '' : 'text-slate-600 hover:bg-slate-50' ?>">
            <span class="material-symbols-outlined">group</span> Kreator Diikuti
        </a>
        <a href="<?= base_url('me/bookmarks') ?>" class="sidebar-link <?= ($activePage ?? '') == 'bookmarks' ? 'active' : '' ?> flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all <?= ($activePage ?? '') == 'bookmarks' ? '' : 'text-slate-600 hover:bg-slate-50' ?>">
            <span class="material-symbols-outlined" style="<?= ($activePage ?? '') == 'bookmarks' ? "font-variation-settings: 'FILL' 1" : '' ?>">bookmark</span>
            <span class="flex-1">Wishlist</span>
            <span class="text-[10px] font-black px-1.5 py-0.5 rounded-md bg-indigo-100 text-indigo-600">Koleksi</span>
        </a>
        <?php
            // Cart badge count
            $cartModelSidebar = new \App\Models\CartModel();
            $cartCountSidebar = count($cartModelSidebar->getWorkIds((string)(session()->get('userId') ?? '')));
        ?>
        <a href="<?= base_url('me/cart') ?>" class="sidebar-link <?= ($activePage ?? '') == 'cart' ? 'active' : '' ?> flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all <?= ($activePage ?? '') == 'cart' ? '' : 'text-slate-600 hover:bg-slate-50' ?>">
            <span class="material-symbols-outlined" style="<?= ($activePage ?? '') == 'cart' ? "font-variation-settings: 'FILL' 1" : '' ?>">shopping_cart</span>
            <span class="flex-1">Keranjang</span>
            <?php if ($cartCountSidebar > 0): ?>
                <span class="text-[10px] font-black px-1.5 py-0.5 rounded-full bg-indigo-600 text-white"><?= $cartCountSidebar ?></span>
            <?php endif; ?>
        </a>
        <div class="pt-4 pb-2 px-4">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Akun</span>
        </div>
        <a href="<?= base_url('me/profile') ?>" class="sidebar-link <?= ($activePage ?? '') == 'profile' ? 'active' : '' ?> flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all <?= ($activePage ?? '') == 'profile' ? '' : 'text-slate-600 hover:bg-slate-50' ?>">
            <span class="material-symbols-outlined">person</span> Profil
        </a>
        <a href="<?= base_url('topup') ?>" class="sidebar-link <?= ($activePage ?? '') == 'topup' ? 'active' : '' ?> flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all <?= ($activePage ?? '') == 'topup' ? '' : 'text-slate-600 hover:bg-slate-50' ?>">
            <span class="material-symbols-outlined">add_card</span> Top Up Cooling Credit
        </a>
        
        <div class="mt-6 px-4">
            <?php if ($creatorProfile): ?>
                <a href="<?= base_url('creator/dashboard') ?>" class="flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-50 text-indigo-700 rounded-xl font-bold text-xs border border-indigo-100 hover:bg-indigo-100 transition-all text-center">
                    <span class="material-symbols-outlined text-sm">potted_plant</span>
                    Creator Page <?= $creatorProfile['display_name'] ?>
                </a>
            <?php else: ?>
                <button onclick="openCreatorModal()" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-900 text-white rounded-xl font-bold text-xs hover:bg-slate-800 transition-all text-center">
                    <span class="material-symbols-outlined text-sm">edit_square</span>
                    Daftar jadi Kreator!
                </button>
            <?php endif; ?>
        </div>

        <a href="<?= base_url('logout') ?>" class="sidebar-link flex items-center gap-3 px-4 py-3 text-red-500 hover:bg-red-50 rounded-xl font-medium transition-all mt-auto">
            <span class="material-symbols-outlined">logout</span> Keluar
        </a>
    </nav>

    <div class="p-4 border-t border-slate-100">
        <div class="bg-gradient-to-br from-[#4F46E5] to-[#22D3EE] p-4 rounded-2xl text-white shadow-lg shadow-indigo-200">
            <p class="text-xs font-medium opacity-80 mb-1">StarSoul Kamu</p>
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-xl">auto_awesome</span>
                <?php 
                    $userModel = new \App\Models\UserModel();
                    $starsoulValue = $userModel->calculateStarsoul($user);
                ?>
                <span class="text-xl font-bold"><?= number_format($starsoulValue, 1) ?></span>
            </div>
        </div>
    </div>
</aside>

<!-- Modal Jadi Kreator -->
<div id="creatorModal" class="fixed inset-0 z-[9999] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Overlay -->
        <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" aria-hidden="true" onclick="closeCreatorModal()"></div>

        <!-- Modal Panel -->
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-3xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="relative bg-white">
                <!-- Close Button -->
                <button onclick="closeCreatorModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>

                <!-- Header Image/Pattern -->
                <div class="h-32 bg-gradient-to-r from-indigo-600 to-violet-600 flex items-center justify-center">
                    <div class="w-20 h-20 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center text-white">
                        <span class="material-symbols-outlined text-4xl">potted_plant</span>
                    </div>
                </div>

                <div class="px-8 pt-6 pb-8">
                    <div class="text-center mb-8">
                        <h3 class="text-2xl font-bold text-slate-900 mb-2" id="modal-title">Jadi Kreator di NusaShare</h3>
                        <p class="text-slate-500 text-sm">Wujudkan ide kreatifmu dan jangkau pembaca di seluruh Nusantara.</p>
                    </div>

                    <div class="space-y-4 mb-8">
                        <div class="flex gap-4">
                            <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined">auto_graph</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm mb-1">Algoritma Adil</h4>
                                <p class="text-slate-500 text-xs leading-relaxed">Karyamu tidak akan tenggelam. Kami memprioritaskan kualitas dan relevansi untuk setiap pembaca.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined">group</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm mb-1">Pembaca Setia</h4>
                                <p class="text-slate-500 text-xs leading-relaxed">Akses ke komunitas yang benar-benar menghargai setiap kata yang kamu tulis.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined">insights</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm mb-1">Insight Mendalam</h4>
                                <p class="text-slate-500 text-xs leading-relaxed">Pelajari perkembangan karyamu dengan data yang jujur, transparan, dan mudah dipahami.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-10 h-10 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined">payments</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm mb-1">Dukungan Finansial</h4>
                                <p class="text-slate-500 text-xs leading-relaxed">Mulai hasilkan pendapatan dari karya terbaikmu melalui sistem Cooling Credit kami.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3">
                        <a href="<?= base_url('creator/register') ?>" class="w-full flex items-center justify-center gap-2 px-6 py-3.5 bg-indigo-600 text-white rounded-2xl font-bold text-sm shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-all">
                            Daftar Sekarang
                        </a>
                        <button onclick="closeCreatorModal()" class="w-full py-3 text-slate-500 text-sm font-semibold hover:text-slate-700 transition-colors">
                            Mungkin Nanti
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openCreatorModal() {
    const modal = document.getElementById('creatorModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Animate panel
    const panel = modal.querySelector('.inline-block');
    panel.classList.add('animate-[zoomIn_0.3s_ease-out]');
}

function closeCreatorModal() {
    const modal = document.getElementById('creatorModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close on escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeCreatorModal();
});
</script>

<style>
@keyframes zoomIn {
    from { opacity: 0; transform: scale(0.95) translateY(10px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}

#mobile-refresh-indicator {
    position: fixed;
    top: calc(0.75rem + env(safe-area-inset-top));
    left: 50%;
    z-index: 9998;
    display: none;
    transform: translate(-50%, -120%);
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 0.85rem;
    border: 1px solid rgba(226, 232, 240, 0.9);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.95);
    box-shadow: 0 14px 35px rgba(15, 23, 42, 0.14);
    color: #4F46E5;
    font-size: 0.72rem;
    font-weight: 800;
    pointer-events: none;
    transition: transform 160ms ease, opacity 160ms ease;
}

#mobile-refresh-indicator.is-visible {
    display: flex;
    opacity: 1;
}

#mobile-refresh-indicator.is-ready .material-symbols-outlined {
    transform: rotate(180deg);
}

#mobile-refresh-indicator.is-loading .material-symbols-outlined {
    animation: refreshSpin 0.8s linear infinite;
}

@keyframes refreshSpin {
    to { transform: rotate(360deg); }
}
</style>

<div id="mobile-refresh-indicator" aria-hidden="true">
    <span class="material-symbols-outlined text-base">keyboard_arrow_down</span>
    <span id="mobile-refresh-text">Tarik untuk refresh</span>
</div>

<script>
(function () {
    if (!window.matchMedia || !window.matchMedia('(max-width: 1023px)').matches) return;
    if (window.__nusaPullToRefreshReady) return;
    window.__nusaPullToRefreshReady = true;

    const indicator = document.getElementById('mobile-refresh-indicator');
    const label = document.getElementById('mobile-refresh-text');
    const threshold = 86;
    let startY = 0;
    let pullDistance = 0;
    let tracking = false;
    let scrollTarget = null;

    function findScrollable(el) {
        while (el && el !== document.body && el !== document.documentElement) {
            const style = window.getComputedStyle(el);
            const canScroll = /(auto|scroll)/.test(style.overflowY) && el.scrollHeight > el.clientHeight;
            if (canScroll) return el;
            el = el.parentElement;
        }
        return document.scrollingElement || document.documentElement;
    }

    function isAtTop(el) {
        return (el ? el.scrollTop : window.scrollY) <= 0;
    }

    function resetIndicator() {
        pullDistance = 0;
        tracking = false;
        indicator.classList.remove('is-visible', 'is-ready', 'is-loading');
        indicator.style.transform = 'translate(-50%, -120%)';
        label.textContent = 'Tarik untuk refresh';
    }

    document.addEventListener('touchstart', function (event) {
        if (event.touches.length !== 1) return;
        const target = event.target;
        if (target.closest && target.closest('input, textarea, select, button, a')) return;

        scrollTarget = findScrollable(target);
        if (!isAtTop(scrollTarget)) return;

        startY = event.touches[0].clientY;
        tracking = true;
    }, { passive: true });

    document.addEventListener('touchmove', function (event) {
        if (!tracking || event.touches.length !== 1 || !isAtTop(scrollTarget)) return;

        pullDistance = Math.max(0, event.touches[0].clientY - startY);
        if (pullDistance < 10) return;

        const eased = Math.min(74, pullDistance * 0.45);
        indicator.classList.add('is-visible');
        indicator.style.transform = 'translate(-50%, ' + eased + 'px)';

        if (pullDistance >= threshold) {
            indicator.classList.add('is-ready');
            label.textContent = 'Lepas untuk refresh';
        } else {
            indicator.classList.remove('is-ready');
            label.textContent = 'Tarik untuk refresh';
        }
    }, { passive: true });

    document.addEventListener('touchend', function () {
        if (!tracking) return;

        if (pullDistance >= threshold && isAtTop(scrollTarget)) {
            indicator.classList.add('is-visible', 'is-loading');
            indicator.classList.remove('is-ready');
            indicator.style.transform = 'translate(-50%, 74px)';
            label.textContent = 'Memuat ulang...';
            window.location.reload();
            return;
        }

        resetIndicator();
    }, { passive: true });

    document.addEventListener('touchcancel', resetIndicator, { passive: true });
})();
</script>
