<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= $title ?? 'Kelola Karya - NusaShare' ?></title>
    
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
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <?= view('creator/_sidebar', [
        'activePage'     => 'content',
        'user'           => $user,
        'creatorProfile' => $creatorProfile,
        'username'       => $username
    ]) ?>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-full overflow-hidden bg-slate-50">
        <!-- Top Nav -->
        <header class="bg-white border-b border-slate-200 px-4 md:px-8 py-3 md:py-4 flex items-center justify-between">
            <h2 class="text-base md:text-lg font-bold text-slate-900">Kelola Karya</h2>
            
            <div class="flex items-center gap-3">
                <a href="<?= base_url('creator/content/create') ?>" class="flex items-center gap-1.5 px-3 md:px-4 py-2 bg-indigo-600 text-white rounded-xl font-bold text-xs md:text-sm hover:bg-indigo-700 transition-all">
                    <span class="material-symbols-outlined text-sm">add</span>
                    <span class="hidden sm:inline">Buat Karya Baru</span>
                    <span class="sm:hidden">Buat</span>
                </a>
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
            
            <div class="mb-5 md:mb-8">
                <h1 class="text-xl md:text-2xl font-bold text-slate-900">Daftar Karya</h1>
                <p class="text-slate-500 text-xs md:text-sm mt-1">Semua cerita dan karyamu yang sedang dalam proses atau sudah terbit.</p>
            </div>

            <?php if (session()->getFlashdata('message')): ?>
                <div class="mb-5 rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                    <?= esc(session()->getFlashdata('message')) ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="mb-5 rounded-2xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
                    <?php $errors = session()->getFlashdata('errors'); ?>
                    <?= esc(is_array($errors) ? reset($errors) : $errors) ?>
                </div>
            <?php endif; ?>

            <!-- Filters & Search -->
            <div class="bg-white rounded-3xl p-3 md:p-4 shadow-sm border border-slate-100 mb-5 md:mb-8 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-2 bg-slate-50 px-4 py-2 rounded-2xl w-full sm:w-auto sm:flex-1 sm:max-w-sm">
                    <span class="material-symbols-outlined text-slate-400 text-lg">search</span>
                    <input type="text" placeholder="Cari judul karya..." class="bg-transparent border-none focus:outline-none text-sm w-full text-slate-600">
                </div>
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <select class="bg-slate-50 px-4 py-2 rounded-2xl text-sm font-medium text-slate-600 focus:outline-none border-none cursor-pointer w-full sm:w-auto">
                        <option value="">Semua Status</option>
                        <option value="published">Terbit</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
            </div>

            <!-- Content Table -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[500px]">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-5 md:px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Karya</th>
                            <th class="px-5 md:px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center">Status</th>
                            <th class="px-5 md:px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center">Update</th>
                            <th class="px-5 md:px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($works)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-slate-50 text-slate-200 rounded-full flex items-center justify-center mb-4">
                                            <span class="material-symbols-outlined text-3xl">auto_stories</span>
                                        </div>
                                        <p class="text-slate-500 font-medium text-sm">Belum ada karya yang dibuat.</p>
                                        <a href="<?= base_url('creator/content/create') ?>" class="mt-4 px-6 py-2 bg-indigo-50 text-indigo-600 rounded-xl font-bold text-xs hover:bg-indigo-100 transition-all">Mulai Menulis</a>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($works as $work): ?>
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-5 md:px-6 py-4">
                                        <div class="flex items-center gap-3 md:gap-4">
                                            <div class="w-10 md:w-12 h-14 md:h-16 bg-slate-100 rounded-lg overflow-hidden flex-shrink-0 shadow-sm border border-slate-200">
                                                <?php if (!empty($work['cover_url'])): ?>
                                                    <?php 
                                                        $cUrl = base_url('image/cover/' . $work['id']);
                                                        if (empty($work['cover_url'])) {
                                                            $cUrl = base_url('assets/icon/logonuss.png');
                                                        }
                                                    ?>
                                                    <img src="<?= $cUrl ?>" class="w-full h-full object-cover">
                                                <?php else: ?>
                                                    <div class="w-full h-full flex items-center justify-center text-slate-300">
                                                        <span class="material-symbols-outlined text-sm">image</span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="min-w-0">
                                                <h4 class="font-bold text-slate-900 text-sm line-clamp-1"><?= $work['title'] ?></h4>
                                                <p class="text-[10px] text-slate-400 mt-1 uppercase font-bold tracking-tight"><?= $work['content_type'] === 'image' ? 'Gambar' : 'Teks' ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 md:px-6 py-4 text-center">
                                        <?php if (($work['status'] ?? 'draft') === 'published'): ?>
                                            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-bold rounded-full uppercase tracking-tight">Terbit</span>
                                        <?php else: ?>
                                            <span class="px-3 py-1 bg-amber-50 text-amber-600 text-[10px] font-bold rounded-full uppercase tracking-tight">Draft</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 md:px-6 py-4 text-center">
                                        <p class="text-xs text-slate-500 font-medium whitespace-nowrap"><?= date('d M Y', strtotime($work['updated_at'] ?? $work['created_at'])) ?></p>
                                    </td>
                                    <td class="px-5 md:px-6 py-4">
                                        <div class="flex items-center justify-end gap-1.5 md:gap-2">
                                            <?php if ($work['content_type'] === 'image'): ?>
                                                <a href="<?= base_url('creator/content/' . $work['id'] . '/images') ?>" class="flex items-center gap-1 px-2 md:px-3 py-1.5 text-xs font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-all">
                                                    <span class="material-symbols-outlined text-sm">image</span>
                                                    <span class="hidden sm:inline">Gambar</span>
                                                </a>
                                            <?php else: ?>
                                                <a href="<?= base_url('creator/content/' . $work['id'] . '/chapters') ?>" class="flex items-center gap-1 px-2 md:px-3 py-1.5 text-xs font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-all">
                                                    <span class="material-symbols-outlined text-sm">auto_stories</span>
                                                    <span class="hidden sm:inline">Bab</span>
                                                </a>
                                            <?php endif; ?>
                                            <?php if (($work['status'] ?? 'draft') === 'published'): ?>
                                                <form method="post" action="<?= base_url('creator/content/' . $work['id'] . '/archive') ?>" class="inline">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="p-2 text-amber-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="Tarik Karya" onclick="return confirm('Tarik karya ini dari publik dan ubah menjadi draft?')">
                                                        <span class="material-symbols-outlined text-lg">archive</span>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <form method="post" action="<?= base_url('creator/content/' . $work['id'] . '/publish') ?>" class="inline">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="p-2 text-emerald-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all" title="Terbitkan Karya" onclick="return confirm('Terbitkan karya ini ke publik?')">
                                                        <span class="material-symbols-outlined text-lg">publish</span>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            <a href="<?= base_url('creator/content/' . $work['id'] . '/edit') ?>" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-lg transition-all" title="Edit Info">
                                                <span class="material-symbols-outlined text-lg">tune</span>
                                            </a>
                                            <form method="post" action="<?= base_url('creator/content/' . $work['id'] . '/delete') ?>" class="inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="p-2 text-rose-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all" title="Hapus Karya" onclick="return confirm('Hapus karya ini secara permanen? Bab, gambar, dan interaksi terkait juga akan dihapus.')">
                                                    <span class="material-symbols-outlined text-lg">delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                </div>
            </div>

            <!-- Pagination -->
            <?php if (!empty($works)): ?>
            <div class="mt-5 md:mt-6 flex items-center justify-between">
                <p class="text-xs text-slate-500">Menampilkan <?= count($works) ?> karya</p>
                <div class="flex items-center gap-2">
                    <button class="w-8 h-8 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-400 cursor-not-allowed">
                        <span class="material-symbols-outlined text-sm">chevron_left</span>
                    </button>
                    <button class="w-8 h-8 flex items-center justify-center rounded-xl bg-indigo-600 text-white shadow-sm font-bold text-xs uppercase tracking-tight">
                        1
                    </button>
                    <button class="w-8 h-8 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-400 cursor-not-allowed">
                        <span class="material-symbols-outlined text-sm">chevron_right</span>
                    </button>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </main>
</body>
</html>
