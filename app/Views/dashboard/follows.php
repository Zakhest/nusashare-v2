<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Mengikuti - NusaShare</title>
    
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
        .sidebar-link.active { background-color: #EEF2FF; color: #4F46E5; border-right: 4px solid #4F46E5; }

        @media (max-width: 640px) {
            .follow-page-title {
                font-size: 1.55rem;
                line-height: 1.15;
            }

            .follow-update-card {
                border-radius: 1.25rem;
                padding: 1rem;
            }

            .follow-update-row {
                gap: 0.875rem;
                align-items: flex-start;
            }

            .follow-cover {
                width: 5.25rem;
                height: 7rem;
                border-radius: 1rem;
            }

            .follow-meta {
                gap: 0.375rem;
                margin-bottom: 0.5rem;
            }

            .follow-title {
                font-size: 0.98rem;
                line-height: 1.25;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            .follow-desc {
                font-size: 0.78rem;
                line-height: 1.45;
                margin-bottom: 0.75rem;
            }

            .follow-card-footer {
                align-items: stretch;
                flex-direction: column;
                gap: 0.65rem;
            }

            .follow-read-link {
                width: 100%;
                justify-content: center;
                border-radius: 0.85rem;
                background: #EEF2FF;
                padding: 0.65rem 0.75rem;
                line-height: 1;
            }
        }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <?= view('dashboard/_sidebar', [
        'activePage'     => 'follows',
        'user'           => $user,
        'creatorProfile' => $creatorProfile
    ]) ?>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-full overflow-hidden bg-slate-50">
        <!-- Top Nav -->
        <header class="bg-white border-b border-slate-200 px-4 sm:px-8 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4 lg:hidden">
                <img src="<?= base_url('assets/icon/logonus.png') ?>" alt="Logo" class="w-8 h-8">
            </div>
            <h2 class="text-lg font-bold text-slate-900 hidden lg:block">Update Kreator</h2>
            <h2 class="text-base font-bold text-slate-900 lg:hidden">Ikuti</h2>
            
            <div class="flex items-center gap-3 md:gap-6">
                <?php 
                    $creditModel = new \App\Models\CreditModel();
                    $userCredit = $creditModel->find(session()->get('userId'));
                    $balance = $userCredit ? $userCredit['balance'] : 0;
                ?>
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
                <a href="<?= base_url('topup') ?>" class="bg-indigo-50 px-3 py-1.5 rounded-full flex items-center gap-2 border border-indigo-100 hover:bg-indigo-100 transition-colors">
                    <span class="material-symbols-outlined text-indigo-600 text-lg">account_balance_wallet</span>
                    <span class="text-xs font-bold text-indigo-900"><?= number_format($balance) ?> CC</span>
                </a>
                <div class="hidden md:flex items-center gap-3 border-l pl-6 border-slate-100">
                    <div class="text-right">
                        <p class="text-xs font-bold text-slate-900"><?= $profile['display_name'] ?? $user['username'] ?></p>
                        <p class="text-[10px] text-slate-500">Anggota NusaShare</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-[#4F46E5] font-bold overflow-hidden">
                        <?php if (!empty($profile['profile_image'])): ?>
                            <img src="<?= profile_url($profile['profile_image']) ?>" alt="Avatar" class="w-full h-full object-cover">
                        <?php else: ?>
                            <?= strtoupper(substr($user['username'], 0, 1)) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Body -->
        <div class="flex-1 overflow-y-auto p-4 md:p-8 pb-24 lg:pb-8">
            
            <!-- Header -->
            <div class="mb-6 md:mb-10">
                <h1 class="follow-page-title text-3xl font-black text-slate-900 mb-2">Terhubung dengan Kreator</h1>
                <p class="text-sm md:text-base text-slate-500">Jangan lewatkan karya terbaru dari mereka yang kamu ikuti.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                <!-- Main Feed -->
                <div class="lg:col-span-2 space-y-8">
                    <?php if (empty($updates)): ?>
                        <div class="bg-white rounded-3xl p-12 text-center border border-slate-100 shadow-sm">
                            <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center text-indigo-600 mx-auto mb-6">
                                <span class="material-symbols-outlined text-4xl">person_add</span>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-2">Belum ada update</h3>
                            <p class="text-slate-500 mb-8 max-w-sm mx-auto">Mulai ikuti kreator favoritmu untuk mendapatkan update karya terbaru mereka di sini.</p>
                            <a href="<?= base_url('explore') ?>" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-8 py-3 rounded-2xl font-bold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100">
                                Cari Kreator
                                <span class="material-symbols-outlined text-sm">explore</span>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="space-y-6">
                            <?php foreach ($updates as $work): ?>
                                <div class="follow-update-card bg-white rounded-3xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition-all group">
                                    <div class="follow-update-row flex gap-6">
                                        <!-- Work Cover -->
                                        <div class="follow-cover w-24 h-32 md:w-32 md:h-44 bg-slate-100 rounded-2xl overflow-hidden shrink-0 shadow-sm">
                                            <img src="<?= base_url('image/cover/' . $work['id']) ?>" alt="<?= esc($work['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" decoding="async">
                                        </div>
                                        
                                        <div class="min-w-0 flex-1 flex flex-col">
                                            <div class="follow-meta flex flex-wrap items-center gap-3 mb-3">
                                                <div class="w-6 h-6 rounded-full bg-slate-200 overflow-hidden">
                                                    <?php if (!empty($work['profile_image'])): ?>
                                                        <img src="<?= profile_url($work['profile_image']) ?>" alt="" class="w-full h-full object-cover">
                                                    <?php else: ?>
                                                        <div class="w-full h-full flex items-center justify-center bg-indigo-100 text-indigo-600 text-[10px] font-bold">
                                                            <?= strtoupper(substr($work['creator_name'], 0, 1)) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <span class="min-w-0 truncate text-xs font-bold text-slate-600"><?= esc($work['creator_name']) ?></span>
                                                <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                                                <span class="text-[10px] text-slate-400 font-medium uppercase tracking-tighter"><?= date('d M Y', strtotime($work['created_at'])) ?></span>
                                            </div>
                                            
                                            <h3 class="follow-title text-lg md:text-xl font-black text-slate-900 mb-2 line-clamp-1 group-hover:text-indigo-600 transition-colors">
                                                <?= esc($work['title']) ?>
                                            </h3>
                                            
                                            <p class="follow-desc text-slate-500 text-sm line-clamp-2 md:line-clamp-3 mb-4 leading-relaxed break-words">
                                                <?= esc($work['description']) ?>
                                            </p>
                                            
                                            <div class="follow-card-footer mt-auto flex items-center justify-between">
                                                <div class="flex items-center gap-4">
                                                    <div class="flex items-center gap-1 text-slate-400">
                                                        <span class="material-symbols-outlined text-sm">visibility</span>
                                                        <span class="text-[10px] font-bold"><?= number_format($work['view_count'] ?? 0) ?></span>
                                                    </div>
                                                    <div class="flex items-center gap-1 text-slate-400">
                                                        <span class="material-symbols-outlined text-sm">favorite</span>
                                                        <span class="text-[10px] font-bold"><?= number_format($work['like_count'] ?? 0) ?></span>
                                                    </div>
                                                </div>
                                                
                                                <a href="<?= base_url('works/' . $work['id']) ?>" class="follow-read-link text-indigo-600 text-sm font-bold flex items-center gap-1 hover:gap-2 transition-all">
                                                    Baca Sekarang
                                                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar List of Followed Creators -->
                <div class="space-y-6">
                    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
                        <h4 class="font-black text-slate-900 mb-6 flex items-center gap-2">
                            <span class="material-symbols-outlined text-indigo-600">group</span>
                            Kreator Diikuti
                        </h4>
                        
                        <?php if (empty($followed)): ?>
                            <p class="text-xs text-slate-400 italic py-4">Kamu belum mengikuti siapapun.</p>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach ($followed as $creator): ?>
                                    <div class="flex items-center justify-between group">
                                        <a href="<?= base_url('creator/' . $creator['followed_id']) ?>" class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-slate-100 overflow-hidden border border-slate-200 group-hover:border-indigo-200 transition-all">
                                                <?php if (!empty($creator['profile_image'])): ?>
                                                    <img src="<?= profile_url($creator['profile_image']) ?>" alt="" class="w-full h-full object-cover">
                                                <?php else: ?>
                                                    <div class="w-full h-full flex items-center justify-center bg-indigo-50 text-indigo-600 font-bold text-sm">
                                                        <?= strtoupper(substr($creator['display_name'] ?? 'U', 0, 1)) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-slate-900 group-hover:text-indigo-600 transition-colors"><?= esc($creator['display_name']) ?></p>
                                                <p class="text-[10px] text-slate-400 font-medium">Lihat Profil</p>
                                            </div>
                                        </a>
                                        
                                        <button onclick="unfollow('<?= $creator['followed_id'] ?>', this)" class="p-2 text-slate-300 hover:text-red-500 transition-colors" title="Batal Ikuti">
                                            <span class="material-symbols-outlined text-lg">person_remove</span>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script>
    async function unfollow(creatorId, btn) {
        if (!confirm('Batal mengikuti kreator ini?')) return;
        
        try {
            const response = await fetch(`<?= base_url('follow') ?>/${creatorId}/remove`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                }
            });
            
            const data = await response.json();
            if (data.status === 'success') {
                location.reload();
            } else {
                alert(data.message || 'Gagal batal mengikuti.');
            }
        } catch (e) {
            console.error(e);
            alert('Terjadi kesalahan sistem.');
        }
    }
    </script>
    <script>
        window.nusaAppData = { baseUrl: '<?= base_url() ?>/' };
    </script>
    <script src="<?= base_url('assets/js/notifications.js') ?>"></script>
</body>
</html>
