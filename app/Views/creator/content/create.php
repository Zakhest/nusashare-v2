<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= $title ?? 'Tambah Karya - NusaShare' ?></title>
    
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
        /* Peer-checked trick untuk radio card UI */
        .type-card input:checked ~ .card-body { border-color: #4F46E5; background-color: rgba(238,242,255,0.5); }
        .type-card input:checked ~ .card-body .card-icon { background-color: #4F46E5; color: #fff; }
        .subtype-card input:checked ~ .card-body { border-color: #6366F1; background-color: rgba(238,242,255,0.4); }
        .subtype-card input:checked ~ .card-body .card-icon-text { color: #4F46E5; }
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
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="<?= base_url('creator/content') ?>" class="p-2 hover:bg-slate-50 rounded-xl transition-all text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Tambah Karya Baru</h2>
                    <p class="text-xs text-slate-400" id="headerSubtitle">Pilih format karya untuk memulai</p>
                </div>
            </div>
            
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-3 border-l pl-6 border-slate-100">
                    <div class="text-right">
                        <p class="text-xs font-bold text-slate-900"><?= $creatorProfile['display_name'] ?? $username ?></p>
                        <p class="text-[10px] text-slate-500 italic">Mode Kreator</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Body -->
        <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
            
            <div class="max-w-4xl mx-auto">
                <div class="mb-8">
                    <h1 class="text-2xl font-bold text-slate-900">Detail Karya</h1>
                    <p class="text-slate-500 text-sm mt-1">Isi informasi dasar tentang karyamu untuk mulai mempublikasikannya.</p>
                </div>

                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-2xl">
                        <div class="flex items-center gap-3 text-red-600 mb-2">
                            <span class="material-symbols-outlined text-sm">error</span>
                            <span class="text-xs font-bold uppercase tracking-tight">Terjadi Kesalahan</span>
                        </div>
                        <ul class="list-disc list-inside text-xs text-red-500 space-y-1">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('creator/content') ?>" method="POST" enctype="multipart/form-data" class="space-y-8" id="createForm">
                    <?= csrf_field() ?>

                    <!-- ══════════════════════════════════════════════
                         STEP 1 — Pilih Format Utama
                    ══════════════════════════════════════════════ -->
                    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-sm">
                        <h3 class="text-sm font-bold text-slate-900 mb-1">Format Utama Karya</h3>
                        <p class="text-xs text-slate-400 mb-5">Pilih kategori besar karyamu. Format yang dipilih akan menentukan kolom-kolom berikutnya.</p>

                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3" id="formatCards">

                            <!-- Gambar / Ilustrasi -->
                            <label class="type-card relative cursor-pointer group">
                                <input type="radio" name="work_format" value="image" class="sr-only" id="fmt_image">
                                <div class="card-body p-4 bg-white border-2 border-slate-100 rounded-2xl transition-all group-hover:border-indigo-200 text-center">
                                    <div class="card-icon w-10 h-10 bg-slate-100 text-slate-400 rounded-xl flex items-center justify-center mx-auto mb-2 transition-all">
                                        <span class="material-symbols-outlined">image</span>
                                    </div>
                                    <p class="text-xs font-bold text-slate-800">Gambar</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Ilustrasi / Foto</p>
                                </div>
                            </label>

                            <!-- Novel -->
                            <label class="type-card relative cursor-pointer group">
                                <input type="radio" name="work_format" value="novel" class="sr-only" id="fmt_novel" checked>
                                <div class="card-body p-4 bg-white border-2 border-slate-100 rounded-2xl transition-all group-hover:border-indigo-200 text-center">
                                    <div class="card-icon w-10 h-10 bg-slate-100 text-slate-400 rounded-xl flex items-center justify-center mx-auto mb-2 transition-all">
                                        <span class="material-symbols-outlined">menu_book</span>
                                    </div>
                                    <p class="text-xs font-bold text-slate-800">Novel</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Bab per bab</p>
                                </div>
                            </label>

                            <!-- Light Novel -->
                            <label class="type-card relative cursor-pointer group">
                                <input type="radio" name="work_format" value="light_novel" class="sr-only" id="fmt_light_novel">
                                <div class="card-body p-4 bg-white border-2 border-slate-100 rounded-2xl transition-all group-hover:border-indigo-200 text-center">
                                    <div class="card-icon w-10 h-10 bg-slate-100 text-slate-400 rounded-xl flex items-center justify-center mx-auto mb-2 transition-all">
                                        <span class="material-symbols-outlined">auto_stories</span>
                                    </div>
                                    <p class="text-xs font-bold text-slate-800">Light Novel</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Teks + Ilustrasi</p>
                                </div>
                            </label>

                            <!-- Comic -->
                            <label class="type-card relative cursor-pointer group">
                                <input type="radio" name="work_format" value="comic" class="sr-only" id="fmt_comic">
                                <div class="card-body p-4 bg-white border-2 border-slate-100 rounded-2xl transition-all group-hover:border-indigo-200 text-center">
                                    <div class="card-icon w-10 h-10 bg-slate-100 text-slate-400 rounded-xl flex items-center justify-center mx-auto mb-2 transition-all">
                                        <span class="material-symbols-outlined">collections_bookmark</span>
                                    </div>
                                    <p class="text-xs font-bold text-slate-800">Comic</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Panel gambar</p>
                                </div>
                            </label>

                            <!-- Teks / Puisi -->
                            <label class="type-card relative cursor-pointer group">
                                <input type="radio" name="work_format" value="text" class="sr-only" id="fmt_text">
                                <div class="card-body p-4 bg-white border-2 border-slate-100 rounded-2xl transition-all group-hover:border-indigo-200 text-center">
                                    <div class="card-icon w-10 h-10 bg-slate-100 text-slate-400 rounded-xl flex items-center justify-center mx-auto mb-2 transition-all">
                                        <span class="material-symbols-outlined">article</span>
                                    </div>
                                    <p class="text-xs font-bold text-slate-800">Teks</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Puisi / Essay</p>
                                </div>
                            </label>

                            <!-- Artikel -->
                            <label class="type-card relative cursor-pointer group">
                                <input type="radio" name="work_format" value="artikel" class="sr-only" id="fmt_artikel">
                                <div class="card-body p-4 bg-white border-2 border-slate-100 rounded-2xl transition-all group-hover:border-indigo-200 text-center">
                                    <div class="card-icon w-10 h-10 bg-slate-100 text-slate-400 rounded-xl flex items-center justify-center mx-auto mb-2 transition-all">
                                        <span class="material-symbols-outlined">newspaper</span>
                                    </div>
                                    <p class="text-xs font-bold text-slate-800">Artikel</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Blog / Infobox</p>
                                </div>
                            </label>
                        </div>

                        <!-- Hidden input yang sesungguhnya dikirim ke server -->
                        <input type="hidden" name="content_type" id="contentTypeHidden" value="novel">

                        <!-- Info badge tipe yang aktif -->
                        <div class="mt-4 flex items-center gap-2">
                            <span class="material-symbols-outlined text-indigo-500 text-sm">info</span>
                            <p class="text-xs text-slate-500">Tipe dipilih: <strong class="text-indigo-600" id="selectedTypeLabel">Novel</strong></p>
                        </div>
                    </div>

                    <!-- ══════════════════════════════════════════════
                         GRID: Cover + Detail
                    ══════════════════════════════════════════════ -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Cover Upload -->
                        <div class="md:col-span-1" id="coverUploadContainer">
                            <label class="block text-sm font-bold text-slate-700 mb-4">Sampul Karya</label>
                            <div class="relative group">
                                <div id="coverPreview" class="w-full aspect-[3/4] bg-slate-100 rounded-3xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center overflow-hidden transition-all group-hover:border-indigo-300">
                                    <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">image</span>
                                    <p class="text-[10px] text-slate-400 font-medium px-6 text-center">Klik untuk upload sampul (JPG, PNG, WebP)</p>
                                </div>
                                <input type="file" name="cover" id="coverInput" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*">
                            </div>
                            <p class="mt-3 text-[10px] text-slate-400 italic">Rekomendasi rasio 3:4, maks 2MB.</p>
                        </div>

                        <!-- Form Fields -->
                        <div class="md:col-span-2 space-y-6">

                            <!-- Gallery Upload — hanya untuk format Gambar -->
                            <div id="galleryUploadContainer" class="hidden">
                                <label class="block text-sm font-bold text-slate-700 mb-4">
                                    Galeri Gambar
                                    <span class="ml-2 text-xs font-normal text-slate-400">(Upload satu atau lebih gambar)</span>
                                </label>
                                <div class="relative group">
                                    <div id="galleryPreview" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 min-h-[120px] p-4 bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200 transition-all group-hover:border-indigo-300">
                                        <div class="col-span-full flex flex-col items-center justify-center py-4 text-slate-400">
                                            <span class="material-symbols-outlined text-3xl mb-1">add_photo_alternate</span>
                                            <p class="text-[10px] font-medium">Klik untuk upload banyak gambar</p>
                                        </div>
                                    </div>
                                    <input type="file" name="gallery_images[]" id="galleryInput" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*" multiple>
                                </div>
                                <p class="mt-3 text-[10px] text-slate-400 italic">Urutan sesuai pilihan. Bisa dipilih lebih dari satu.</p>
                            </div>

                            <!-- Judul -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Judul Karya</label>
                                <input type="text" name="title" id="titleInput" value="<?= old('title') ?>" placeholder="Masukkan judul yang menarik..." 
                                    class="w-full px-5 py-4 bg-white border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-900 font-medium">
                            </div>

                            <!-- Genre -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Genre</label>
                                <input type="text" name="genre" value="<?= old('genre') ?>" placeholder="Contoh: Action, Romance, Fantasy..." 
                                    class="w-full px-5 py-4 bg-white border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-900 font-medium">
                            </div>

                            <!-- Status Publikasi -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Status Publikasi</label>
                                <select name="status" class="w-full px-5 py-4 bg-white border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-900 font-medium appearance-none cursor-pointer">
                                    <option value="draft" <?= old('status') == 'draft' ? 'selected' : '' ?>>Simpan Draft</option>
                                    <option value="published" <?= old('status') == 'published' ? 'selected' : '' ?>>Langsung Terbit</option>
                                </select>
                            </div>

                            <!-- Deskripsi -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Sinopsis / Deskripsi</label>
                                <textarea name="description" rows="5" placeholder="Ceritakan sedikit tentang karya ini..." 
                                    class="w-full px-5 py-4 bg-white border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-900 font-medium resize-none"><?= old('description') ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- ══════════════════════════════════════════════
                         Artikel Content Editor (hanya muncul jika tipe Artikel)
                    ══════════════════════════════════════════════ -->
                    <div id="articleEditorContainer" class="hidden bg-white rounded-3xl border border-slate-100 p-6 shadow-sm space-y-6">
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
                                <input type="text" name="slug" id="articleSlugInput" value="<?= old('slug') ?>" placeholder="slug-artikel-anda" 
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
                                class="w-full px-5 py-4 bg-slate-50/50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-800 font-mono resize-y min-h-[300px]"><?= old('article_body') ?></textarea>
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

                    <!-- ══════════════════════════════════════════════
                         Pengaturan Akses & Status (hanya tipe berbab)
                    ══════════════════════════════════════════════ -->
                    <div id="chapterSettingsContainer" class="p-6 bg-slate-50 rounded-3xl border border-slate-100 space-y-4">
                        <h3 class="text-sm font-bold text-slate-900">Pengaturan Bab</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-2 uppercase tracking-wider">Status Cerita</label>
                                <div class="flex items-center gap-4">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="work_status" value="ongoing" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500" checked>
                                        <span class="text-xs text-slate-700">Ongoing</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="work_status" value="ended" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-xs text-slate-700">Ended</span>
                                    </label>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-2 uppercase tracking-wider">Batasi Akses</label>
                                <div class="flex items-center gap-4">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="access_type" value="full" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500" checked>
                                        <span class="text-xs text-slate-700">Seluruh Karya</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="access_type" value="chapter" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-xs text-slate-700">Bab per Bab</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pengaturan default untuk tipe Gambar (hidden input agar validasi lolos) -->
                    <input type="hidden" name="work_status_img" value="ended" id="workStatusImgHidden">
                    <input type="hidden" name="access_type_img" value="full" id="accessTypeImgHidden">

                    <!-- ══════════════════════════════════════════════
                         Monetisasi
                    ══════════════════════════════════════════════ -->
                    <div id="monetizationSection" class="p-6 bg-indigo-50/50 rounded-3xl border border-indigo-100 space-y-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Izinkan Pembaca Mengunduh PDF/ZIP</h4>
                                <p class="text-[10px] text-slate-500 mt-0.5">Aktifkan agar pembaca bisa mengunduh karya melalui PDF atau ZIP.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="allow_downloads" value="0">
                                <input type="checkbox" name="allow_downloads" value="1" class="sr-only peer" <?= old('allow_downloads', '1') == '1' ? 'checked' : '' ?>>
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                                <span class="ml-3 text-xs font-bold text-slate-700 peer-checked:text-emerald-600">Diizinkan</span>
                            </label>
                        </div>

                        <div class="border-t border-indigo-100/50"></div>

                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Monetisasi</h4>
                                <p class="text-[10px] text-slate-500 mt-0.5">Tentukan apakah karya ini gratis atau berbayar.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_paid" id="isPaidToggle" class="sr-only peer" <?= old('is_paid') ? 'checked' : '' ?>>
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                <span class="ml-3 text-xs font-bold text-slate-700 peer-checked:text-indigo-600" id="accessLabel">Gratis</span>
                            </label>
                        </div>

                        <div id="paidSettings" class="<?= old('is_paid') ? '' : 'hidden' ?> space-y-5 pt-6 border-t border-indigo-100/50">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <!-- Harga Akses (timer unlock) -->
                                <div id="priceAccessContainer">
                                    <label class="block text-[11px] font-bold text-slate-700 mb-2 uppercase tracking-wider">
                                        Harga Akses Sementara (CC)
                                        <span class="block text-slate-400 font-normal normal-case tracking-normal mt-0.5">Dibayar per sesi / timer</span>
                                    </label>
                                    <div class="relative">
                                        <input type="number" name="price" value="<?= old('price', 0) ?>" min="0"
                                            class="w-full pl-5 pr-12 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-bold text-slate-900">
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-400">CC</span>
                                    </div>
                                </div>

                                <!-- Harga Beli Permanen (download) -->
                                <div id="purchasePriceContainer">
                                    <label class="block text-[11px] font-bold text-slate-700 mb-2 uppercase tracking-wider">
                                        Harga Beli Permanen (CC)
                                        <span class="block text-slate-400 font-normal normal-case tracking-normal mt-0.5">Untuk download / miliki selamanya</span>
                                    </label>
                                    <div class="relative">
                                        <input type="number" name="purchase_price" value="<?= old('purchase_price', 0) ?>" min="0"
                                            class="w-full pl-5 pr-12 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-bold text-slate-900">
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-400">CC</span>
                                    </div>
                                </div>

                                <!-- Durasi Timer -->
                                <div id="timerContainer">
                                    <label class="block text-[11px] font-bold text-slate-700 mb-2 uppercase tracking-wider">
                                        Durasi Timer (Detik)
                                        <span class="block text-slate-400 font-normal normal-case tracking-normal mt-0.5">Setelah timer habis, akses dikunci lagi</span>
                                    </label>
                                    <div class="relative">
                                        <input type="number" name="timer_duration" value="<?= old('timer_duration', 30) ?>" min="10"
                                            class="w-full pl-5 pr-12 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-bold text-slate-900">
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-400">SEC</span>
                                    </div>
                                </div>

                                <!-- Watermark -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 mb-2 uppercase tracking-wider">
                                        Teks Watermark
                                        <span class="block text-slate-400 font-normal normal-case tracking-normal mt-0.5">Muncul di preview berbayar</span>
                                    </label>
                                    <input type="text" name="watermark_text" value="<?= old('watermark_text', $creatorProfile['display_name'] ?? $username) ?>" placeholder="Contoh: Milik <?= $creatorProfile['display_name'] ?? $username ?>"
                                        class="w-full px-5 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-900 font-medium">
                                </div>
                            </div>

                            <!-- Info monetisasi sesuai tipe -->
                            <div id="monetizeInfoImage" class="hidden p-3 bg-blue-50 border border-blue-100 rounded-xl">
                                <div class="flex gap-2 text-blue-700">
                                    <span class="material-symbols-outlined text-sm mt-0.5">info</span>
                                    <p class="text-xs">Untuk karya <strong>Gambar</strong>: gunakan <em>Harga Beli Permanen</em> untuk fitur download. Timer & harga akses sementara berlaku untuk preview gambar.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Progress Overlay -->
                    <div id="uploadProgressContainer" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center">
                        <div class="bg-white p-8 rounded-3xl shadow-2xl max-w-sm w-full mx-4 relative overflow-hidden">
                            <div class="absolute top-0 left-0 w-full h-1 bg-slate-100">
                                <div id="uploadProgressBar" class="h-full bg-indigo-600 w-0 transition-all duration-300 ease-out"></div>
                            </div>
                            <div class="text-center">
                                <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4 animate-pulse">
                                    <span class="material-symbols-outlined text-3xl">cloud_upload</span>
                                </div>
                                <h3 class="font-bold text-slate-900 text-lg mb-1">Mengunggah Karya...</h3>
                                <p class="text-xs text-slate-500 mb-6">Mohon tunggu sebentar, jangan tutup halaman ini.</p>
                                <div class="flex items-center justify-between text-xs font-bold text-slate-700 mb-2">
                                    <span>Progress</span>
                                    <span id="uploadProgressText" class="text-indigo-600">0%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-4 pt-8 border-t border-slate-100 relative z-10">
                        <a href="<?= base_url('creator/content') ?>" class="px-8 py-3 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-50 transition-all">
                            Batal
                        </a>
                        <button type="submit" id="submitBtn" class="px-10 py-3 bg-indigo-600 text-white rounded-xl font-bold text-sm hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">save</span>
                            <span>Simpan Karya</span>
                            <span class="material-symbols-outlined text-sm hidden animate-spin" id="submitSpinner">sync</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </main>

    <script>
    // ─────────────────────────────────────────────────────────────
    // Mapping format → content_type & label
    // ─────────────────────────────────────────────────────────────
    const FORMAT_META = {
        image:       { label: 'Gambar / Ilustrasi', isImage: true,  isChapter: false },
        novel:       { label: 'Novel',               isImage: false, isChapter: true  },
        light_novel: { label: 'Light Novel',         isImage: false, isChapter: true  },
        comic:       { label: 'Comic',               isImage: false, isChapter: true  },
        text:        { label: 'Teks / Puisi',        isImage: false, isChapter: true  },
        artikel:     { label: 'Artikel',             isImage: false, isChapter: false, isArtikel: true }
    };

    const contentTypeHidden     = document.getElementById('contentTypeHidden');
    const selectedTypeLabel     = document.getElementById('selectedTypeLabel');
    const headerSubtitle        = document.getElementById('headerSubtitle');
    const galleryContainer      = document.getElementById('galleryUploadContainer');
    const chapterSettings       = document.getElementById('chapterSettingsContainer');
    const monetizeInfoImage     = document.getElementById('monetizeInfoImage');
    const timerContainer        = document.getElementById('timerContainer');
    const priceAccessContainer  = document.getElementById('priceAccessContainer');

    const coverUploadContainer   = document.getElementById('coverUploadContainer');
    const monetizationSection    = document.getElementById('monetizationSection');
    const articleEditorContainer = document.getElementById('articleEditorContainer');

    // Sinkronisasi nama field work_status & access_type saat format berubah
    const workStatusRadios  = document.querySelectorAll('input[name="work_status"]');
    const accessTypeRadios  = document.querySelectorAll('input[name="access_type"]');

    function applyFormat(format) {
        const meta = FORMAT_META[format] || FORMAT_META['novel'];

        // 1. Update hidden content_type
        contentTypeHidden.value = format;

        // 2. Update label
        selectedTypeLabel.textContent = meta.label;
        headerSubtitle.textContent = 'Format: ' + meta.label;

        // Reset default layout
        coverUploadContainer.classList.remove('hidden');
        monetizationSection.classList.remove('hidden');
        articleEditorContainer.classList.add('hidden');

        // 3. Toggle gallery vs chapter settings vs article settings
        if (meta.isImage) {
            galleryContainer.classList.remove('hidden');
            chapterSettings.classList.add('hidden');
            // Disable chapter radios so they don't submit
            workStatusRadios.forEach(r => r.disabled = true);
            accessTypeRadios.forEach(r => r.disabled = true);
            // Info monetisasi khusus gambar
            monetizeInfoImage.classList.remove('hidden');
        } else if (meta.isArtikel) {
            galleryContainer.classList.add('hidden');
            chapterSettings.classList.add('hidden');
            coverUploadContainer.classList.add('hidden');
            monetizationSection.classList.add('hidden');
            articleEditorContainer.classList.remove('hidden');
            workStatusRadios.forEach(r => r.disabled = true);
            accessTypeRadios.forEach(r => r.disabled = true);
            
            // Force free
            const isPaidToggle = document.getElementById('isPaidToggle');
            if (isPaidToggle && isPaidToggle.checked) {
                isPaidToggle.checked = false;
                isPaidToggle.dispatchEvent(new Event('change'));
            }
        } else {
            galleryContainer.classList.add('hidden');
            chapterSettings.classList.remove('hidden');
            workStatusRadios.forEach(r => r.disabled = false);
            accessTypeRadios.forEach(r => r.disabled = false);
            monetizeInfoImage.classList.add('hidden');
        }
    }

    // Pasang event listener pada semua format card
    document.querySelectorAll('input[name="work_format"]').forEach(input => {
        input.addEventListener('change', function() {
            applyFormat(this.value);
        });
    });

    // Init saat halaman load
    const initFormat = document.querySelector('input[name="work_format"]:checked');
    if (initFormat) applyFormat(initFormat.value);

    // ─────────────────────────────────────────────────────────────
    // Cover preview
    // ─────────────────────────────────────────────────────────────
    document.getElementById('coverInput').addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = e => {
            const preview = document.getElementById('coverPreview');
            preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
            preview.classList.remove('border-dashed');
        };
        reader.readAsDataURL(file);
    });

    // ─────────────────────────────────────────────────────────────
    // Gallery preview (multi-file, appendable)
    // ─────────────────────────────────────────────────────────────
    const galleryInput    = document.getElementById('galleryInput');
    const galleryPreview  = document.getElementById('galleryPreview');
    const galleryDT       = new DataTransfer();

    galleryInput.addEventListener('change', function() {
        Array.from(this.files).forEach(f => galleryDT.items.add(f));
        this.files = galleryDT.files;
        renderGalleryPreview(Array.from(this.files));
    });

    function renderGalleryPreview(files) {
        galleryPreview.innerHTML = '';
        if (!files.length) {
            galleryPreview.innerHTML = `<div class="col-span-full flex flex-col items-center justify-center py-4 text-slate-400">
                <span class="material-symbols-outlined text-3xl mb-1">add_photo_alternate</span>
                <p class="text-[10px] font-medium">Klik untuk upload banyak gambar</p></div>`;
            return;
        }
        files.forEach((file, i) => {
            const reader = new FileReader();
            const div = document.createElement('div');
            div.className = 'relative aspect-square rounded-2xl overflow-hidden bg-slate-200 border border-slate-100 shadow-sm';
            reader.onload = e => {
                div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">
                    <div class="absolute top-2 left-2 bg-black/50 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">#${i+1}</div>
                    <button type="button" data-idx="${i}" class="remove-img absolute top-2 right-2 bg-red-500 text-white rounded-full w-5 h-5 text-xs font-bold flex items-center justify-center hover:bg-red-600 transition">✕</button>`;
                div.querySelector('.remove-img').addEventListener('click', function() {
                    const idx = parseInt(this.dataset.idx);
                    galleryDT.items.remove(idx);
                    galleryInput.files = galleryDT.files;
                    renderGalleryPreview(Array.from(galleryInput.files));
                });
            };
            reader.readAsDataURL(file);
            galleryPreview.appendChild(div);
        });
        // Tombol tambah lebih
        const addDiv = document.createElement('div');
        addDiv.className = 'relative aspect-square rounded-2xl border-2 border-dashed border-slate-300 flex flex-col items-center justify-center text-slate-400 bg-slate-50 hover:bg-slate-100 transition-colors pointer-events-none';
        addDiv.innerHTML = `<span class="material-symbols-outlined text-2xl mb-1">add</span><span class="text-[10px] font-bold uppercase">Tambah</span>`;
        galleryPreview.appendChild(addDiv);
    }

    // ─────────────────────────────────────────────────────────────
    // Paid/Free toggle
    // ─────────────────────────────────────────────────────────────
    const isPaidToggle = document.getElementById('isPaidToggle');
    const paidSettings = document.getElementById('paidSettings');
    const accessLabel  = document.getElementById('accessLabel');

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

    // ─────────────────────────────────────────────────────────────
    // Validasi & inject hidden fields sebelum submit
    // ─────────────────────────────────────────────────────────────
    function injectImageDefaults(formData) {
        const fmt = document.querySelector('input[name="work_format"]:checked')?.value;
        if (FORMAT_META[fmt]?.isImage) {
            formData.set('work_status', 'ended');
            formData.set('access_type', 'full');
        } else if (FORMAT_META[fmt]?.isArtikel) {
            formData.set('work_status', 'ended');
            formData.set('access_type', 'full');
            formData.set('is_paid', '0');
            formData.set('price', '0');
            formData.set('purchase_price', '0');
            formData.set('allow_downloads', '0');
            formData.set('timer_duration', '0');
        }
        return formData;
    }

    // ─────────────────────────────────────────────────────────────
    // Artikel Editor JavaScript
    // ─────────────────────────────────────────────────────────────
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

    // ─────────────────────────────────────────────────────────────
    // AJAX Form Submission
    // ─────────────────────────────────────────────────────────────
    const createForm      = document.getElementById('createForm');
    const submitBtn       = document.getElementById('submitBtn');
    const submitSpinner   = document.getElementById('submitSpinner');
    const progressContainer = document.getElementById('uploadProgressContainer');
    const progressBar     = document.getElementById('uploadProgressBar');
    const progressText    = document.getElementById('uploadProgressText');

    createForm.addEventListener('submit', function(e) {
        e.preventDefault();

        submitBtn.disabled = true;
        submitSpinner.classList.remove('hidden');
        progressContainer.classList.remove('hidden');
        progressBar.style.width = '0%';
        progressText.textContent = '0%';

        let formData = new FormData(this);
        formData = injectImageDefaults(formData);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', this.action, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.upload.addEventListener('progress', function(ev) {
            if (ev.lengthComputable) {
                const pct = Math.round((ev.loaded / ev.total) * 100);
                progressBar.style.width = pct + '%';
                progressText.textContent = pct + '%';
            }
        });

        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                try {
                    const res = JSON.parse(xhr.responseText);
                    if (res.success) {
                        progressText.textContent = 'Selesai!';
                        progressBar.style.width = '100%';
                        setTimeout(() => {
                            window.location.href = res.redirect || '<?= base_url('creator/content') ?>';
                        }, 500);
                    } else {
                        let msg = 'Gagal menyimpan karya:\n\n';
                        if (res.errors) {
                            for (const [, m] of Object.entries(res.errors)) msg += `- ${m}\n`;
                        } else {
                            msg += res.message || 'Terjadi kesalahan tidak diketahui.';
                        }
                        alert(msg);
                        resetUI();
                    }
                } catch {
                    alert('Terjadi kesalahan format respon dari server.');
                    resetUI();
                }
            } else {
                alert('Kesalahan jaringan/server. (Status: ' + xhr.status + ')');
                resetUI();
            }
        };

        xhr.onerror = () => { alert('Terjadi kesalahan jaringan.'); resetUI(); };
        xhr.send(formData);
    });

    function resetUI() {
        submitBtn.disabled = false;
        submitSpinner.classList.add('hidden');
        submitSpinner.classList.remove('animate-spin');
        progressContainer.classList.add('hidden');
    }
    </script>
</body>
</html>
