<?php /** INTEGRATED SIDEBAR & PROFILE VIEW **/ ?>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= $title ?? 'Profil Saya' ?> - NusaShare</title>
    
    <!-- Fonts: Inter -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Material Icons -->
    <link rel="icon" href="<?= base_url('assets/icon/logonus.png') ?>" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">
    <!-- Cropper.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js" defer></script>

    <style>
        :root {
            --lp-bg: #EEF2FF;
            --lp-primary: #4F46E5;
            --lp-accent: #22D3EE;
            --lp-surface: #FFFFFF;
            --lp-border: rgba(79, 70, 229, 0.15);
            --lp-text-main: #0F172A;
            --lp-text-muted: #475569;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #F8FAFC;
            color: var(--lp-text-main);
            overflow-x: hidden;
        }

        .sidebar-link.active { background-color: #EEF2FF; color: #4F46E5; border-right: 4px solid #4F46E5; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #E2E8F0; border-radius: 10px; }

        .btn-primary {
            background: linear-gradient(135deg, #4F46E5, #22D3EE);
            color: white;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            opacity: 0.95;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
        }

        .card {
            background: var(--lp-surface);
            border: 1px solid #E2E8F0;
            border-radius: 16px;
        }
    </style>
</head>
<body class="bg-[#F8FAFC] flex h-screen overflow-hidden">
 
    <?= view('dashboard/_sidebar', [
        'activePage'     => 'profile',
        'user'           => $user,
        'creatorProfile' => $creatorProfile
    ]) ?>

    <!-- Main Wrapper -->
    <div class="flex-1 flex flex-col h-full overflow-hidden">

        <!-- Top Bar -->
        <nav class="bg-white border-b border-slate-200 px-8 py-4 flex items-center justify-between sticky top-0 z-40">
            <div class="w-full flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <h2 class="text-lg font-bold text-slate-900 hidden lg:block">Profil Saya</h2>
                    <div class="flex items-center gap-4 lg:hidden">
                        <img src="<?= base_url('assets/icon/logonus.png') ?>" alt="Logo" class="w-8 h-8">
                    </div>
                </div>

                <div class="flex items-center gap-6">
                    <!-- Notifications Dropdown -->
                    <div class="relative hidden md:flex" id="notification-dropdown-container">
                        <button id="notification-bell" class="text-slate-500 hover:text-slate-900 transition-colors relative">
                            <span class="material-symbols-outlined">notifications</span>
                            <span id="notification-badge" class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full hidden border border-white"></span>
                        </button>
                        
                        <div id="notification-dropdown" class="absolute right-0 top-full mt-4 w-80 bg-white rounded-xl shadow-xl border border-slate-100 hidden z-50 transform opacity-0 scale-95 transition-all origin-top-right">
                            <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50 rounded-t-xl">
                                <h3 class="font-bold text-slate-900">Notifikasi</h3>
                                <button id="mark-all-read" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Tandai semua dibaca</button>
                            </div>
                            <div id="notification-list" class="max-h-80 overflow-y-auto custom-scrollbar">
                                <div class="p-6 text-center text-slate-400 text-sm">Memuat notifikasi...</div>
                            </div>
                        </div>
                    </div>
                    <?php 
                        $creditModel = new \App\Models\CreditModel();
                        $userCredit = $creditModel->find(session()->get('userId'));
                        $balance = $userCredit ? $userCredit['balance'] : 0;
                    ?>
                    <a href="<?= base_url('topup') ?>" class="bg-indigo-50 px-3 py-1.5 rounded-full flex items-center gap-2 border border-indigo-100 hover:bg-indigo-100 transition-colors">
                        <span class="material-symbols-outlined text-indigo-600 text-lg">account_balance_wallet</span>
                        <span class="text-xs font-bold text-indigo-900"><?= number_format($balance) ?> CC</span>
                    </a>
                    <div class="flex items-center gap-3 border-l pl-6 border-slate-100">
                        <div class="text-right">
                            <p class="text-xs font-bold text-slate-900"><?= $username ?></p>
                            <p class="text-[10px] text-slate-500"><?= $user['starsoul_status'] ?? 'Anggota NusaShare' ?></p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-[#4F46E5] font-bold overflow-hidden">
                                    <?php if (!empty($profile['profile_image'])): ?>
                                        <img src="<?= base_url('image/profile/' . $profile['profile_image']) ?>" alt="Avatar" class="w-full h-full object-cover">
                            <?php else: ?>
                                <?= strtoupper(substr($username, 0, 1)) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Content Container -->
        <div class="flex-1 overflow-y-auto">
            
            <header class="pt-12 pb-8 px-8">
                <div class="max-w-4xl mx-auto">
                    <h1 class="text-3xl font-bold text-[#0F172A] mb-2">Pengaturan Profil</h1>
                    <p class="text-[#64748B]">Kelola informasi akun dan profil publik Anda.</p>
                </div>
            </header>

            <main class="max-w-4xl mx-auto px-8 py-8 mb-20">
                
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-3">
                        <span class="material-symbols-outlined">check_circle</span>
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-3">
                        <span class="material-symbols-outlined">error</span>
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <div class="grid gap-8">
                    <!-- Profile Card -->
                    <div class="card p-8">
                        <form action="<?= base_url('me/profile') ?>" method="POST" enctype="multipart/form-data">
                        <div class="flex flex-col md:flex-row gap-8 items-start">
                            <!-- Avatar Section -->
                            <div class="flex flex-col items-center gap-4">
                                <div id="avatarPreviewContainer" class="w-32 h-32 rounded-full bg-indigo-100 flex items-center justify-center text-[#4F46E5] text-5xl font-bold border-4 border-white shadow-xl overflow-hidden">
                                    <?php if (!empty($profile['profile_image'])): ?>
                                        <img src="<?= base_url('image/profile/' . $profile['profile_image']) ?>" alt="Avatar" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <?= strtoupper(substr($username, 0, 1)) ?>
                                    <?php endif; ?>
                                </div>
                                <button type="button" onclick="document.getElementById('profileImageInput').click()" class="text-sm font-medium text-[#4F46E5] hover:underline">Ganti Foto</button>
                                <input type="file" id="profileImageInput" class="hidden" accept="image/*">
                                <!-- Hidden: receives base64 of cropped image -->
                                <input type="hidden" name="profile_image_cropped" id="profileImageCropped">
                            </div>

                            <!-- Form Section -->
                            <div class="flex-1 w-full">
                                <div class="space-y-6">
                                    <div class="grid md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-bold text-slate-700 mb-2">Username</label>
                                            <input type="text" name="username" value="<?= old('username', $user['username']) ?>" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#4F46E5] focus:ring-4 focus:ring-indigo-50 outline-none transition-all" placeholder="username">
                                            <?php if (isset(session('errors')['username'])): ?>
                                                <p class="text-xs text-red-500 mt-1"><?= session('errors')['username'] ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-slate-700 mb-2">Email</label>
                                            <input type="email" value="<?= $user['email'] ?>" disabled class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50 text-slate-400 cursor-not-allowed outline-none" placeholder="email@example.com">
                                            <p class="text-[10px] text-slate-400 mt-1">Email tidak dapat diubah.</p>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-2">Nama Tampilan (Display Name)</label>
                                        <input type="text" name="display_name" value="<?= old('display_name', $profile['display_name']) ?>" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#4F46E5] focus:ring-4 focus:ring-indigo-50 outline-none transition-all" placeholder="Nama Anda">
                                        <?php if (isset(session('errors')['display_name'])): ?>
                                            <p class="text-xs text-red-500 mt-1"><?= session('errors')['display_name'] ?></p>
                                        <?php endif; ?>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-2">Bio Singkat</label>
                                        <textarea name="bio" rows="4" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#4F46E5] focus:ring-4 focus:ring-indigo-50 outline-none transition-all resize-none" placeholder="Ceritakan sedikit tentang Anda..."><?= old('bio', $profile['bio']) ?></textarea>
                                        <?php if (isset(session('errors')['bio'])): ?>
                                            <p class="text-xs text-red-500 mt-1"><?= session('errors')['bio'] ?></p>
                                        <?php endif; ?>
                                        <p class="text-right text-[10px] text-slate-400 mt-1">Maksimum 200 karakter.</p>
                                    </div>

                                    <div class="pt-4 flex justify-end">
                                        <button type="submit" class="btn-primary px-10 py-3 rounded-xl font-bold shadow-lg shadow-indigo-500/20">
                                            Simpan Perubahan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>

                    <!-- Mobile Creator Actions -->
                    <div class="lg:hidden mt-6 px-4">
                        <?php if ($creatorProfile): ?>
                            <a href="<?= base_url('creator/dashboard') ?>" class="flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-50 text-indigo-700 rounded-xl font-bold text-xs border border-indigo-100 hover:bg-indigo-100 transition-all text-center">
                                <span class="material-symbols-outlined text-sm">potted_plant</span>
                                Creator Page <?= $creatorProfile['display_name'] ?>
                            </a>
                        <?php else: ?>
                            <button onclick="openCreatorModal()" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-900 text-white rounded-xl font-bold text-xs hover:bg-slate-800 transition-all text-center">
                                <span class="material-symbols-outlined text-sm">edit_square</span>
                                Daftar jadi Kreator!
                            </button>
                        <?php endif; ?>
                    </div>

                    <!-- Additional Info -->
                    <div class="grid md:grid-cols-3 gap-6">
                        <div class="card p-6 bg-slate-900 text-white">
                            <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
                                <span class="material-symbols-outlined text-indigo-400">auto_awesome</span>
                                Status Keperaksaan
                            </h3>
                            <div class="bg-indigo-500/20 rounded-2xl p-4 border border-white/10">
                                <p class="text-xs opacity-70 mb-1">StarSoul Kamu</p>
                                <div class="flex items-center gap-2">
                                    <span class="text-3xl font-bold"><?= number_format($user['starsoul_value'] ?? 0) ?></span>
                                    <span class="text-xs bg-white/20 px-2 py-0.5 rounded-full"><?= $user['starsoul_status'] ?? 'Anggota NusaShare' ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- User ID / PDF Password Card -->
                        <div class="card p-6 bg-gradient-to-br from-amber-50 to-orange-50 border-amber-200">
                            <h3 class="font-bold text-slate-900 mb-1 flex items-center gap-2">
                                <span class="material-symbols-outlined text-amber-500" style="font-variation-settings:'FILL' 1">lock</span>
                                User ID & Password PDF
                            </h3>
                            <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                                User ID ini digunakan sebagai <strong>password</strong> untuk membuka file PDF yang Anda beli di NusaShare.
                            </p>
                            <div class="bg-white border border-amber-200 rounded-2xl px-4 py-3 flex items-center gap-2 shadow-sm">
                                <span class="material-symbols-outlined text-amber-500 text-[18px]">badge</span>
                                <span id="profile-user-id" class="font-mono font-bold text-slate-900 tracking-widest text-sm flex-1"><?= htmlspecialchars($user['id'] ?? '') ?></span>
                                <button
                                    onclick="copyProfileUserId()"
                                    title="Salin User ID"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition-all"
                                >
                                    <span class="material-symbols-outlined text-[18px]" id="profile-copy-icon">content_copy</span>
                                </button>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-3">
                                Jangan bagikan User ID ini kepada orang lain.
                            </p>
                        </div>

                        <div class="card p-6 border-dashed border-2 flex flex-col justify-center items-center text-center">
                            <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center text-slate-400 mb-3">
                                <span class="material-symbols-outlined">security</span>
                            </div>
                            <h3 class="font-bold text-slate-900 mb-1">Keamanan Akun</h3>
                            <p class="text-xs text-slate-500 mb-4">Ingin mengubah kata sandi Anda?</p>
                            <button class="text-sm font-bold text-[#4F46E5] hover:underline">Ubah Kata Sandi</button>
                        </div>
                    </div>
                </div>

            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-slate-200 pt-16 pb-8 mt-auto">
                <div class="max-w-4xl mx-auto px-8">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-6 mb-8">
                        <div class="flex items-center gap-2">
                            <img src="<?= base_url('assets/icon/logonus.png') ?>" alt="Logo N" class="w-8 h-8">
                            <span class="font-bold text-[#0F172A]">NusaShare</span>
                        </div>
                        <div class="flex gap-8 text-sm text-[#64748B]">
                            <a href="#" class="hover:text-[#4F46E5]">Tentang</a>
                            <a href="#" class="hover:text-[#4F46E5]">Kebijakan Privasi</a>
                        </div>
                    </div>
                    <div class="text-center md:text-left text-xs text-[#94A3B8]">
                        ©<?php echo date("Y"); ?> NusaShare.
                    </div>
                </div>
            </footer>

        </div>
    </div>

<!-- ====== CROP MODAL ====== -->
<div id="cropModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-indigo-600">crop</span>
                Sesuaikan Foto Profil
            </h3>
            <button type="button" id="cropCancel" class="text-slate-400 hover:text-slate-700 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div class="p-6">
            <div class="relative bg-slate-100 rounded-xl overflow-hidden" style="height:320px">
                <img id="cropImage" src="" alt="Preview" class="max-w-full block">
            </div>
            <p class="text-xs text-slate-400 mt-3 text-center">Geser dan perbesar untuk menyesuaikan area foto</p>
        </div>

        <div class="flex gap-3 px-6 pb-6 justify-end">
            <button type="button" id="cropCancel2" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition-colors">
                Batal
            </button>
            <button type="button" id="cropSave" class="px-6 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700 transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">check</span>
                Gunakan Foto Ini
            </button>
        </div>
    </div>
</div>

<script>
let cropper = null;

const profileInput  = document.getElementById('profileImageInput');
const cropModal     = document.getElementById('cropModal');
const cropImageEl   = document.getElementById('cropImage');
const cropSaveBtn   = document.getElementById('cropSave');
const cropCancelBtn = document.getElementById('cropCancel');
const cropCancelBtn2 = document.getElementById('cropCancel2');
const previewContainer = document.getElementById('avatarPreviewContainer');
const hiddenInput   = document.getElementById('profileImageCropped');

// Open crop modal when user picks a file
profileInput.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
        cropImageEl.src = e.target.result;
        cropModal.classList.remove('hidden');
        cropModal.classList.add('flex');

        // Init or re-init cropper
        if (cropper) { cropper.destroy(); }
        cropper = new Cropper(cropImageEl, {
            aspectRatio: 1,          // square avatar
            viewMode: 1,
            dragMode: 'move',
            autoCropArea: 0.85,
            restore: false,
            guides: true,
            center: true,
            highlight: false,
            cropBoxMovable: true,
            cropBoxResizable: true,
            toggleDragModeOnDblclick: false,
        });
    };
    reader.readAsDataURL(file);
    // Reset input so same file can be repicked
    this.value = '';
});

// Save cropped image
cropSaveBtn.addEventListener('click', function () {
    if (!cropper) return;

    const canvas = cropper.getCroppedCanvas({ width: 400, height: 400 });
    const dataUrl = canvas.toDataURL('image/jpeg', 0.88);

    // Show preview in avatar
    previewContainer.innerHTML = `<img src="${dataUrl}" class="w-full h-full object-cover">`;

    // Store in hidden input for form submission
    hiddenInput.value = dataUrl;

    closeCropModal();
});

// Cancel handlers
[cropCancelBtn, cropCancelBtn2].forEach(btn => {
    btn.addEventListener('click', closeCropModal);
});
// Click outside modal
cropModal.addEventListener('click', function (e) {
    if (e.target === cropModal) closeCropModal();
});

function closeCropModal() {
    cropModal.classList.add('hidden');
    cropModal.classList.remove('flex');
    if (cropper) { cropper.destroy(); cropper = null; }
}

function copyProfileUserId() {
    const id = document.getElementById('profile-user-id').textContent.trim();
    navigator.clipboard.writeText(id).then(() => {
        const icon = document.getElementById('profile-copy-icon');
        icon.textContent = 'check';
        icon.style.color = '#f59e0b';
        setTimeout(() => {
            icon.textContent = 'content_copy';
            icon.style.color = '';
        }, 2000);
    });
}
</script>
<script>
    window.nusaAppData = { baseUrl: '<?= base_url() ?>/' };
</script>
<script src="<?= base_url('assets/js/notifications.js') ?>"></script>
</body>
</html>

