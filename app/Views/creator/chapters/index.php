<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
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

    <?= view('creator/_sidebar', ['activePage' => 'content', 'user' => $user, 'creatorProfile' => $creatorProfile, 'username' => $username]) ?>

    <main class="flex-1 flex flex-col h-full overflow-hidden bg-slate-50">
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="<?= base_url('creator/content') ?>" class="p-2 hover:bg-slate-50 rounded-xl transition-all text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Daftar Bab</h2>
                    <p class="text-[10px] text-slate-400 truncate max-w-xs"><?= esc($work['title']) ?></p>
                </div>
            </div>
            <a href="<?= base_url('creator/content/' . $work['id'] . '/chapters/create') ?>" class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-xl font-bold text-sm hover:bg-indigo-700 transition-all">
                <span class="material-symbols-outlined text-sm">add</span> Tulis Bab Baru
            </a>
        </header>

        <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
            <div class="max-w-3xl mx-auto">

                <?php if ($msg = session()->getFlashdata('message')): ?>
                    <div class="mb-6 px-5 py-4 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-2xl text-sm font-medium flex items-center gap-3">
                        <span class="material-symbols-outlined text-base">check_circle</span> <?= esc($msg) ?>
                    </div>
                <?php endif; ?>

                <?php if ($warn = session()->getFlashdata('upload_warning')): ?>
                    <div class="mb-6 px-5 py-4 bg-amber-50 text-amber-700 border border-amber-100 rounded-2xl text-sm font-medium flex items-center gap-3">
                        <span class="material-symbols-outlined text-base">warning</span> <?= esc($warn) ?>
                    </div>
                <?php endif; ?>

                <?php if (empty($chapters)): ?>
                    <div class="bg-white rounded-3xl border border-slate-100 p-16 text-center shadow-sm">
                        <span class="material-symbols-outlined text-5xl text-slate-200 block mb-4">edit_note</span>
                        <p class="text-slate-500 font-medium text-sm">Belum ada bab. Mulai tulis sekarang!</p>
                        <a href="<?= base_url('creator/content/' . $work['id'] . '/chapters/create') ?>" class="mt-5 inline-flex items-center gap-2 px-6 py-2 bg-indigo-600 text-white rounded-xl font-bold text-xs hover:bg-indigo-700 transition-all">
                            <span class="material-symbols-outlined text-sm">add</span> Tulis Bab Pertama
                        </a>
                    </div>
                <?php else: ?>
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-slate-50/50 border-b border-slate-100">
                                    <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest w-10">#</th>
                                    <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Judul Bab</th>
                                    <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center">Akses</th>
                                    <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center">Status</th>
                                    <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center">Update</th>
                                    <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php foreach ($chapters as $chapter): ?>
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-4 text-sm font-bold text-slate-400"><?= $chapter['order_num'] ?></td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm font-bold text-slate-900"><?= esc($chapter['title']) ?></p>
                                            <p class="text-[10px] text-slate-400 mt-0.5"><?= mb_strimwidth(strip_tags($chapter['body'] ?? ''), 0, 60, '...') ?></p>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <?php if ($chapter['is_locked']): ?>
                                                <span class="flex items-center justify-center gap-1 text-amber-600">
                                                    <span class="material-symbols-outlined text-sm">lock</span>
                                                    <span class="text-[10px] font-bold uppercase"><?= $chapter['price'] ?> CC</span>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-[10px] font-bold text-emerald-600 uppercase">Gratis</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <?php if ($chapter['status'] === 'published'): ?>
                                                <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-bold rounded-full uppercase tracking-tight">Terbit</span>
                                            <?php else: ?>
                                                <span class="px-3 py-1 bg-amber-50 text-amber-600 text-[10px] font-bold rounded-full uppercase tracking-tight">Draft</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 text-center text-xs text-slate-400"><?= date('d M Y', strtotime($chapter['updated_at'])) ?></td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="<?= base_url('creator/content/' . $work['id'] . '/chapters/' . $chapter['id'] . '/edit') ?>" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all">
                                                    <span class="material-symbols-outlined text-lg">edit</span>
                                                </a>
                                                <form method="POST" action="<?= base_url('creator/content/' . $work['id'] . '/chapters/' . $chapter['id'] . '/delete') ?>" onsubmit="return confirm('Hapus bab ini?')">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                                                        <span class="material-symbols-outlined text-lg">delete</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <p class="mt-4 text-xs text-slate-400 text-right"><?= count($chapters) ?> bab total</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>
