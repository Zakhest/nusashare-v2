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
                        <div class="md:col-span-1">
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

                            <!-- Tipe & Status -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Tipe Konten</label>
                                    <select name="content_type" class="w-full px-5 py-4 bg-white border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-900 font-medium appearance-none cursor-pointer">
                                        <option value="novel"  <?= (old('content_type', $work['content_type']) === 'novel' || old('content_type', $work['content_type']) === 'text') ? 'selected' : '' ?>>Novel (Teks Saja)</option>
                                        <option value="light_novel" <?= (old('content_type', $work['content_type']) === 'light_novel') ? 'selected' : '' ?>>Light Novel (Teks + Ilustrasi)</option>
                                        <option value="comic"  <?= (old('content_type', $work['content_type']) === 'comic') ? 'selected' : '' ?>>Komik (Manga / Webtoon)</option>
                                        <option value="image" <?= (old('content_type', $work['content_type']) === 'image') ? 'selected' : '' ?>>Galeri Gambar</option>
                                        <option value="pdf"   <?= (old('content_type', $work['content_type']) === 'pdf')   ? 'selected' : '' ?>>Dokumen PDF</option>
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
                            <div class="p-6 bg-indigo-50/50 rounded-3xl border border-indigo-100 space-y-6">
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
            });
        }

        // Toggle Gallery Upload based on Content Type
        const contentTypeSelect = document.querySelector('select[name="content_type"]');
        const galleryContainer = document.getElementById('galleryUploadContainer');

        if (contentTypeSelect) {
            contentTypeSelect.addEventListener('change', function() {
                if (this.value === 'image') {
                    galleryContainer.classList.remove('hidden');
                } else {
                    galleryContainer.classList.add('hidden');
                }
            });
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
    </script>
</body>
</html>
