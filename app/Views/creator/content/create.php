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
                <h2 class="text-lg font-bold text-slate-900">Tambah Karya Baru</h2>
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

                <form action="<?= base_url('creator/content') ?>" method="POST" enctype="multipart/form-data" class="space-y-8">
                    <?= csrf_field() ?>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Cover Upload -->
                        <div class="md:col-span-1">
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
                        <div class="md:col-span-2 space-y-8">
                            <!-- Format Karya -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-4">Format Utama Karya</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <!-- Gambar -->
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" name="work_format" value="image" class="peer sr-only" id="formatImage">
                                        <div class="p-4 bg-white border-2 border-slate-100 rounded-2xl transition-all peer-checked:border-indigo-600 peer-checked:bg-indigo-50/30 group-hover:border-slate-200">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 bg-slate-50 text-slate-400 rounded-xl flex items-center justify-center transition-all group-hover:scale-110 peer-checked:bg-indigo-600 peer-checked:text-white">
                                                    <span class="material-symbols-outlined text-xl">image</span>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-bold text-slate-900">Gambar</p>
                                                    <p class="text-[10px] text-slate-500 mt-0.5">Ilustrasi, Foto, dll.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                    <!-- Cerita -->
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" name="work_format" value="story" class="peer sr-only" id="formatStory" checked>
                                        <div class="p-4 bg-white border-2 border-slate-100 rounded-2xl transition-all peer-checked:border-indigo-600 peer-checked:bg-indigo-50/30 group-hover:border-slate-200">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 bg-slate-50 text-slate-400 rounded-xl flex items-center justify-center transition-all group-hover:scale-110 peer-checked:bg-indigo-600 peer-checked:text-white">
                                                    <span class="material-symbols-outlined text-xl">auto_stories</span>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-bold text-slate-900">Cerita</p>
                                                    <p class="text-[10px] text-slate-500 mt-0.5">Novel, Light Novel, Comic</p>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                <!-- Sub-tipe Cerita (muncul hanya saat pilih Cerita) -->
                                <div id="storySubTypeContainer" class="mt-4 grid grid-cols-3 gap-3">
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" name="content_type" value="novel" class="peer sr-only" checked>
                                        <div class="p-3 bg-white border-2 border-slate-100 rounded-xl transition-all peer-checked:border-indigo-500 peer-checked:bg-indigo-50/40 group-hover:border-slate-200 text-center">
                                            <span class="material-symbols-outlined text-2xl text-slate-400 peer-checked:text-indigo-600 mb-1 block">menu_book</span>
                                            <p class="text-[11px] font-bold text-slate-800">Novel</p>
                                        </div>
                                    </label>
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" name="content_type" value="light_novel" class="peer sr-only">
                                        <div class="p-3 bg-white border-2 border-slate-100 rounded-xl transition-all peer-checked:border-indigo-500 peer-checked:bg-indigo-50/40 group-hover:border-slate-200 text-center">
                                            <span class="material-symbols-outlined text-2xl text-slate-400 peer-checked:text-indigo-600 mb-1 block">auto_stories</span>
                                            <p class="text-[11px] font-bold text-slate-800">Light Novel</p>
                                        </div>
                                    </label>
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" name="content_type" value="comic" class="peer sr-only">
                                        <div class="p-3 bg-white border-2 border-slate-100 rounded-xl transition-all peer-checked:border-indigo-500 peer-checked:bg-indigo-50/40 group-hover:border-slate-200 text-center">
                                            <span class="material-symbols-outlined text-2xl text-slate-400 peer-checked:text-indigo-600 mb-1 block">collections_bookmark</span>
                                            <p class="text-[11px] font-bold text-slate-800">Comic</p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Gallery Upload (Visible only for Image format) -->
                            <div id="galleryUploadContainer" class="hidden animate-in fade-in slide-in-from-top-4 duration-500">
                                <label class="block text-sm font-bold text-slate-700 mb-4">Galeri Gambar (Multiple)</label>
                                <div class="relative group">
                                    <div id="galleryPreview" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 min-h-[120px] p-4 bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200 transition-all group-hover:border-indigo-300">
                                        <div class="col-span-full flex flex-col items-center justify-center py-4 text-slate-400">
                                            <span class="material-symbols-outlined text-3xl mb-1">add_photo_alternate</span>
                                            <p class="text-[10px] font-medium">Klik untuk upload banyak gambar</p>
                                        </div>
                                    </div>
                                    <input type="file" name="gallery_images[]" id="galleryInput" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*" multiple>
                                </div>
                                <p class="mt-3 text-[10px] text-slate-400 italic">Pilih satu atau lebih gambar. Urutan akan sesuai dengan pilihan.</p>
                            </div>

                            <!-- Judul -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Judul Karya</label>
                                <input type="text" name="title" value="<?= old('title') ?>" placeholder="Masukkan judul yang menarik..." 
                                    class="w-full px-5 py-4 bg-white border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-900 font-medium">
                            </div>

                            <!-- Status Awal -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Status Awal</label>
                                <select name="status" class="w-full px-5 py-4 bg-white border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-900 font-medium appearance-none cursor-pointer">
                                    <option value="draft" <?= old('status') == 'draft' ? 'selected' : '' ?>>Simpan Draft</option>
                                    <option value="published" <?= old('status') == 'published' ? 'selected' : '' ?>>Langsung Terbit</option>
                                </select>
                            </div>

                            <!-- Deskripsi -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Sinopsis / Deskripsi</label>
                                <textarea name="description" rows="6" placeholder="Ceritakan sedikit tentang karya ini..." 
                                    class="w-full px-5 py-4 bg-white border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-900 font-medium resize-none"><?= old('description') ?></textarea>
                            </div>

                            <!-- Pengaturan Akses & Status -->
                            <div class="p-6 bg-indigo-50/50 rounded-3xl border border-indigo-100 space-y-6">
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
                                        <label class="block text-[11px] font-bold text-slate-700 mb-2 uppercase tracking-wider">Batasi Akses Cerita</label>
                                        <div class="flex items-center gap-4">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="radio" name="access_type" value="full" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500" checked>
                                                <span class="text-xs text-slate-700">Seluruh Cerita</span>
                                            </label>
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="radio" name="access_type" value="chapter" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500">
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
                                        <input type="checkbox" name="is_paid" id="isPaidToggle" class="sr-only peer" <?= old('is_paid') ? 'checked' : '' ?>>
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        <span class="ml-3 text-xs font-bold text-slate-700 peer-checked:text-indigo-600" id="accessLabel">Gratis</span>
                                    </label>
                                </div>

                                <div id="paidSettings" class="<?= old('is_paid') ? '' : 'hidden' ?> space-y-6 pt-6 border-t border-indigo-100/50">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-700 mb-2 uppercase tracking-wider">Harga (CC)</label>
                                            <div class="relative">
                                                <input type="number" name="price" value="<?= old('price', 0) ?>" 
                                                    class="w-full pl-5 pr-12 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-bold text-slate-900">
                                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-400">CC</span>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-700 mb-2 uppercase tracking-wider">Durasi Timer (Detik)</label>
                                            <div class="relative">
                                                <input type="number" name="timer_duration" value="<?= old('timer_duration', 30) ?>" 
                                                    class="w-full pl-5 pr-12 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-bold text-slate-900">
                                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-400">SEC</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-2 uppercase tracking-wider">Teks Watermark</label>
                                        <input type="text" name="watermark_text" value="<?= old('watermark_text', $creatorProfile['display_name'] ?? $username) ?>" placeholder="Contoh: Milik <?= $creatorProfile['display_name'] ?? $username ?>"
                                            class="w-full px-5 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-900 font-medium">
                                        <p class="mt-2 text-[9px] text-slate-400 italic">Watermark akan muncul saat user membayar untuk melihat preview.</p>
                                    </div>
                                </div>
                            </div>
                            <!-- Submit Progress Overlay (Hidden by default) -->
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

                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-4 pt-8 border-t border-slate-100 relative z-10">
                        <a href="<?= base_url('creator/content') ?>" class="px-8 py-3 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-50 transition-all">
                            Batal
                        </a>
                        <button type="submit" id="submitBtn" class="px-10 py-3 bg-indigo-600 text-white rounded-xl font-bold text-sm hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 flex items-center gap-2">
                            <span>Simpan Karya</span>
                            <span class="material-symbols-outlined text-sm hidden" id="submitSpinner">sync</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </main>

    <script>
        // Simple image preview
        const coverInput = document.getElementById('coverInput');
        const coverPreview = document.getElementById('coverPreview');

        coverInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    coverPreview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                    coverPreview.classList.remove('border-dashed');
                }
                reader.readAsDataURL(file);
            }
        });

        // Toggle Gallery Upload & Story Sub-types based on Format
        const formatInputs = document.querySelectorAll('input[name="work_format"]');
        const galleryContainer = document.getElementById('galleryUploadContainer');
        const storySubTypeContainer = document.getElementById('storySubTypeContainer');

        function toggleFormatFields(format) {
            if (format === 'image') {
                galleryContainer.classList.remove('hidden');
                storySubTypeContainer.classList.add('hidden');
            } else {
                galleryContainer.classList.add('hidden');
                storySubTypeContainer.classList.remove('hidden');
            }
        }

        formatInputs.forEach(input => {
            input.addEventListener('change', function() {
                toggleFormatFields(this.value);
            });
        });

        // Initialize on load
        const checkedFormat = document.querySelector('input[name="work_format"]:checked');
        if (checkedFormat) toggleFormatFields(checkedFormat.value);

        // Multiple Gallery Preview
        const galleryInput = document.getElementById('galleryInput');
        const galleryPreview = document.getElementById('galleryPreview');
        const galleryDataTransfer = new DataTransfer();

        galleryInput.addEventListener('change', function() {
            // Append incoming files to our DataTransfer object
            const newFiles = Array.from(this.files);
            newFiles.forEach(file => {
                galleryDataTransfer.items.add(file);
            });
            
            // Sync back to input
            this.files = galleryDataTransfer.files;
            
            const allFiles = Array.from(this.files);
            galleryPreview.innerHTML = ''; // Clear preview

            if (allFiles.length === 0) {
                galleryPreview.innerHTML = `
                    <div class="col-span-full flex flex-col items-center justify-center py-4 text-slate-400">
                        <span class="material-symbols-outlined text-3xl mb-1">add_photo_alternate</span>
                        <p class="text-[10px] font-medium">Klik untuk upload banyak gambar</p>
                    </div>
                `;
                return;
            }

            allFiles.forEach((file, index) => {
                const reader = new FileReader();
                const div = document.createElement('div');
                div.className = 'relative aspect-square rounded-2xl overflow-hidden bg-slate-200 border border-slate-100 shadow-sm z-0';
                
                reader.onload = function(e) {
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-full object-cover">
                        <div class="absolute top-2 left-2 bg-black/50 text-white text-[10px] font-bold px-2 py-0.5 rounded-full z-10">#${index + 1}</div>
                    `;
                }
                reader.readAsDataURL(file);
                galleryPreview.appendChild(div);
            });
            
            // Add a visual 'add more' box at the end
            const addMoreDiv = document.createElement('div');
            addMoreDiv.className = 'relative aspect-square rounded-2xl border-2 border-dashed border-slate-300 flex flex-col items-center justify-center text-slate-400 z-0 bg-slate-50/50 hover:bg-slate-100/50 transition-colors pointer-events-none';
            addMoreDiv.innerHTML = `
                <span class="material-symbols-outlined text-2xl mb-1">add</span>
                <span class="text-[10px] font-bold uppercase">Tambah</span>
            `;
            galleryPreview.appendChild(addMoreDiv);
        });

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

        // AJAX Form Submission with Progress Tracking
        const createForm = document.querySelector('form');
        const submitBtn = document.getElementById('submitBtn');
        const submitSpinner = document.getElementById('submitSpinner');
        const progressContainer = document.getElementById('uploadProgressContainer');
        const progressBar = document.getElementById('uploadProgressBar');
        const progressText = document.getElementById('uploadProgressText');

        if (createForm) {
            createForm.addEventListener('submit', function(e) {
                e.preventDefault(); // Prevent standard submission

                // Disable submit button and show spinner
                submitBtn.disabled = true;
                submitSpinner.classList.remove('hidden');
                submitSpinner.classList.add('animate-spin');
                
                // Show progress overlay
                progressContainer.classList.remove('hidden');
                // Reset progress
                progressBar.style.width = '0%';
                progressText.textContent = '0%';

                const formData = new FormData(this);
                const xhr = new XMLHttpRequest();

                xhr.open('POST', this.action, true);
                // CodeIgniter CSRF is usually standard via forms, but good to ensure header if needed
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                // Upload progress event listener
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percentComplete = Math.round((e.loaded / e.total) * 100);
                        progressBar.style.width = percentComplete + '%';
                        progressText.textContent = percentComplete + '%';
                    }
                });

                // Request completed
                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                progressText.textContent = 'Selesai!';
                                progressBar.style.width = '100%';
                                setTimeout(() => {
                                    window.location.href = response.redirect || '<?= base_url('creator/content') ?>';
                                }, 500);
                            } else {
                                // Gagal upload dari validasi backend
                                let errorMsg = 'Gagal menyimpan karya:\n\n';
                                if (response.errors) {
                                    for (const [field, msg] of Object.entries(response.errors)) {
                                        errorMsg += `- ${msg}\n`;
                                    }
                                } else if (response.message) {
                                    errorMsg += response.message;
                                } else {
                                    errorMsg += 'Terjadi kesalahan tidak diketahui.';
                                }
                                alert(errorMsg);
                                
                                // Reset UI kembali ke semula
                                submitBtn.disabled = false;
                                submitSpinner.classList.add('hidden');
                                submitSpinner.classList.remove('animate-spin');
                                progressContainer.classList.add('hidden');
                            }
                        } catch (e) {
                            // Fallback jika tidak sengaja return HTML/redirect standar
                            alert('Terjadi kesalahan format respon dari server.');
                            submitBtn.disabled = false;
                            submitSpinner.classList.add('hidden');
                            submitSpinner.classList.remove('animate-spin');
                            progressContainer.classList.add('hidden');
                        }
                    } else {
                        // Error handling
                        alert('Terjadi kesalahan jaringan/server saat mengunggah. (Status: ' + xhr.status + ')');
                        
                        // Reset UI
                        submitBtn.disabled = false;
                        submitSpinner.classList.add('hidden');
                        submitSpinner.classList.remove('animate-spin');
                        progressContainer.classList.add('hidden');
                    }
                };

                // Network errors
                xhr.onerror = function() {
                    alert('Terjadi kesalahan jaringan.');
                    submitBtn.disabled = false;
                    submitSpinner.classList.add('hidden');
                    submitSpinner.classList.remove('animate-spin');
                    progressContainer.classList.add('hidden');
                };

                xhr.send(formData);
            });
        }
    </script>
</body>
</html>
