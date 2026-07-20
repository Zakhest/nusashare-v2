<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= $title ?? 'Pengaturan Profil - NusaShare' ?></title>
    
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
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #E2E8F0; border-radius: 10px; }
        .form-input { transition: all 0.2s; }
        .form-input:focus { border-color: #4F46E5; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <?= view('creator/_sidebar', [
        'activePage'     => 'settings',
        'user'           => $user,
        'creatorProfile' => $creatorProfile,
        'username'       => $username
    ]) ?>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-full overflow-hidden bg-slate-50">
        <!-- Top Nav -->
        <header class="bg-white border-b border-slate-200 px-4 md:px-8 py-3 md:py-4 flex items-center justify-between">
            <h2 class="text-base md:text-lg font-bold text-slate-900">Pengaturan</h2>
            
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
            
            <div class="max-w-4xl mx-auto">
                <div class="mb-6 md:mb-10">
                    <h1 class="text-xl md:text-2xl font-bold text-slate-900">Profil Kreator</h1>
                    <p class="text-slate-500 text-xs md:text-sm mt-1">Sesuaikan bagaimana pembaca melihat identitasmu di paltform.</p>
                </div>

                <!-- Feedback Messages -->
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="mb-8 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-3 text-emerald-700 text-sm font-medium">
                        <span class="material-symbols-outlined text-lg">check_circle</span>
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="mb-8 p-4 bg-rose-50 border border-rose-100 rounded-2xl flex items-center gap-3 text-rose-700 text-sm font-medium">
                        <span class="material-symbols-outlined text-lg">error</span>
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <div class="bg-white rounded-[32px] shadow-sm border border-slate-100 overflow-hidden">
                    <form action="<?= base_url('creator/settings') ?>" method="POST" class="p-5 md:p-8 lg:p-12">
                        <?= csrf_field() ?>
                        
                        <div class="space-y-10">
                            <!-- Avatar Section (Placeholder for now) -->
                            <div class="flex flex-col md:flex-row items-center gap-8 pb-10 border-b border-slate-50">
                                <div class="relative">
                                    <div class="w-32 h-32 rounded-[40px] bg-indigo-600 flex items-center justify-center text-white text-4xl font-black shadow-xl shadow-indigo-100">
                                        <?= strtoupper(substr($creatorProfile['display_name'] ?? $username, 0, 1)) ?>
                                    </div>
                                    <button type="button" class="absolute -right-2 -bottom-2 w-10 h-10 bg-white rounded-2xl shadow-lg border border-slate-100 flex items-center justify-center text-slate-400 hover:text-indigo-600 transition-all">
                                        <span class="material-symbols-outlined text-lg">photo_camera</span>
                                    </button>
                                </div>
                                <div class="text-center md:text-left">
                                    <h3 class="font-bold text-slate-900 text-lg mb-1">Foto Profil</h3>
                                    <p class="text-xs text-slate-400 leading-relaxed mb-4">Maksimum 2MB. Format JPG, PNG, atau WEBP.</p>
                                    <button type="button" class="px-5 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-600 rounded-xl font-bold text-xs transition-all border border-slate-100">Ganti Foto</button>
                                </div>
                            </div>

                            <!-- Basic Info -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Nama Tampilan</label>
                                    <input type="text" name="display_name" value="<?= esc($creatorProfile['display_name'] ?? $username) ?>" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-medium text-slate-900 form-input focus:outline-none focus:bg-white" placeholder="Masukkan nama pena kamu...">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Username (ID)</label>
                                    <input type="text" value="<?= $username ?>" disabled class="w-full px-5 py-3.5 bg-slate-100 border border-slate-100 rounded-2xl text-sm font-medium text-slate-400 cursor-not-allowed" title="Username tidak dapat diubah">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Bio Kreator</label>
                                <textarea name="bio" rows="4" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-medium text-slate-900 form-input focus:outline-none focus:bg-white" placeholder="Ceritakan sedikit tentang dirimu atau jadwal rilis karyamu..."><?= esc($creatorProfile['bio'] ?? '') ?></textarea>
                            </div>

                            <!-- Save Actions -->
                            <div class="pt-10 border-t border-slate-50 flex items-center justify-between gap-4">
                                <p class="text-xs text-slate-400">
                                    * Informasi ini akan terlihat secara publik.
                                </p>
                                <div class="flex items-center gap-3">
                                    <button type="reset" class="px-6 py-3 text-slate-400 font-bold text-sm hover:text-slate-600 transition-all">Batal</button>
                                    <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold text-sm shadow-lg shadow-indigo-100 transition-all">Simpan Perubahan</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Danger Zone -->
                <div class="mt-6 md:mt-12 p-5 md:p-8 border border-rose-100 bg-rose-50/30 rounded-[32px]">
                    <h3 class="text-rose-600 font-black text-xs uppercase tracking-widest mb-4">Zona Berbahaya</h3>
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div>
                            <p class="font-bold text-slate-900 text-sm">Hentikan Mode Kreator</p>
                            <p class="text-xs text-slate-500 mt-1">Kamu akan kembali ke mode user biasa dan akses kreator akan dinonaktifkan.</p>
                        </div>
                        <button type="button" class="px-6 py-3 border border-rose-200 text-rose-500 hover:bg-rose-500 hover:text-white rounded-2xl font-bold text-xs transition-all whitespace-nowrap">
                            Nonaktifkan Kreator
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </main>
</body>
</html>
