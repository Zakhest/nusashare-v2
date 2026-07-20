<?php
/**
 * Sidebar Khusus Kreator - Mobile Friendly
 */
$_activePage = $activePage ?? '';
?>

<!-- ===== DESKTOP SIDEBAR ===== -->
<aside class="w-64 bg-slate-900 text-white hidden lg:flex flex-col flex-shrink-0 h-full">
    <div class="p-6 border-b border-slate-800 flex items-center gap-3">
        <img src="<?= base_url('assets/icon/logonus.png') ?>" alt="Logo" class="w-8 h-8">
        <span class="font-bold text-xl text-white tracking-tight">NusaShare <span class="text-indigo-400 text-xs align-top ml-1">Kreator</span></span>
    </div>
    
    <nav class="flex-1 p-4 space-y-2 mt-4 overflow-y-auto custom-scrollbar">
        <p class="px-4 mb-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Utama</p>
        
        <a href="<?= base_url('creator/dashboard') ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all <?= $_activePage == 'dashboard' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
            <span class="material-symbols-outlined">dashboard</span> Dashboard
        </a>
        
        <a href="<?= base_url('creator/content') ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all <?= $_activePage == 'content' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
            <span class="material-symbols-outlined">auto_stories</span> Kelola Karya
        </a>
        
        <div class="pt-6 pb-2 px-4">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Insight & Ads</span>
        </div>
        
        <a href="<?= base_url('creator/stats') ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all <?= $_activePage == 'stats' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
            <span class="material-symbols-outlined">analytics</span> Statistik
        </a>
        
        <a href="<?= base_url('creator/monetization') ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all <?= $_activePage == 'monetization' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
            <span class="material-symbols-outlined">payments</span> Monetisasi
        </a>

        <div class="pt-6 pb-2 px-4">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Lainnya</span>
        </div>

        <a href="<?= base_url('creator/settings') ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all <?= $_activePage == 'settings' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
            <span class="material-symbols-outlined">settings</span> Pengaturan Profil
        </a>

        <a href="<?= base_url('/dashboard') ?>" class="flex items-center gap-3 px-4 py-3 mt-4 text-emerald-400 hover:bg-emerald-400/10 rounded-xl font-medium transition-all">
            <span class="material-symbols-outlined">swap_horiz</span> Kembali Ke User
        </a>
        
        <a href="<?= base_url('creator/logout') ?>" class="flex items-center gap-3 px-4 py-3 text-red-400 hover:bg-red-400/10 rounded-xl font-medium transition-all">
            <span class="material-symbols-outlined">logout</span> Keluar
        </a>
    </nav>

    <div class="p-4 border-t border-slate-800">
        <div class="bg-slate-800 p-4 rounded-2xl flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-indigo-500 flex items-center justify-center text-white font-bold">
                <?= strtoupper(substr($username ?? 'K', 0, 1)) ?>
            </div>
            <div class="overflow-hidden">
                <p class="text-xs font-bold text-white truncate"><?= $creatorProfile['display_name'] ?? $username ?></p>
                <p class="text-[10px] text-slate-500 truncate">Kreator Terverifikasi</p>
            </div>
        </div>
    </div>
</aside>

<!-- ===== MOBILE: BACKDROP ===== -->
<div id="mobile-drawer-backdrop" 
     class="lg:hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-40 opacity-0 pointer-events-none transition-opacity duration-300"
     onclick="closeMobileDrawer()">
</div>

<!-- ===== MOBILE: SLIDE-IN DRAWER ===== -->
<div id="mobile-drawer"
     class="lg:hidden fixed top-0 left-0 h-full w-72 bg-slate-900 text-white z-50 flex flex-col -translate-x-full transition-transform duration-300 ease-in-out shadow-2xl">
    
    <!-- Drawer Header -->
    <div class="p-5 border-b border-slate-800 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <img src="<?= base_url('assets/icon/logonus.png') ?>" alt="Logo" class="w-7 h-7">
            <span class="font-bold text-lg text-white tracking-tight">NusaShare <span class="text-indigo-400 text-[10px] align-top ml-1">Kreator</span></span>
        </div>
        <button onclick="closeMobileDrawer()" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-all">
            <span class="material-symbols-outlined text-lg">close</span>
        </button>
    </div>

    <!-- Drawer User Info -->
    <div class="p-4 border-b border-slate-800">
        <div class="bg-slate-800 p-3 rounded-2xl flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                <?= strtoupper(substr($username ?? 'K', 0, 1)) ?>
            </div>
            <div class="overflow-hidden">
                <p class="text-xs font-bold text-white truncate"><?= $creatorProfile['display_name'] ?? $username ?></p>
                <p class="text-[10px] text-indigo-400 truncate font-medium">Mode Kreator</p>
            </div>
        </div>
    </div>

    <!-- Drawer Navigation -->
    <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
        <p class="px-3 pt-2 pb-1 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Utama</p>
        
        <a href="<?= base_url('creator/dashboard') ?>" onclick="closeMobileDrawer()" class="flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-all <?= $_activePage == 'dashboard' ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
            <span class="material-symbols-outlined text-[20px]">dashboard</span> 
            <span class="text-sm">Dashboard</span>
        </a>
        
        <a href="<?= base_url('creator/content') ?>" onclick="closeMobileDrawer()" class="flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-all <?= $_activePage == 'content' ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
            <span class="material-symbols-outlined text-[20px]">auto_stories</span>
            <span class="text-sm">Kelola Karya</span>
        </a>
        
        <p class="px-3 pt-4 pb-1 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Insight & Ads</p>
        
        <a href="<?= base_url('creator/stats') ?>" onclick="closeMobileDrawer()" class="flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-all <?= $_activePage == 'stats' ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
            <span class="material-symbols-outlined text-[20px]">analytics</span>
            <span class="text-sm">Statistik</span>
        </a>
        
        <a href="<?= base_url('creator/monetization') ?>" onclick="closeMobileDrawer()" class="flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-all <?= $_activePage == 'monetization' ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
            <span class="material-symbols-outlined text-[20px]">payments</span>
            <span class="text-sm">Monetisasi</span>
        </a>

        <p class="px-3 pt-4 pb-1 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Lainnya</p>

        <a href="<?= base_url('creator/settings') ?>" onclick="closeMobileDrawer()" class="flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-all <?= $_activePage == 'settings' ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
            <span class="material-symbols-outlined text-[20px]">settings</span>
            <span class="text-sm">Pengaturan Profil</span>
        </a>

        <div class="pt-4 border-t border-slate-800 mt-2 space-y-1">
            <a href="<?= base_url('/dashboard') ?>" class="flex items-center gap-3 px-3 py-3 text-emerald-400 hover:bg-emerald-400/10 rounded-xl font-medium transition-all">
                <span class="material-symbols-outlined text-[20px]">swap_horiz</span>
                <span class="text-sm">Kembali Ke User</span>
            </a>
            <a href="<?= base_url('creator/logout') ?>" class="flex items-center gap-3 px-3 py-3 text-red-400 hover:bg-red-400/10 rounded-xl font-medium transition-all">
                <span class="material-symbols-outlined text-[20px]">logout</span>
                <span class="text-sm">Keluar</span>
            </a>
        </div>
    </nav>
</div>

<!-- ===== MOBILE: BOTTOM NAVIGATION BAR ===== -->
<nav class="lg:hidden fixed bottom-0 left-0 right-0 z-30 bg-white/95 backdrop-blur-md border-t border-slate-200 px-2 py-1 safe-area-bottom">
    <div class="flex items-center justify-around">
        <!-- Dashboard -->
        <a href="<?= base_url('creator/dashboard') ?>" class="flex flex-col items-center gap-0.5 px-3 py-2 rounded-xl transition-all min-w-0 <?= $_activePage == 'dashboard' ? 'text-indigo-600' : 'text-slate-400' ?>">
            <span class="material-symbols-outlined text-[22px] <?= $_activePage == 'dashboard' ? 'text-indigo-600' : '' ?>"
                  style="<?= $_activePage == 'dashboard' ? 'font-variation-settings: \'FILL\' 1;' : '' ?>">dashboard</span>
            <span class="text-[9px] font-bold tracking-tight <?= $_activePage == 'dashboard' ? 'text-indigo-600' : 'text-slate-400' ?>">Home</span>
        </a>

        <!-- Kelola Karya -->
        <a href="<?= base_url('creator/content') ?>" class="flex flex-col items-center gap-0.5 px-3 py-2 rounded-xl transition-all min-w-0 <?= $_activePage == 'content' ? 'text-indigo-600' : 'text-slate-400' ?>">
            <span class="material-symbols-outlined text-[22px]"
                  style="<?= $_activePage == 'content' ? 'font-variation-settings: \'FILL\' 1;' : '' ?>">auto_stories</span>
            <span class="text-[9px] font-bold tracking-tight">Karya</span>
        </a>

        <!-- Buat Karya (FAB center button) -->
        <a href="<?= base_url('creator/content/create') ?>" class="flex flex-col items-center -mt-5">
            <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-all active:scale-95">
                <span class="material-symbols-outlined text-white text-[22px]">add</span>
            </div>
            <span class="text-[9px] font-bold text-indigo-600 mt-1">Buat</span>
        </a>

        <!-- Statistik -->
        <a href="<?= base_url('creator/stats') ?>" class="flex flex-col items-center gap-0.5 px-3 py-2 rounded-xl transition-all min-w-0 <?= $_activePage == 'stats' ? 'text-indigo-600' : 'text-slate-400' ?>">
            <span class="material-symbols-outlined text-[22px]"
                  style="<?= $_activePage == 'stats' ? 'font-variation-settings: \'FILL\' 1;' : '' ?>">analytics</span>
            <span class="text-[9px] font-bold tracking-tight">Statistik</span>
        </a>

        <!-- Menu (Hamburger) -->
        <button onclick="openMobileDrawer()" class="flex flex-col items-center gap-0.5 px-3 py-2 rounded-xl transition-all min-w-0 text-slate-400 active:text-indigo-600">
            <span class="material-symbols-outlined text-[22px]">menu</span>
            <span class="text-[9px] font-bold tracking-tight">Menu</span>
        </button>
    </div>
</nav>

<script>
function openMobileDrawer() {
    document.getElementById('mobile-drawer').classList.remove('-translate-x-full');
    document.getElementById('mobile-drawer-backdrop').classList.remove('opacity-0', 'pointer-events-none');
    document.body.style.overflow = 'hidden';
}
function closeMobileDrawer() {
    document.getElementById('mobile-drawer').classList.add('-translate-x-full');
    document.getElementById('mobile-drawer-backdrop').classList.add('opacity-0', 'pointer-events-none');
    document.body.style.overflow = '';
}
// Close drawer on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeMobileDrawer();
});
</script>
