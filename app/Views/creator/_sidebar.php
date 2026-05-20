<?php
/**
 * Sidebar Khusus Kreator
 */
 ?>
<!-- Sidebar -->
<aside class="w-64 bg-slate-900 text-white hidden lg:flex flex-col flex-shrink-0 h-full">
    <div class="p-6 border-b border-slate-800 flex items-center gap-3">
        <img src="<?= base_url('assets/icon/logonus.png') ?>" alt="Logo" class="w-8 h-8">
        <span class="font-bold text-xl text-white tracking-tight">NusaShare <span class="text-indigo-400 text-xs align-top ml-1">Kreator</span></span>
    </div>
    
    <nav class="flex-1 p-4 space-y-2 mt-4 overflow-y-auto custom-scrollbar">
        <p class="px-4 mb-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Utama</p>
        
        <a href="<?= base_url('creator/dashboard') ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all <?= ($activePage ?? '') == 'dashboard' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
            <span class="material-symbols-outlined">dashboard</span> Dashboard
        </a>
        
        <a href="<?= base_url('creator/content') ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all <?= ($activePage ?? '') == 'content' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
            <span class="material-symbols-outlined">auto_stories</span> Kelola Karya
        </a>
        
        <div class="pt-6 pb-2 px-4">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Insight & Ads</span>
        </div>
        
        <a href="<?= base_url('creator/stats') ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all <?= ($activePage ?? '') == 'stats' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
            <span class="material-symbols-outlined">analytics</span> Statistik
        </a>
        
        <a href="<?= base_url('creator/monetization') ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all <?= ($activePage ?? '') == 'monetization' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
            <span class="material-symbols-outlined">payments</span> Monetisasi
        </a>

        <div class="pt-6 pb-2 px-4">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Lainnya</span>
        </div>

        <a href="<?= base_url('creator/settings') ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all <?= ($activePage ?? '') == 'settings' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
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
