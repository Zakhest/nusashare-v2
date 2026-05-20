<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= $title ?? 'Profil - NusaShare' ?></title>
    
    <!-- Fonts: Inter -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Material Icons -->
    <link rel="icon" href="<?= base_url('assets/icon/logonus.png') ?>" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F8FAFC; color: #0F172A; }
        .art-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .art-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.08);
            border-color: #CBD5E1;
        }
        .btn-primary {
            background: linear-gradient(135deg, #4F46E5, #22D3EE);
            color: white;
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="<?= ($isLoggedIn ?? false) ? 'flex h-screen overflow-hidden' : '' ?>">

    <?php if ($isLoggedIn ?? false): ?>
        <?= view('dashboard/_sidebar', [
            'activePage'     => '',
            'user'           => $user,
            'creatorProfile' => $creatorProfile ?? []
        ]) ?>
    <?php endif; ?>

    <!-- Main Wrapper -->
    <div class="<?= ($isLoggedIn ?? false) ? 'flex-1 flex flex-col h-full overflow-hidden' : '' ?>">
        
        <!-- Navbar -->
        <nav class="<?= ($isLoggedIn ?? false) ? 'bg-white border-b border-slate-200 px-8 py-4 flex items-center justify-between sticky top-0 z-40' : 'fixed top-0 w-full z-40 bg-white/80 backdrop-blur-md border-b border-slate-200' ?>">
            <div class="<?= ($isLoggedIn ?? false) ? 'w-full flex items-center justify-between' : 'max-w-7xl mx-auto px-4 md:px-6 h-16 flex items-center justify-between' ?>">
                <div class="flex items-center gap-6">
                    <a href="<?= base_url('explore') ?>" class="flex items-center gap-2 group">
                        <img src="<?= base_url('assets/icon/logonus.png') ?>" alt="Logo N" class="w-8 h-8"/>
                        <span class="font-bold text-lg text-[#0F172A] tracking-tight">NusaShare</span>
                    </a>
                </div>
                
                <div class="flex items-center gap-4">
                    <?php if (!($isLoggedIn ?? false)): ?>
                        <a href="<?= base_url('login') ?>" class="btn-primary px-5 py-2 rounded-full text-sm font-medium shadow-sm">Masuk</a>
                    <?php else: ?>
                        <?php 
                            $creditModel = new \App\Models\CreditModel();
                            $userCredit = $creditModel->find(session()->get('userId'));
                            $balance = $userCredit ? $userCredit['balance'] : 0;
                        ?>
                        <a href="<?= base_url('topup') ?>" class="bg-indigo-50 px-3 py-1.5 rounded-full flex items-center gap-2 border border-indigo-100 hover:bg-indigo-100 transition-colors mr-2">
                            <span class="material-symbols-outlined text-indigo-600 text-lg">account_balance_wallet</span>
                            <span class="text-xs font-bold text-indigo-900"><?= number_format($balance) ?> CC</span>
                        </a>
                        <div class="text-right hidden sm:block">
                            <p class="text-xs font-bold text-slate-900"><?= $username ?></p>
                            <p class="text-[10px] text-slate-500">Mode User</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </nav>

        <!-- Profile Content -->
        <div class="flex-1 overflow-y-auto pt-24 pb-12 px-6">
            <div class="max-w-5xl mx-auto">
                <!-- Header Card -->
                <div class="bg-white rounded-[32px] shadow-sm border border-slate-100 p-8 md:p-12 mb-12 relative overflow-hidden">
                    <!-- Background Decor -->
                    <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-50/50 rounded-full -mr-32 -mt-32 blur-3xl"></div>
                    
                    <div class="flex flex-col md:flex-row items-center md:items-start gap-8 relative z-10">
                        <div class="w-32 h-32 rounded-[40px] bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center text-white text-4xl font-black shadow-xl shrink-0 overflow-hidden">
                            <?php if (!empty($profile['profile_image'])): ?>
                                <img src="/image-nusashare/profile/<?= $profile['profile_image'] ?>" alt="Avatar" class="w-full h-full object-cover">
                            <?php else: ?>
                                <?= strtoupper(substr($profile['display_name'], 0, 1)) ?>
                            <?php endif; ?>
                        </div>
                        
                        <div class="text-center md:text-left flex-1">
                            <div class="flex flex-col md:flex-row md:items-center gap-3 mb-4">
                                <h1 class="text-3xl font-black text-slate-900"><?= esc($profile['display_name']) ?></h1>
                                <?php if (($targetUser['role'] ?? 'user') === 'creator'): ?>
                                    <span class="inline-flex items-center px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-[10px] font-bold uppercase tracking-widest border border-indigo-100">
                                        OFFICIAL KREATOR
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <p class="text-slate-600 leading-relaxed max-w-2xl text-lg italic">
                                "<?= esc($profile['bio'] ?: 'Belum ada bio.') ?>"
                            </p>
                            
                            <div class="flex flex-wrap justify-center md:justify-start gap-6 mt-8">
                                <div class="text-center md:text-left">
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Followers</p>
                                    <p id="followerCount" class="text-xl font-black text-slate-900">...</p>
                                </div>
                                <div class="text-center md:text-left">
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Total Readers</p>
                                    <p id="readerCount" class="text-xl font-black text-slate-900">...</p>
                                </div>
                                <div class="text-center md:text-left">
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Karya</p>
                                    <p class="text-xl font-black text-slate-900"><?= count($works ?? []) ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="shrink-0 mt-4 md:mt-0">
                            <?php if (($targetUser['id'] ?? '') !== (session()->get('userId') ?? '')): ?>
                                <button 
                                    id="followBtn" 
                                    data-creator-id="<?= $targetUser['id'] ?>"
                                    data-is-following="<?= $isFollowing ? 'true' : 'false' ?>"
                                    class="<?= $isFollowing ? 'bg-slate-100 text-slate-600' : 'btn-primary text-white shadow-lg shadow-indigo-100' ?> px-8 py-3 rounded-2xl font-bold text-sm flex items-center gap-2 transition-all active:scale-95"
                                >
                                    <span class="material-symbols-outlined text-lg"><?= $isFollowing ? 'check' : 'add' ?></span>
                                    <span class="btn-text"><?= $isFollowing ? 'Diikuti' : 'Follow' ?></span>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Works Grid -->
                <div>
                    <h2 class="text-xl font-black text-slate-900 mb-8 flex items-center gap-3">
                        <span class="material-symbols-outlined text-indigo-500">palette</span>
                        Portfolio Karya
                    </h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                        <?php if (!empty($works)): ?>
                            <?php foreach ($works as $work): ?>
                                <?php 
                                    $coverUrl = base_url('image/cover/' . $work['id']);
                                    if (empty($work['cover_url'])) {
                                        $coverUrl = base_url('assets/img/default-cover.jpg');
                                    }
                                ?>

                                <a href="<?= base_url('works/' . $work['id']) ?>" class="art-card group cursor-pointer block">
                                    <div class="relative aspect-[4/3] bg-slate-100 overflow-hidden border-b border-slate-100">
                                        <img src="<?= $coverUrl ?>" alt="<?= esc($work['title']) ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy">
                                    </div>
                                    <div class="p-4">
                                        <h3 class="font-bold text-slate-900 leading-tight mb-2 truncate group-hover:text-indigo-600 transition-colors"><?= esc($work['title']) ?></h3>
                                        <div class="flex items-center justify-between">
                                            <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400 italic"><?= esc($work['content_type']) ?></span>
                                            <div class="flex items-center gap-1 text-[10px] text-slate-400">
                                                <span class="material-symbols-outlined text-[12px]">visibility</span>
                                                <?= number_format($work['view_count'] ?? 0) ?>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-span-full py-20 text-center bg-white rounded-[32px] border border-slate-100">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                                    <span class="material-symbols-outlined text-3xl">folder_off</span>
                                </div>
                                <h3 class="text-slate-900 font-bold mb-1">Belum ada karya</h3>
                                <p class="text-slate-400 text-sm">Eksperiman atau karya terbaru akan muncul di sini.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-white border-t border-slate-100 py-12">
            <div class="max-w-5xl mx-auto px-6 text-center">
                <div class="flex items-center justify-center gap-2 mb-6">
                    <img src="<?= base_url('assets/icon/logonus.png') ?>" alt="Logo N" class="w-6 h-6">
                    <span class="font-bold text-slate-900">NusaShare</span>
                </div>
                <p class="text-slate-400 text-xs">©<?= date("Y") ?> NusaShare. Platform Kreator Independen Indonesia.</p>
            </div>
        </footer>
    </div>

    <script>
    const creatorId = '<?= $targetUser['id'] ?>';
    const followerCountEl = document.getElementById('followerCount');
    const readerCountEl = document.getElementById('readerCount');

    async function fetchStats() {
        try {
            const response = await fetch(`<?= base_url('follow') ?>/${creatorId}/stats`);
            const data = await response.json();
            if (data.status === 'success') {
                followerCountEl.textContent = data.followers.toLocaleString();
                readerCountEl.textContent = data.readers.toLocaleString();
            }
        } catch (e) {
            console.error('Failed to fetch stats:', e);
        }
    }

    // Initial fetch
    fetchStats();

    const followBtn = document.getElementById('followBtn');
    if (followBtn) {
        followBtn.addEventListener('click', async () => {
            const creatorId = followBtn.dataset.creatorId;
            const isFollowing = followBtn.dataset.isFollowing === 'true';
            const url = isFollowing 
                ? `<?= base_url('follow') ?>/${creatorId}/remove`
                : `<?= base_url('follow') ?>/${creatorId}`;
            
            try {
                // Disable button during request
                followBtn.disabled = true;
                followBtn.style.opacity = '0.5';

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                    }
                });
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    // Toggle state
                    const newIsFollowing = !isFollowing;
                    followBtn.dataset.isFollowing = newIsFollowing;
                    
                    // Update UI
                    const icon = followBtn.querySelector('.material-symbols-outlined');
                    const text = followBtn.querySelector('.btn-text');
                    
                    if (newIsFollowing) {
                        followBtn.className = 'bg-slate-100 text-slate-600 px-8 py-3 rounded-2xl font-bold text-sm flex items-center gap-2 transition-all active:scale-95';
                        icon.textContent = 'check';
                        text.textContent = 'Diikuti';
                    } else {
                        followBtn.className = 'btn-primary text-white shadow-lg shadow-indigo-100 px-8 py-3 rounded-2xl font-bold text-sm flex items-center gap-2 transition-all active:scale-95';
                        icon.textContent = 'add';
                        text.textContent = 'Follow';
                    }

                    // Re-fetch stats to update counter
                    fetchStats();
                } else {
                    alert(data.message || 'Terjadi kesalahan.');
                    if (data.message === 'Silakan login terlebih dahulu.') {
                        window.location.href = '<?= base_url('login') ?>';
                    }
                }
            } catch (e) {
                console.error(e);
                alert('Gagal menghubungkan ke server.');
            } finally {
                followBtn.disabled = false;
                followBtn.style.opacity = '1';
            }
        });
    }
    </script>
</body>
</html>
