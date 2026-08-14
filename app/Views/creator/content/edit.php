<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= $title ?? 'Edit Karya - NusaShare' ?></title>
    
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
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

    <?= view('creator/_sidebar', [
        'activePage'     => 'content',
        'user'           => $user,
        'creatorProfile' => $creatorProfile,
        'username'       => $username
    ]) ?>

    <main class="flex-1 flex flex-col h-full overflow-hidden bg-slate-50">
        <!-- Top Nav -->
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="<?= base_url('creator/content') ?>" class="p-2 hover:bg-slate-50 rounded-xl transition-all text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Edit Karya</h2>
                    <p class="text-[10px] text-slate-400 font-medium truncate max-w-xs"><?= esc($work['title']) ?></p>
                </div>
            </div>
            <div class="flex items-center gap-3 border-l pl-6 border-slate-100">
                <div class="text-right">
                    <p class="text-xs font-bold text-slate-900"><?= $creatorProfile['display_name'] ?? $username ?></p>
                    <p class="text-[10px] text-slate-500 italic">Mode Kreator</p>
                </div>
            </div>
        </header>

        <!-- Body -->
        <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
            <div class="max-w-4xl mx-auto">

                <div class="mb-8">
                    <h1 class="text-2xl font-bold text-slate-900">Edit Detail Karya</h1>
                    <p class="text-slate-500 text-sm mt-1">Perbarui informasi karya kamu. Perubahan akan langsung tersimpan.</p>
                </div>

                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-2xl">
                        <div class="flex items-center gap-3 text-red-600 mb-2">
                            <span class="material-symbols-outlined text-sm">error</span>
                            <span class="text-xs font-bold uppercase tracking-tight">Terjadi Kesalahan</span>
                        </div>
                        <ul class="list-disc list-inside text-xs text-red-500 space-y-1">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('creator/content/' . $work['id'] . '/update') ?>" method="POST" enctype="multipart/form-data" class="space-y-8">
                    <?= csrf_field() ?>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Cover -->
                        <div class="md:col-span-1" id="coverUploadContainer">
                            <label class="block text-sm font-bold text-slate-700 mb-4">Sampul Karya</label>
                            <div class="relative group">
                                <div id="coverPreview" class="w-full aspect-[3/4] bg-slate-100 rounded-3xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center overflow-hidden transition-all group-hover:border-indigo-300">
                                    <?php if (!empty($work['cover_url'])): ?>
                                        <img src="<?= base_url('image/cover/' . $work['id']) ?>" class="w-full h-full object-cover" id="currentCoverImg">
                                    <?php else: ?>
                                        <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">image</span>
                                        <p class="text-[10px] text-slate-400 font-medium px-6 text-center">Klik untuk ganti sampul</p>
                                    <?php endif; ?>
                                </div>
                                <input type="file" name="cover" id="coverInput" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*">
                            </div>
                            <?php if (!empty($work['cover_url'])): ?>
                                <p class="mt-3 text-[10px] text-indigo-500 font-medium">✓ Sudah ada sampul. Upload baru untuk mengganti.</p>
                            <?php else: ?>
                                <p class="mt-3 text-[10px] text-slate-400 italic">Rekomendasi rasio 3:4, maks 2MB.</p>
                            <?php endif; ?>
                        </div>

                        <!-- Form Fields -->
                        <div class="md:col-span-2 space-y-6">
                            <!-- Status Badge -->
                            <div class="flex items-center gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                <span class="material-symbols-outlined text-slate-400">info</span>
                                <div>
                                    <p class="text-xs font-bold text-slate-700">Status saat ini:
                                        <?php if ($work['status'] === 'published'): ?>
                                            <span class="text-emerald-600">Terbit</span>
                                        <?php else: ?>
                                            <span class="text-amber-600">Draft</span>
                                        <?php endif; ?>
                                    </p>
                                    <p class="text-[10px] text-slate-500 mt-0.5">Dibuat <?= date('d M Y', strtotime($work['created_at'])) ?></p>
                                </div>
                            </div>

                            <!-- Judul -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Judul Karya</label>
                                <input type="text" name="title" value="<?= esc(old('title', $work['title'])) ?>"
                                    class="w-full px-5 py-4 bg-white border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-900 font-medium">
                            </div>

                            <!-- Genre -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Genre</label>
                                <input type="text" name="genre" value="<?= esc(old('genre', $work['genre'] ?? '')) ?>" placeholder="Contoh: Action, Romance, Fantasy..." 
                                    class="w-full px-5 py-4 bg-white border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-900 font-medium">
                            </div>

                            <!-- Tipe & Status -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Tipe Konten</label>
                                    <select name="content_type" id="contentTypeSelect" class="w-full px-5 py-4 bg-white border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-900 font-medium appearance-none cursor-pointer">
                                        <option value="novel"  <?= (old('content_type', $work['content_type']) === 'novel') ? 'selected' : '' ?>>Novel (Teks Saja)</option>
                                        <option value="light_novel" <?= (old('content_type', $work['content_type']) === 'light_novel') ? 'selected' : '' ?>>Light Novel (Teks + Ilustrasi)</option>
                                        <option value="comic"  <?= (old('content_type', $work['content_type']) === 'comic') ? 'selected' : '' ?>>Komik (Manga / Webtoon)</option>
                                        <option value="image" <?= (old('content_type', $work['content_type']) === 'image') ? 'selected' : '' ?>>Galeri Gambar</option>
                                        <option value="pdf"   <?= (old('content_type', $work['content_type']) === 'pdf')   ? 'selected' : '' ?>>Dokumen PDF</option>
                                        <option value="text"  <?= (old('content_type', $work['content_type']) === 'text')  ? 'selected' : '' ?>>Teks / Puisi</option>
                                        <option value="artikel" <?= (old('content_type', $work['content_type']) === 'artikel') ? 'selected' : '' ?>>Artikel (Blog / Infobox)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Status</label>
                                    <select name="status" class="w-full px-5 py-4 bg-white border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-900 font-medium appearance-none cursor-pointer">
                                        <option value="draft"      <?= (old('status', $work['status']) === 'draft')      ? 'selected' : '' ?>>Simpan sebagai Draft</option>
                                        <option value="published"  <?= (old('status', $work['status']) === 'published')  ? 'selected' : '' ?>>Terbitkan</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Gallery Upload (Visible only for Image format) -->
                            <div id="galleryUploadContainer" class="<?= (old('content_type', $work['content_type']) === 'image') ? '' : 'hidden' ?> animate-in fade-in slide-in-from-top-4 duration-500">
                                <label class="block text-sm font-bold text-slate-700 mb-4">Galeri Gambar (Multiple)</label>
                                <div class="relative group">
                                    <div id="galleryPreview" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 min-h-[120px] p-4 bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200 transition-all group-hover:border-indigo-300">
                                        <div class="col-span-full flex flex-col items-center justify-center py-4 text-slate-400">
                                            <span class="material-symbols-outlined text-3xl mb-1">add_photo_alternate</span>
                                            <p class="text-[10px] font-medium">Klik untuk tambah gambar ke galeri</p>
                                        </div>
                                    </div>
                                    <input type="file" name="gallery_images[]" id="galleryInput" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*" multiple>
                                </div>
                                <p class="mt-3 text-[10px] text-slate-400 italic">Pilih gambar tambahan. Gambar yang sudah ada bisa dikelola di menu Kelola Gambar.</p>
                            </div>

                            <!-- Deskripsi -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Sinopsis / Deskripsi</label>
                                <textarea name="description" rows="6"
                                    class="w-full px-5 py-4 bg-white border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-900 font-medium resize-none"><?= esc(old('description', $work['description'])) ?></textarea>
                            </div>

                            <!-- Pengaturan Akses & Status -->
                            <div id="workConfigurationSection" class="p-6 bg-indigo-50/50 rounded-3xl border border-indigo-100 space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-2 uppercase tracking-wider">Status Cerita</label>
                                        <div class="flex items-center gap-4">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="radio" name="work_status" value="ongoing" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500" <?= (old('work_status', $work['work_status']) === 'ongoing') ? 'checked' : '' ?>>
                                                <span class="text-xs text-slate-700">Ongoing</span>
                                            </label>
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="radio" name="work_status" value="ended" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500" <?= (old('work_status', $work['work_status']) === 'ended') ? 'checked' : '' ?>>
                                                <span class="text-xs text-slate-700">Ended</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-2 uppercase tracking-wider">Batasi Akses Cerita</label>
                                        <div class="flex items-center gap-4">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="radio" name="access_type" value="full" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500" <?= (old('access_type', $work['access_type']) === 'full') ? 'checked' : '' ?>>
                                                <span class="text-xs text-slate-700">Seluruh Cerita</span>
                                            </label>
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="radio" name="access_type" value="chapter" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500" <?= (old('access_type', $work['access_type']) === 'chapter') ? 'checked' : '' ?>>
                                                <span class="text-xs text-slate-700">Bab per Bab</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between pt-6 border-t border-indigo-100/50">
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-900">Monetisasi</h4>
                                        <p class="text-[10px] text-slate-500 mt-0.5">Tentukan apakah karya ini gratis atau berbayar.</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_paid" id="isPaidToggle" class="sr-only peer" <?= (old('is_paid', $work['is_paid']) == 1) ? 'checked' : '' ?>>
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        <span class="ml-3 text-xs font-bold text-slate-700 peer-checked:text-indigo-600" id="accessLabel"><?= (old('is_paid', $work['is_paid']) == 1) ? 'Berbayar' : 'Gratis' ?></span>
                                    </label>
                                </div>

                                <div id="paidSettings" class="<?= (old('is_paid', $work['is_paid']) == 1) ? '' : 'hidden' ?> space-y-6 pt-6 border-t border-indigo-100/50">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-700 mb-2 uppercase tracking-wider">Harga (CC)</label>
                                            <div class="relative">
                                                <input type="number" name="price" value="<?= esc(old('price', $work['price'])) ?>" 
                                                    class="w-full pl-5 pr-12 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-bold text-slate-900">
                                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-400">CC</span>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-700 mb-2 uppercase tracking-wider">Durasi Timer (Detik)</label>
                                            <div class="relative">
                                                <input type="number" name="timer_duration" value="<?= esc(old('timer_duration', $work['timer_duration'] ?: 30)) ?>" 
                                                    class="w-full pl-5 pr-12 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-bold text-slate-900">
                                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-400">SEC</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-2 uppercase tracking-wider">Teks Watermark</label>
                                        <input type="text" name="watermark_text" value="<?= esc(old('watermark_text', $work['watermark_text'] ?: ($creatorProfile['display_name'] ?? $username))) ?>" placeholder="Contoh: Milik <?= $creatorProfile['display_name'] ?? $username ?>"
                                            class="w-full px-5 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-900 font-medium">
                                        <p class="mt-2 text-[9px] text-slate-400 italic">Watermark akan muncul saat user membayar untuk melihat preview.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- ══════════════════════════════════════════════
                                 Artikel Content Editor (hanya muncul jika tipe Artikel)
                            ══════════════════════════════════════════════ -->
                            <div id="articleEditorContainer" class="hidden bg-white rounded-3xl border border-slate-100 p-6 shadow-sm space-y-6 mt-6">
                                <div class="flex items-center justify-between border-b pb-4">
                                    <div>
                                        <h3 class="text-sm font-bold text-slate-900">Konten Artikel</h3>
                                        <p class="text-xs text-slate-400">Tulis isi artikel lengkap, masukkan infobox, gambar, dan tabel.</p>
                                    </div>
                                </div>

                                <!-- Slug Artikel -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">URL Slug Artikel</label>
                                    <div class="flex rounded-xl shadow-sm">
                                        <span class="inline-flex items-center px-4 rounded-l-xl border border-r-0 border-slate-200 bg-slate-50 text-slate-500 text-xs">
                                            <?= base_url('artikel') ?>/
                                        </span>
                                        <input type="text" name="slug" id="articleSlugInput" value="<?= esc(old('slug', $article['slug'] ?? '')) ?>" placeholder="slug-artikel-anda" 
                                            class="flex-1 min-w-0 block w-full px-4 py-3 bg-white border border-slate-200 rounded-r-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-xs font-medium text-slate-800">
                                    </div>
                                    <p class="mt-1 text-[10px] text-slate-400 italic">Kosongkan jika ingin dibuat otomatis dari judul.</p>
                                </div>

                                <!-- Tabs & Actions -->
                                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-2">
                                    <!-- Tabs (Edit vs Preview) -->
                                    <div class="flex gap-2">
                                        <button type="button" id="tabEdit" class="px-4 py-2 text-xs font-bold rounded-lg bg-indigo-50 text-indigo-600 transition-all">
                                            Edit Konten
                                        </button>
                                        <button type="button" id="tabPreview" class="px-4 py-2 text-xs font-bold rounded-lg text-slate-500 hover:bg-slate-50 transition-all">
                                            Preview Tampilan
                                        </button>
                                    </div>

                                    <!-- Tools helper -->
                                    <div class="flex items-center gap-2">
                                        <button type="button" id="insertInfoboxBtn" class="flex items-center gap-1 px-3 py-1.5 bg-slate-50 border border-slate-200 hover:bg-slate-100 hover:border-slate-300 rounded-lg text-[10px] font-bold text-slate-600 transition-all">
                                            <span class="material-symbols-outlined text-xs">analytics</span>
                                            <span>+ Infobox</span>
                                        </button>
                                        <button type="button" id="insertTableBtn" class="flex items-center gap-1 px-3 py-1.5 bg-slate-50 border border-slate-200 hover:bg-slate-100 hover:border-slate-300 rounded-lg text-[10px] font-bold text-slate-600 transition-all">
                                            <span class="material-symbols-outlined text-xs">table_chart</span>
                                            <span>+ Tabel</span>
                                        </button>
                                        
                                        <!-- File Upload for Inline Image -->
                                        <div class="relative">
                                            <button type="button" class="flex items-center gap-1 px-3 py-1.5 bg-slate-50 border border-slate-200 hover:bg-slate-100 hover:border-slate-300 rounded-lg text-[10px] font-bold text-slate-600 transition-all cursor-pointer">
                                                <span class="material-symbols-outlined text-xs">add_photo_alternate</span>
                                                <span>+ Upload Gambar</span>
                                            </button>
                                            <input type="file" id="inlineImageUploader" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*">
                                        </div>
                                    </div>
                                </div>

                                <!-- Editor Textarea -->
                                <div id="editorArea">
                                    <textarea name="article_body" id="articleBodyInput" rows="18" placeholder="Ketik konten artikel di sini...

Gunakan tool di atas untuk menyisipkan infobox, tabel, atau mengunggah gambar secara langsung.

Teks biasa akan otomatis terbagi menjadi paragraf." 
                                        class="w-full px-5 py-4 bg-slate-50/50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-800 font-mono resize-y min-h-[300px]"><?= esc(old('article_body', $article['body'] ?? '')) ?></textarea>
                                </div>

                                <!-- Preview Container -->
                                <div id="previewArea" class="hidden bg-slate-50 p-6 rounded-2xl border border-slate-200 min-h-[400px]">
                                    <!-- Styles will be applied to output container -->
                                    <div id="articleLivePreview" class="article-content max-w-none"></div>
                                </div>

                                <!-- Cheatsheet Collapsible -->
                                <div class="border border-slate-200 rounded-2xl overflow-hidden">
                                    <button type="button" onclick="document.getElementById('cheatsheetContent').classList.toggle('hidden')" class="w-full bg-slate-50 px-4 py-3 flex items-center justify-between text-xs font-bold text-slate-700 hover:bg-slate-100 transition-colors">
                                        <span class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-sm text-indigo-500">help</span>
                                            Panduan Penulisan & Syntax Artikel
                                        </span>
                                        <span class="material-symbols-outlined text-sm">expand_more</span>
                                    </button>
                                    <div id="cheatsheetContent" class="hidden p-4 text-xs text-slate-600 space-y-3 bg-white border-t border-slate-100">
                                        <p><strong>Paragraf & Format Dasar:</strong></p>
                                        <ul class="list-disc list-inside space-y-1 pl-2">
                                            <li>Tulis teks biasa seperti biasa. Baris kosong ganda akan memisahkan paragraf.</li>
                                            <li>Gunakan <code>**teks tebal**</code> untuk membuat tulisan <strong>tebal</strong>.</li>
                                            <li>Gunakan <code>_teks miring_</code> untuk membuat tulisan <em>miring</em>.</li>
                                            <li>Gunakan shift+enter untuk baris baru (line break) di dalam paragraf yang sama.</li>
                                        </ul>
                                        <p class="pt-2"><strong>Infobox (Gaya Wikipedia / Biodata / Detail Karakter):</strong></p>
                                        <pre class="bg-slate-50 p-2.5 rounded-lg overflow-x-auto text-[10px] text-slate-700 font-mono">infobox: [
  { "key": "Nama", "value": "Hanami Wickecklov" },
  { "key": "Kekuatan", "value": "Angin" }
]</pre>
                                        <p class="pt-2"><strong>Tabel Data:</strong></p>
                                        <pre class="bg-slate-50 p-2.5 rounded-lg overflow-x-auto text-[10px] text-slate-700 font-mono">table: {
  "headers": ["Nama", "Kekuatan"],
  "rows": [
    ["Hanami", "Angin"],
    ["Riku", "Api"]
  ]
}</pre>
                                        <p class="pt-2"><strong>Gambar (Layout & Caption):</strong></p>
                                        <p>Disarankan mengunggah via tombol <strong>+ Upload Gambar</strong> agar URL otomatis dihasilkan.</p>
                                        <pre class="bg-slate-50 p-2.5 rounded-lg overflow-x-auto text-[10px] text-slate-700 font-mono">image: {
  "url": "https://...",
  "layout": "center", 
  "caption": "Keterangan gambar"
}</pre>
                                        <p>Opsi <code>layout</code> yang tersedia: <code>center</code> (tengah), <code>left</code> (kiri, teks melingkari), <code>right</code> (kanan, teks melingkari), atau <code>full</code> (lebar penuh).</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between pt-8 border-t border-slate-100">
                        <a href="<?= base_url('creator/content') ?>" class="px-8 py-3 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-50 transition-all">
                            Batal
                        </a>
                        <div class="flex items-center gap-3">
                            <p class="text-[10px] text-slate-400 italic hidden" id="coverChangedHint">⚠ Sampul baru akan digunakan</p>
                            <button type="submit" class="px-10 py-3 bg-indigo-600 text-white rounded-xl font-bold text-sm hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200">
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </main>

    <script>
        const coverInput = document.getElementById('coverInput');
        const coverPreview = document.getElementById('coverPreview');
        const coverHint = document.getElementById('coverChangedHint');

        if (coverInput) {
            coverInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        coverPreview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                        coverPreview.classList.remove('border-dashed');
                        coverPreview.classList.add('border-indigo-400');
                        if (coverHint) coverHint.classList.remove('hidden');
                    }
                    reader.readAsDataURL(file);
                }
            });
        }

        // Paid/Free Toggle Logic
        const isPaidToggle = document.getElementById('isPaidToggle');
        const paidSettings = document.getElementById('paidSettings');
        const accessLabel = document.getElementById('accessLabel');

        if (isPaidToggle) {
            isPaidToggle.addEventListener('change', function() {
                if (this.checked) {
                    paidSettings.classList.remove('hidden');
                    accessLabel.textContent = 'Berbayar';
                    accessLabel.classList.add('text-indigo-600');
                } else {
                    paidSettings.classList.add('hidden');
                    accessLabel.textContent = 'Gratis';
                    accessLabel.classList.remove('text-indigo-600');
                }
            });
        }

        // Toggle elements based on Content Type select
        const contentTypeSelect = document.getElementById('contentTypeSelect');
        const galleryContainer = document.getElementById('galleryUploadContainer');
        const coverUploadContainer = document.getElementById('coverUploadContainer');
        const workConfigurationSection = document.getElementById('workConfigurationSection');
        const articleEditorContainer = document.getElementById('articleEditorContainer');

        function applyContentTypeLayout(type) {
            if (type === 'image') {
                galleryContainer.classList.remove('hidden');
                coverUploadContainer.classList.remove('hidden');
                workConfigurationSection.classList.remove('hidden');
                articleEditorContainer.classList.add('hidden');
            } else if (type === 'artikel') {
                galleryContainer.classList.add('hidden');
                coverUploadContainer.classList.add('hidden');
                workConfigurationSection.classList.add('hidden');
                articleEditorContainer.classList.remove('hidden');

                // Force free
                if (isPaidToggle && isPaidToggle.checked) {
                    isPaidToggle.checked = false;
                    isPaidToggle.dispatchEvent(new Event('change'));
                }
            } else {
                galleryContainer.classList.add('hidden');
                coverUploadContainer.classList.remove('hidden');
                workConfigurationSection.classList.remove('hidden');
                articleEditorContainer.classList.add('hidden');
            }
        }

        if (contentTypeSelect) {
            contentTypeSelect.addEventListener('change', function() {
                applyContentTypeLayout(this.value);
            });
            // Init layout on load
            applyContentTypeLayout(contentTypeSelect.value);
        }

        // Multiple Gallery Preview
        const galleryInput = document.getElementById('galleryInput');
        const galleryPreview = document.getElementById('galleryPreview');

        if (galleryInput) {
            galleryInput.addEventListener('change', function() {
                const files = Array.from(this.files);
                galleryPreview.innerHTML = ''; // Clear preview

                if (files.length === 0) {
                    galleryPreview.innerHTML = `
                        <div class="col-span-full flex flex-col items-center justify-center py-4 text-slate-400">
                            <span class="material-symbols-outlined text-3xl mb-1">add_photo_alternate</span>
                            <p class="text-[10px] font-medium">Klik untuk tambah gambar ke galeri</p>
                        </div>
                    `;
                    return;
                }

                files.forEach(file => {
                    const reader = new FileReader();
                    const div = document.createElement('div');
                    div.className = 'relative aspect-square rounded-2xl overflow-hidden bg-slate-200 border border-slate-100 shadow-sm';
                    
                    reader.onload = function(e) {
                        div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                    }
                    reader.readAsDataURL(file);
                    galleryPreview.appendChild(div);
                });
            });
        }

        // Artikel Editor JavaScript
        (function() {
            const titleInput = document.getElementById('titleInput');
            const slugInput  = document.getElementById('articleSlugInput');
            
            if (titleInput && slugInput) {
                titleInput.addEventListener('input', function() {
                    if (!slugInput.value) {
                        slugInput.placeholder = titleToSlug(this.value);
                    }
                });
            }

            function titleToSlug(title) {
                return title.toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '')
                    .substring(0, 80);
            }

            // Tab switcher
            const tabEdit = document.getElementById('tabEdit');
            const tabPreview = document.getElementById('tabPreview');
            const editorArea = document.getElementById('editorArea');
            const previewArea = document.getElementById('previewArea');
            const articleBodyInput = document.getElementById('articleBodyInput');
            const articleLivePreview = document.getElementById('articleLivePreview');

            if (tabEdit && tabPreview) {
                tabEdit.addEventListener('click', function() {
                    tabEdit.classList.add('bg-indigo-50', 'text-indigo-600');
                    tabPreview.classList.remove('bg-indigo-50', 'text-indigo-600');
                    tabPreview.classList.add('text-slate-500');
                    editorArea.classList.remove('hidden');
                    previewArea.classList.add('hidden');
                });

                tabPreview.addEventListener('click', function() {
                    tabPreview.classList.add('bg-indigo-50', 'text-indigo-600');
                    tabEdit.classList.remove('bg-indigo-50', 'text-indigo-600');
                    tabEdit.classList.add('text-slate-500');
                    editorArea.classList.add('hidden');
                    previewArea.classList.remove('hidden');

                    // Render live preview
                    articleLivePreview.innerHTML = renderArticleBodyJs(articleBodyInput.value);
                });
            }

            // Template insertions
            const insertInfoboxBtn = document.getElementById('insertInfoboxBtn');
            const insertTableBtn    = document.getElementById('insertTableBtn');
            const inlineImageUploader = document.getElementById('inlineImageUploader');

            function insertAtCursor(textarea, text) {
                const start = textarea.selectionStart;
                const end = textarea.selectionEnd;
                textarea.value = textarea.value.substring(0, start) + text + textarea.value.substring(end);
                textarea.focus();
                textarea.selectionStart = textarea.selectionEnd = start + text.length;
            }

            if (insertInfoboxBtn) {
                insertInfoboxBtn.addEventListener('click', () => {
                    const infoboxTpl = `\n\ninfobox: [
  { "key": "Nama", "value": "Hanami Wickecklov" },
  { "key": "Kekuatan", "value": "Cahaya putih + angin" },
  { "key": "Afiliasi", "value": "N.I.I.A." },
  { "key": "Asal", "value": "Rekayasa DNA ORDOM" }
]\n`;
                    insertAtCursor(articleBodyInput, infoboxTpl);
                });
            }

            if (insertTableBtn) {
                insertTableBtn.addEventListener('click', () => {
                    const tableTpl = `\n\ntable: {
  "headers": ["Kolom A", "Kolom B", "Kolom C"],
  "rows": [
    ["Data A1", "Data B1", "Data C1"],
    ["Data A2", "Data B2", "Data C2"]
  ]
}\n`;
                    insertAtCursor(articleBodyInput, tableTpl);
                });
            }

            if (inlineImageUploader) {
                inlineImageUploader.addEventListener('change', async function() {
                    const file = this.files[0];
                    if (!file) return;

                    const formData = new FormData();
                    formData.append('image', file);

                    const btn = this.closest('div').querySelector('button');
                    const btnSpan = btn.querySelector('span:last-child');
                    const originalText = btnSpan.textContent;
                    btnSpan.textContent = 'Uploading...';
                    btn.disabled = true;

                    try {
                        const res = await fetch('<?= base_url('creator/artikel/upload-image') ?>', {
                            method: 'POST',
                            body: formData,
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const data = await res.json();
                        if (data.success) {
                            const imageTpl = `\n\nimage: {
  "url": "${data.url}",
  "layout": "center",
  "caption": "Keterangan gambar..."
}\n`;
                            insertAtCursor(articleBodyInput, imageTpl);
                        } else {
                            alert('Gagal mengupload gambar: ' + (data.error || 'Terjadi kesalahan'));
                        }
                    } catch(e) {
                        alert('Terjadi kesalahan jaringan.');
                    } finally {
                        btnSpan.textContent = originalText;
                        btn.disabled = false;
                        this.value = '';
                    }
                });
            }

            // Live Preview Renderer in JS
            function renderArticleBodyJs(raw) {
                if (!raw.trim()) return '<p class="text-slate-400 italic">Belum ada konten...</p>';
                
                const blocks = splitBlocksJs(raw);
                let html = '';

                blocks.forEach(block => {
                    const trimmed = block.trim();
                    if (!trimmed) return;

                    if (trimmed.startsWith('infobox:')) {
                        html += renderInfoboxJs(trimmed);
                    } else if (trimmed.startsWith('table:')) {
                        html += renderTableJs(trimmed);
                    } else if (trimmed.startsWith('image:')) {
                        html += renderImageJs(trimmed);
                    } else {
                        html += renderParagraphJs(trimmed);
                    }
                });

                return html;
            }

            function splitBlocksJs(body) {
                const normalized = body.replace(/\r\n/g, '\n').replace(/\r/g, '\n');
                const lines = normalized.split('\n');
                const blocks = [];
                let buffer = '';
                let depth = 0;
                let inSpecial = false;

                lines.forEach(line => {
                    const trimmed = line.trimStart();
                    const startsSpecial = trimmed.startsWith('infobox:')
                        || trimmed.startsWith('table:')
                        || trimmed.startsWith('image:');

                    if (!inSpecial && startsSpecial) {
                        if (buffer.trim()) blocks.push(buffer);
                        buffer = '';
                        depth = 0;
                        inSpecial = true;
                    }

                    buffer += line + '\n';
                    depth += (line.match(/[\[\{]/g) || []).length;
                    depth -= (line.match(/[\]\}]/g) || []).length;

                    if (inSpecial && depth <= 0) {
                        if (buffer.trim()) blocks.push(buffer);
                        buffer = '';
                        depth = 0;
                        inSpecial = false;
                        return;
                    }

                    if (!inSpecial && line.trim() === '' && depth <= 0) {
                        if (buffer.trim()) blocks.push(buffer);
                        buffer = '';
                        depth = 0;
                    }
                });

                if (buffer.trim()) blocks.push(buffer);
                return blocks;
            }

            function renderInfoboxJs(block) {
                try {
                    const jsonStr = block.substring(8).trim();
                    const data = JSON.parse(jsonStr);
                    if (!Array.isArray(data)) throw new Error();
                    let rows = '';
                    data.forEach(row => {
                        if (row.key === undefined || row.value === undefined) return;
                        rows += `<tr>
                            <th class="article-infobox-key">${escapeHtml(row.key)}</th>
                            <td class="article-infobox-value">${escapeHtml(row.value)}</td>
                        </tr>`;
                    });
                    return `<div class="article-infobox">
                        <div class="article-infobox-header">
                            <span class="article-infobox-icon">📋</span>
                            <span>Info</span>
                        </div>
                        <table class="article-infobox-table">
                            <tbody>${rows}</tbody>
                        </table>
                    </div>`;
                } catch(e) {
                    return '<p class="article-parse-error">⚠️ Infobox tidak valid (Pastikan format JSON benar).</p>';
                }
            }

            function renderTableJs(block) {
                try {
                    const jsonStr = block.substring(6).trim();
                    const data = JSON.parse(jsonStr);
                    if (!data.rows) throw new Error();
                    
                    let thead = '';
                    if (data.headers) {
                        let ths = '';
                        data.headers.forEach(h => ths += `<th>${escapeHtml(h)}</th>`);
                        thead = `<thead><tr>${ths}</tr></thead>`;
                    }

                    let tbody = '<tbody>';
                    data.rows.forEach(row => {
                        let tds = '';
                        row.forEach(cell => tds += `<td>${escapeHtml(cell)}</td>`);
                        tbody += `<tr>${tds}</tr>`;
                    });
                    tbody += '</tbody>';

                    return `<div class="article-table-wrap"><table class="article-table">${thead}${tbody}</table></div>`;
                } catch(e) {
                    return '<p class="article-parse-error">⚠️ Tabel tidak valid (Pastikan format JSON benar).</p>';
                }
            }

            function renderImageJs(block) {
                try {
                    const jsonStr = block.substring(6).trim();
                    const data = JSON.parse(jsonStr);
                    if (!data.url) throw new Error();
                    const layout = data.layout || 'center';
                    const caption = data.caption ? `<figcaption class="article-img-caption">${escapeHtml(data.caption)}</figcaption>` : '';
                    return `<figure class="article-img article-img--${layout}">
                        <img src="${escapeHtml(data.url)}" alt="${escapeHtml(data.caption || '')}">
                        ${caption}
                    </figure>`;
                } catch(e) {
                    return '<p class="article-parse-error">⚠️ Gambar tidak valid.</p>';
                }
            }

            function renderParagraphJs(block) {
                let escaped = escapeHtml(block);
                escaped = escaped.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
                escaped = escaped.replace(/_(.+?)_/g, '<em>$1</em>');
                
                const paras = escaped.split(/\n\s*\n/);
                return paras.map(p => {
                    if (!p.trim()) return '';
                    const withBrs = p.replace(/\n/g, '<br>');
                    return `<p class="article-para">${withBrs}</p>`;
                }).join('');
            }

            function escapeHtml(str) {
                return str.replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }
        })();
    </script>
</body>
</html>
