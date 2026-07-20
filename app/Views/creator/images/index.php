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
        .drop-zone.drag-over { border-color: #6366f1; background-color: #eef2ff; }
        .img-card:hover .img-delete { opacity: 1; }
        .img-delete { opacity: 0; transition: opacity 0.2s; }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <?= view('creator/_sidebar', ['activePage' => 'content', 'user' => $user, 'creatorProfile' => $creatorProfile, 'username' => $username]) ?>

    <main class="flex-1 flex flex-col h-full overflow-hidden bg-slate-50">
        <header class="bg-white border-b border-slate-200 px-4 md:px-8 py-3 md:py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="<?= base_url('creator/content') ?>" class="p-1.5 hover:bg-slate-50 rounded-xl transition-all text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <div class="min-w-0">
                    <h2 class="text-base md:text-lg font-bold text-slate-900">Kelola Gambar</h2>
                    <p class="text-[10px] text-slate-400 truncate max-w-[150px] sm:max-w-xs"><?= esc($work['title']) ?></p>
                </div>
            </div>
            <div class="hidden md:flex items-center gap-3 text-right">
                <p class="text-xs font-bold text-slate-900"><?= $creatorProfile['display_name'] ?? $username ?></p>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-4 md:p-8 pb-24 lg:pb-8 custom-scrollbar">
            <div class="max-w-5xl mx-auto space-y-5 md:space-y-8">

                <?php if ($msg = session()->getFlashdata('message')): ?>
                    <div class="px-5 py-4 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-2xl text-sm font-medium flex items-center gap-3">
                        <span class="material-symbols-outlined text-base">check_circle</span> <?= esc($msg) ?>
                    </div>
                <?php endif; ?>

                <!-- Upload Area -->
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8">
                    <h3 class="text-sm font-bold text-slate-900 mb-1">Upload Gambar</h3>
                    <p class="text-xs text-slate-400 mb-6">Pilih satu atau lebih gambar. Format: JPG, PNG, WebP. Maks 5MB/gambar.</p>

                    <form id="uploadForm" action="<?= base_url('creator/content/' . $work['id'] . '/images/upload') ?>" method="POST" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <div id="dropZone" class="drop-zone border-2 border-dashed border-slate-200 rounded-2xl p-12 text-center cursor-pointer hover:border-indigo-400 transition-all">
                            <span class="material-symbols-outlined text-5xl text-slate-300 block mb-3">cloud_upload</span>
                            <p class="text-sm font-bold text-slate-500">Klik atau seret gambar ke sini</p>
                            <p class="text-xs text-slate-400 mt-1">Bisa pilih banyak gambar sekaligus</p>
                            <input type="file" name="images[]" id="imageInput" multiple accept="image/*" class="hidden">
                        </div>

                        <!-- Preview sebelum upload -->
                        <div id="previewGrid" class="hidden mt-6 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-4"></div>

                        <div id="uploadActions" class="hidden mt-6 flex items-center justify-end gap-3">
                            <button type="button" id="clearBtn" class="px-5 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-50 transition-all">Batal</button>
                            <button type="submit" class="flex items-center gap-2 px-6 py-2 bg-indigo-600 text-white rounded-xl font-bold text-sm hover:bg-indigo-700 transition-all">
                                <span class="material-symbols-outlined text-sm">upload</span>
                                Upload <span id="uploadCount">0</span> Gambar
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Existing Images Grid -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-slate-900">Halaman Terupload <span class="text-slate-400 font-normal">(<?= count($images) ?>)</span></h3>
                        <?php if (!empty($images)): ?>
                            <p class="text-[10px] text-slate-400">Hover gambar untuk hapus</p>
                        <?php endif; ?>
                    </div>

                    <?php if (empty($images)): ?>
                        <div class="bg-white rounded-3xl border border-slate-100 p-12 text-center shadow-sm">
                            <span class="material-symbols-outlined text-5xl text-slate-200 block mb-3">image</span>
                            <p class="text-sm text-slate-400">Belum ada gambar terupload.</p>
                        </div>
                    <?php else: ?>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                            <?php foreach ($images as $img): ?>
                                <div class="img-card relative group rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 aspect-[3/4] shadow-sm">
                                    <?php 
                                        $finalPath = base_url('image/gallery/' . $img['id']);
                                    ?>
                                    <img src="<?= $finalPath ?>" class="w-full h-full object-cover">
                                    <div class="img-delete absolute inset-0 bg-black/50 flex flex-col items-center justify-center gap-2">
                                        <span class="text-white text-[10px] font-bold bg-black/30 px-2 py-1 rounded-full">#<?= $img['order_num'] ?></span>
                                        <form method="POST" action="<?= base_url('creator/content/' . $work['id'] . '/images/' . $img['id'] . '/delete') ?>" onsubmit="return confirm('Hapus gambar ini?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="p-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-all">
                                                <span class="material-symbols-outlined text-sm">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </main>

    <script>
        const dropZone      = document.getElementById('dropZone');
        const imageInput    = document.getElementById('imageInput');
        const previewGrid   = document.getElementById('previewGrid');
        const uploadActions = document.getElementById('uploadActions');
        const uploadCount   = document.getElementById('uploadCount');
        const clearBtn      = document.getElementById('clearBtn');
        const uploadForm    = document.getElementById('uploadForm');

        let selectedFiles = [];

        dropZone.addEventListener('click', () => imageInput.click());

        dropZone.addEventListener('dragover', e => {
            e.preventDefault();
            dropZone.classList.add('drag-over', 'border-indigo-500', 'bg-indigo-50/30');
        });
        dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over', 'border-indigo-500', 'bg-indigo-50/30'));
        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.classList.remove('drag-over', 'border-indigo-500', 'bg-indigo-50/30');
            handleNewFiles(e.dataTransfer.files);
        });

        imageInput.addEventListener('change', () => handleNewFiles(imageInput.files));

        function handleNewFiles(files) {
            const newFiles = Array.from(files);
            selectedFiles = [...selectedFiles, ...newFiles];
            renderPreview();
            syncInput();
        }

        function renderPreview() {
            previewGrid.innerHTML = '';
            if (selectedFiles.length === 0) {
                previewGrid.classList.add('hidden');
                uploadActions.classList.add('hidden');
                return;
            }

            previewGrid.classList.remove('hidden');
            previewGrid.className = 'mt-6 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3';
            uploadActions.classList.remove('hidden');
            uploadCount.textContent = selectedFiles.length;

            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                const div = document.createElement('div');
                div.className = 'relative aspect-[3/4] rounded-xl overflow-hidden bg-slate-100 border border-slate-200 group/preview';
                
                reader.onload = e => {
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-full object-cover">
                        <button type="button" onclick="removeFile(${index})" class="absolute top-1 right-1 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover/preview:opacity-100 transition-opacity shadow-lg">
                            <span class="material-symbols-outlined text-[14px]">close</span>
                        </button>
                    `;
                };
                reader.readAsDataURL(file);
                previewGrid.appendChild(div);
            });
        }

        function syncInput() {
            const dt = new DataTransfer();
            selectedFiles.forEach(file => dt.items.add(file));
            imageInput.files = dt.files;
        }

        window.removeFile = function(index) {
            selectedFiles.splice(index, 1);
            renderPreview();
            syncInput();
        };

        clearBtn.addEventListener('click', () => {
            selectedFiles = [];
            imageInput.value = '';
            renderPreview();
        });

        uploadForm.addEventListener('submit', function() {
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = `<span class="material-symbols-outlined animate-spin text-sm">sync</span> Memproses...`;
        });
    </script>
</body>
</html>
