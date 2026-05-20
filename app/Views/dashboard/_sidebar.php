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
<nav class="lg:hidden fixed bottom-0 inset-x-0 z-50 bg-white/95 backdrop-blur-md border-t border-slate-200 flex items-stretch safe-bottom">
    <?php foreach ($navItems as $item): ?>
        <?php $isActive = $activePage === $item['page']; ?>
        <a href="<?= $item['href'] ?>" class="flex-1 flex flex-col items-center justify-center py-2 gap-0.5 text-xs font-medium transition-all duration-200 <?= ($item['danger'] ?? false) ? 'text-red-400 active:text-red-600' : ($isActive ? 'text-indigo-600' : 'text-slate-400 active:text-indigo-600') ?>">
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
                    Login sebagai <?= $creatorProfile['display_name'] ?>
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
</style>
