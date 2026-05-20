<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">
    <link rel="icon" href="<?= base_url('assets/icon/logonus.png') ?>" type="image/x-icon">

    <?php
    $contentType = $work['content_type'] ?? 'novel';
    $isComic     = ($contentType === 'comic');
    $isLN        = ($contentType === 'light_novel');
    $hasText     = !$isComic;  // novel & light_novel have text editor
    $hasImages   = ($isComic || $isLN); // comic & light_novel have image upload
    ?>

    <?php if ($hasText): ?>
    <!-- Quill.js -->
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <?php endif; ?>

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F8FAFC; }

        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #E2E8F0; border-radius: 10px; }

        <?php if ($hasText): ?>
        /* Quill toolbar overrides */
        .ql-toolbar.ql-snow {
            border: none !important;
            border-bottom: 1px solid #E2E8F0 !important;
            background: #FAFAFA;
            padding: 10px 16px;
            font-family: 'Inter', sans-serif;
        }
        .ql-container.ql-snow {
            border: none !important;
            font-family: 'Lora', serif;
            font-size: 1.05rem;
            line-height: 1.9;
            color: #1E293B;
        }
        .ql-editor {
            min-height: 400px;
            padding: 2rem 0;
        }
        .ql-editor.ql-blank::before {
            color: #CBD5E1;
            font-style: italic;
            font-family: 'Lora', serif;
        }
        /* Toolbar button colors */
        .ql-toolbar .ql-stroke { stroke: #64748B; }
        .ql-toolbar .ql-fill { fill: #64748B; }
        .ql-toolbar button:hover .ql-stroke,
        .ql-toolbar .ql-active .ql-stroke { stroke: #6366F1; }
        .ql-toolbar button:hover .ql-fill,
        .ql-toolbar .ql-active .ql-fill { fill: #6366F1; }
        .ql-toolbar .ql-picker-label { color: #64748B; }
        .ql-toolbar .ql-picker-label:hover { color: #6366F1; }
        <?php endif; ?>

        /* Image upload area */
        .chapter-img-card {
            position: relative;
            aspect-ratio: 3/4;
            border-radius: 1rem;
            overflow: hidden;
            background: #F1F5F9;
            border: 1.5px solid #E2E8F0;
            box-shadow: 0 1px 4px 0 rgba(0,0,0,.04);
        }
        .chapter-img-card img {
            width: 100%; height: 100%;
            object-fit: cover;
        }
        .chapter-img-card .badge {
            position: absolute; top: 6px; left: 6px;
            background: rgba(0,0,0,.55); color:#fff;
            font-size: 10px; font-weight: 700;
            padding: 2px 8px; border-radius: 999px;
            z-index: 2;
        }
        .chapter-img-card .remove-btn {
            position: absolute; top: 6px; right: 6px;
            background: rgba(239,68,68,.85); color:#fff;
            width: 22px; height: 22px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; z-index: 2;
            font-size: 14px; line-height: 1;
        }

        /* ── Upload Progress Overlay ── */
        #uploadOverlay {
            position: fixed; inset: 0; z-index: 9999;
            background: rgba(15,23,42,0.65);
            backdrop-filter: blur(6px);
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 1.5rem;
            opacity: 0; pointer-events: none;
            transition: opacity 0.25s ease;
        }
        #uploadOverlay.visible {
            opacity: 1; pointer-events: all;
        }
        #uploadOverlay .upload-card {
            background: #fff; border-radius: 1.5rem;
            padding: 2.5rem 3rem; text-align: center;
            width: min(420px, 90vw);
            box-shadow: 0 25px 60px rgba(0,0,0,.25);
        }
        #uploadOverlay .spinner {
            width: 56px; height: 56px; border-radius: 50%;
            background: linear-gradient(135deg, #6366F1, #22D3EE);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.25rem;
            animation: pulse-ring 1.4s ease-in-out infinite;
        }
        @keyframes pulse-ring {
            0%,100% { box-shadow: 0 0 0 0 rgba(99,102,241,.5); }
            50%      { box-shadow: 0 0 0 14px rgba(99,102,241,.0); }
        }
        #uploadOverlay h3 {
            font-size: 1.05rem; font-weight: 700;
            color: #0F172A; margin-bottom: .35rem;
        }
        #uploadOverlay .sub {
            font-size: .75rem; color: #64748B; margin-bottom: 1.25rem;
        }
        .prog-track {
            width: 100%; height: 8px;
            background: #F1F5F9; border-radius: 99px; overflow: hidden;
        }
        .prog-bar {
            height: 100%; width: 0%;
            background: linear-gradient(90deg, #6366F1, #22D3EE);
            border-radius: 99px;
            transition: width .3s ease;
        }
        #uploadPct {
            font-size: .7rem; font-weight: 700; color: #6366F1;
            margin-top: .5rem; letter-spacing: .02em;
        }

        /* ── Toast Notification ── */
        #uploadToast {
            position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 10000;
            min-width: 300px; max-width: 420px;
            padding: 1rem 1.25rem;
            border-radius: 1rem;
            display: flex; align-items: flex-start; gap: .75rem;
            font-size: .825rem; line-height: 1.5;
            box-shadow: 0 10px 30px rgba(0,0,0,.15);
            transform: translateY(120%);
            transition: transform .35s cubic-bezier(.34,1.56,.64,1);
        }
        #uploadToast.show { transform: translateY(0); }
        #uploadToast.toast-error   { background:#FFF0F0; border: 1px solid #FECACA; color:#991B1B; }
        #uploadToast.toast-warning { background:#FFFBEB; border: 1px solid #FDE68A; color:#92400E; }
        #uploadToast.toast-ok      { background:#F0FDF4; border: 1px solid #BBF7D0; color:#166534; }
        #uploadToast .toast-icon { font-size: 1.25rem; flex-shrink: 0; margin-top: .05rem; }
        #uploadToast .toast-close {
            margin-left: auto; cursor: pointer;
            font-size: 1rem; opacity: .5; flex-shrink: 0;
            background: none; border: none; padding: 0; color: inherit;
        }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <!-- ── Upload Progress Overlay ── -->
    <div id="uploadOverlay">
        <div class="upload-card">
            <div class="spinner">
                <span class="material-symbols-outlined text-white text-2xl">cloud_upload</span>
            </div>
            <h3 id="overlayTitle">Mengunggah gambar...</h3>
            <p class="sub" id="overlaySubtitle">Mohon jangan tutup halaman ini</p>
            <div class="prog-track">
                <div class="prog-bar" id="progBar"></div>
            </div>
            <p id="uploadPct">0%</p>
        </div>
    </div>

    <!-- ── Toast Notification ── -->
    <div id="uploadToast">
        <span class="toast-icon" id="toastIcon">⚠️</span>
        <span id="toastMsg">Pesan notifikasi</span>
        <button class="toast-close" onclick="hideToast()">✕</button>
    </div>

    <?= view('creator/_sidebar', ['activePage' => 'content', 'user' => $user, 'creatorProfile' => $creatorProfile, 'username' => $username]) ?>

    <main class="flex-1 flex flex-col h-full overflow-hidden bg-slate-50">

        <!-- Top Nav -->
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-4">
                <a href="<?= base_url('creator/content/' . $work['id'] . '/chapters') ?>" class="p-2 hover:bg-slate-50 rounded-xl transition-all text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <div>
                    <h2 class="text-lg font-bold text-slate-900"><?= $mode === 'edit' ? 'Edit Bab' : 'Tulis Bab Baru' ?></h2>
                    <p class="text-[10px] text-slate-400"><?= esc($work['title']) ?>
                        <span class="ml-2 px-2 py-0.5 rounded-full text-[9px] font-bold uppercase
                            <?= $isComic ? 'bg-purple-100 text-purple-600' : ($isLN ? 'bg-teal-100 text-teal-600' : 'bg-indigo-100 text-indigo-600') ?>">
                            <?= $isComic ? 'Comic' : ($isLN ? 'Light Novel' : 'Novel') ?>
                        </span>
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <?php if ($hasText): ?>
                <div class="text-[10px] text-slate-400 text-right hidden sm:block">
                    <span id="wordCount">0 kata</span> &nbsp;•&nbsp; <span id="readTime">~0 mnt baca</span>
                </div>
                <?php endif; ?>
                <button type="button" id="btnDraft" class="px-5 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-50 transition-all">
                    Simpan Draft
                </button>
                <button type="button" id="btnPublish" class="px-6 py-2 bg-indigo-600 text-white rounded-xl font-bold text-sm hover:bg-indigo-700 transition-all shadow-md shadow-indigo-200">
                    Terbitkan
                </button>
            </div>
        </header>

        <!-- Error flash -->
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="mx-8 mt-4 p-4 bg-red-50 border border-red-100 rounded-2xl">
                <ul class="list-disc list-inside text-xs text-red-500 space-y-1">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php
        $formAction = $mode === 'edit'
            ? base_url('creator/content/' . $work['id'] . '/chapters/' . $chapter['id'] . '/update')
            : base_url('creator/content/' . $work['id'] . '/chapters');
        ?>

        <!-- Use multipart only when image upload is needed -->
        <form id="chapterForm" method="POST" action="<?= $formAction ?>"
              <?= $hasImages ? 'enctype="multipart/form-data"' : '' ?>>
            <?= csrf_field() ?>
            <input type="hidden" name="status"  id="statusField" value="<?= old('status', $chapter['status'] ?? 'draft') ?>">
            <?php if ($hasText): ?>
            <!-- Hidden field that receives Quill HTML output -->
            <input type="hidden" name="body" id="bodyField">
            <?php endif; ?>

            <!-- Scrollable content area -->
            <div class="flex-1 overflow-y-auto custom-scrollbar" style="height: calc(100vh - 140px);">
                <div class="max-w-3xl mx-auto px-8 pt-8 pb-12">

                    <!-- Chapter title input -->
                    <input
                        type="text"
                        name="title"
                        id="chapterTitle"
                        placeholder="Judul bab..."
                        value="<?= esc(old('title', $chapter['title'] ?? '')) ?>"
                        class="w-full text-3xl font-bold text-slate-900 bg-transparent border-none focus:outline-none placeholder-slate-200 pb-4 border-b border-slate-100 block"
                        style="font-family:'Inter',sans-serif;"
                    >

                    <!-- Chapter Settings -->
                    <div class="mt-6 flex flex-wrap gap-4 items-end bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Akses Bab</label>
                            <div class="flex items-center gap-4">
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="radio" name="is_locked" value="0" <?= (old('is_locked', $chapter['is_locked'] ?? 0) == 0) ? 'checked' : '' ?> class="w-4 h-4 text-indigo-600 border-slate-200 focus:ring-indigo-500">
                                    <span class="text-sm font-medium text-slate-600 group-hover:text-slate-900 transition-all">Gratis</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="radio" name="is_locked" value="1" <?= (old('is_locked', $chapter['is_locked'] ?? 0) == 1) ? 'checked' : '' ?> class="w-4 h-4 text-indigo-600 border-slate-200 focus:ring-indigo-500">
                                    <span class="text-sm font-medium text-slate-600 group-hover:text-slate-900 transition-all">Terkunci (CC)</span>
                                </label>
                            </div>
                        </div>
                        <div class="w-32" id="priceContainer" style="<?= (old('is_locked', $chapter['is_locked'] ?? 0) == 1) ? '' : 'display:none;' ?>">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Harga (CC)</label>
                            <div class="relative">
                                <input
                                    type="number"
                                    name="price"
                                    placeholder="0"
                                    value="<?= old('price', $chapter['price'] ?? 0) ?>"
                                    class="w-full pl-3 pr-8 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
                                >
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-slate-400">CC</span>
                            </div>
                        </div>
                    </div>

                    <?php if ($hasText): ?>
                    <!-- ── TEXT EDITOR (Novel / Light Novel) ── -->
                    <div class="mt-6 bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                        <div id="quillEditor"><?php
                            $rawBody = old('body', $chapter['body'] ?? '');
                            // For LN, strip the image-embed comment before rendering to editor
                            if ($isLN) {
                                $rawBody = preg_replace('/<!--chapter_images:.*?-->/', '', $rawBody);
                            }
                            echo $rawBody;
                        ?></div>
                    </div>
                    <?php endif; ?>

                    <?php if ($hasImages): ?>
                    <!-- ── IMAGE UPLOAD (Comic / Light Novel) ── -->
                    <div class="mt-6">
                        <div class="flex items-center justify-between mb-3">
                            <label class="text-sm font-bold text-slate-700">
                                <?= $isComic ? 'Halaman Komik (berurutan)' : 'Gambar Ilustrasi Bab' ?>
                            </label>
                            <span class="text-[10px] text-slate-400 italic">JPG, PNG, WebP · maks. 5MB/file</span>
                        </div>

                        <!-- Drop zone -->
                        <div id="imageDropZone" class="relative group min-h-[140px] bg-white rounded-3xl border-2 border-dashed border-slate-200 hover:border-indigo-300 transition-all cursor-pointer">
                            <div id="imageEmptyState" class="absolute inset-0 flex flex-col items-center justify-center text-slate-400 pointer-events-none">
                                <span class="material-symbols-outlined text-4xl mb-2">add_photo_alternate</span>
                                <p class="text-xs font-medium">Klik atau seret gambar ke sini</p>
                                <p class="text-[10px] mt-1 text-slate-300">Urutan sesuai pilihan</p>
                            </div>
                            <div id="imageGrid" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3 p-4 min-h-[140px]"></div>
                            <input type="file" id="chapterImageInput" name="chapter_images[]"
                                   class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                                   accept="image/*" multiple>
                        </div>
                        <p class="mt-2 text-[10px] text-slate-400 italic">
                            <?= $isComic ? 'Tambah gambar secara kumulatif. Urutan bisa diatur dengan mengurutkan file yang dipilih.' : 'Gambar akan ditampilkan di bawah teks bab.' ?>
                        </p>
                    </div>
                    <?php endif; ?>

                    <div class="py-6 flex items-center justify-between text-[10px] text-slate-400">
                        <span><?= esc($work['title']) ?></span>
                        <?php if ($mode === 'edit'): ?>
                            <span>Terakhir disimpan: <?= date('d M Y, H:i', strtotime($chapter['updated_at'])) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </form>
    </main>

    <script>
        <?php if ($hasText): ?>
        // ----- Init Quill -----
        const quill = new Quill('#quillEditor', {
            theme: 'snow',
            placeholder: 'Mulai tulisanmu di sini...',
            modules: {
                toolbar: [
                    [{ header: [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    ['blockquote'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    [{ indent: '-1' }, { indent: '+1' }],
                    ['link'],
                    ['clean']
                ]
            }
        });

        // ----- Word counter -----
        const wordCountEl  = document.getElementById('wordCount');
        const readTimeEl   = document.getElementById('readTime');

        function updateCount() {
            const text  = quill.getText().trim();
            const words = text ? text.split(/\s+/).filter(Boolean).length : 0;
            const mins  = Math.ceil(words / 200);
            wordCountEl.textContent = words.toLocaleString('id') + ' kata';
            readTimeEl.textContent  = `~${mins} mnt baca`;
        }

        quill.on('text-change', updateCount);
        updateCount();
        <?php endif; ?>

        <?php if ($hasImages): ?>
        // ----- Image Upload Preview -----
        const imageInput    = document.getElementById('chapterImageInput');
        const imageGrid     = document.getElementById('imageGrid');
        const emptyState    = document.getElementById('imageEmptyState');
        const dataTransfer  = new DataTransfer();

        function syncInput() {
            imageInput.files = dataTransfer.files;
        }

        function refreshGrid() {
            imageGrid.innerHTML = '';
            const files = Array.from(dataTransfer.files);

            if (files.length === 0) {
                emptyState.style.display = 'flex';
                return;
            }
            emptyState.style.display = 'none';

            files.forEach((file, i) => {
                const reader = new FileReader();
                const card   = document.createElement('div');
                card.className = 'chapter-img-card';

                reader.onload = e => {
                    card.innerHTML = `
                        <img src="${e.target.result}" alt="${file.name}">
                        <div class="badge">#${i + 1}</div>
                        <div class="remove-btn" data-index="${i}" title="Hapus">✕</div>
                    `;
                    card.querySelector('.remove-btn').addEventListener('click', (ev) => {
                        ev.stopPropagation();
                        const idx = parseInt(ev.currentTarget.dataset.index);
                        // Rebuild DataTransfer without this index
                        const newDT = new DataTransfer();
                        Array.from(dataTransfer.files).forEach((f, j) => {
                            if (j !== idx) newDT.items.add(f);
                        });
                        dataTransfer.items.clear();
                        Array.from(newDT.files).forEach(f => dataTransfer.items.add(f));
                        syncInput();
                        refreshGrid();
                    });
                };
                reader.readAsDataURL(file);
                imageGrid.appendChild(card);
            });
        }

        imageInput.addEventListener('change', function () {
            Array.from(this.files).forEach(f => dataTransfer.items.add(f));
            syncInput();
            refreshGrid();
            // Menghapus 'this.value = ""' agar FormData native browser mengirimkan file dengan benar.
        });

        refreshGrid(); // init (empty)
        <?php endif; ?>

        // ----- Toast helpers -----
        function showToast(type, msg, autohide = 7000) {
            const toast   = document.getElementById('uploadToast');
            const iconEl  = document.getElementById('toastIcon');
            const msgEl   = document.getElementById('toastMsg');
            const icons   = { error: '❌', warning: '⚠️', ok: '✅' };
            toast.className = `show toast-${type}`;
            iconEl.textContent = icons[type] || '⚠️';
            msgEl.textContent  = msg;
            if (autohide) setTimeout(hideToast, autohide);
        }
        function hideToast() {
            document.getElementById('uploadToast').className = '';
        }

        // ----- Progress overlay helpers -----
        const overlay   = document.getElementById('uploadOverlay');
        const progBar   = document.getElementById('progBar');
        const uploadPct = document.getElementById('uploadPct');

        function showOverlay(title, subtitle) {
            document.getElementById('overlayTitle').textContent    = title    || 'Mengunggah gambar...';
            document.getElementById('overlaySubtitle').textContent = subtitle || 'Mohon jangan tutup halaman ini';
            progBar.style.width = '0%';
            uploadPct.textContent = '0%';
            overlay.classList.add('visible');
        }
        function setProgress(pct) {
            progBar.style.width = pct + '%';
            uploadPct.textContent = pct + '%';
        }
        function hideOverlay() {
            overlay.classList.remove('visible');
        }

        // ----- Submit with XHR progress -----
        const hasImages = <?= $hasImages ? 'true' : 'false' ?>;

        function submitForm(status) {
            document.getElementById('statusField').value = status;
            <?php if ($hasText): ?>
            document.getElementById('bodyField').value = quill.root.innerHTML;
            <?php endif; ?>

            const form     = document.getElementById('chapterForm');
            const formData = new FormData(form);
            
            const fileCount = hasImages
                ? (document.getElementById('chapterImageInput')?.files?.length ?? 0)
                : 0;

            // Show overlay only when there are images to upload
            if (hasImages && fileCount > 0) {
                const label = status === 'published' ? 'Menerbitkan bab...' : 'Menyimpan draft...';
                showOverlay(label, `Mengunggah ${fileCount} gambar ke server, mohon tunggu`);
            } else {
                // No images → just show a simple spinner without progress
                showOverlay(
                    status === 'published' ? 'Menerbitkan bab...' : 'Menyimpan draft...',
                    'Sedang menyimpan data...'
                );
                setProgress(60); // indeterminate feel
            }

            const xhr = new XMLHttpRequest();

            // Track real upload progress
            xhr.upload.onprogress = function (e) {
                if (e.lengthComputable) {
                    // Upload phase is ~80% of the total feel; reserve 20% for server processing
                    const pct = Math.min(80, Math.round((e.loaded / e.total) * 80));
                    setProgress(pct);
                }
            };

            // Server done
            xhr.onreadystatechange = function () {
                if (xhr.readyState !== 4) return;

                if (xhr.status >= 200 && xhr.status < 400) {
                    setProgress(100);
                    document.getElementById('overlayTitle').textContent    = 'Berhasil disimpan!';
                    document.getElementById('overlaySubtitle').textContent = 'Mengalihkan halaman...';

                    // Follow the final redirect URL
                    setTimeout(() => {
                        window.location.href = xhr.responseURL || form.action;
                    }, 500);
                } else {
                    // HTTP error
                    hideOverlay();
                    showToast('error', `Gagal menyimpan bab (HTTP ${xhr.status}). Periksa koneksi dan coba lagi.`);
                }
            };

            xhr.onerror = function () {
                hideOverlay();
                showToast('error', 'Koneksi ke server terputus. Periksa koneksi internet Anda lalu coba lagi.');
            };

            xhr.open('POST', form.action);
            xhr.send(formData);
        }

        document.getElementById('btnDraft').addEventListener('click',   () => submitForm('draft'));
        document.getElementById('btnPublish').addEventListener('click', () => submitForm('published'));

        // Toggle price container
        const lockRadios     = document.querySelectorAll('input[name="is_locked"]');
        const priceContainer = document.getElementById('priceContainer');

        lockRadios.forEach(radio => {
            radio.addEventListener('change', () => {
                priceContainer.style.display = radio.value == '1' ? 'block' : 'none';
            });
        });
    </script>
</body>
</html>
