<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= $title ?? 'Profil - NusaShare' ?></title>

    <!-- Fonts: Inter -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Material Icons -->
    <link rel="icon" href="<?= base_url('assets/icon/logonus.png') ?>" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; color: #0f172a; }
        .profile-shell {
            background:
                radial-gradient(circle at top left, rgba(79, 70, 229, 0.12), transparent 34rem),
                radial-gradient(circle at 85% 10%, rgba(20, 184, 166, 0.11), transparent 28rem),
                linear-gradient(180deg, #f8fafc 0%, #ffffff 48%, #f8fafc 100%);
        }
        .hero-panel {
            background:
                linear-gradient(135deg, rgba(15, 23, 42, 0.96), rgba(30, 41, 59, 0.94)),
                linear-gradient(135deg, rgba(79, 70, 229, 0.35), rgba(20, 184, 166, 0.25));
            box-shadow: 0 30px 80px -48px rgba(15, 23, 42, 0.75);
        }
        .hero-pattern {
            background-image:
                linear-gradient(rgba(255,255,255,0.08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.08) 1px, transparent 1px);
            background-size: 38px 38px;
            mask-image: linear-gradient(135deg, transparent 0%, black 22%, black 75%, transparent 100%);
        }
        .btn-primary {
            background: linear-gradient(135deg, #4f46e5, #0f766e);
            color: white;
            transition: transform 0.2s ease, box-shadow 0.2s ease, opacity 0.2s ease;
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 16px 30px -18px rgba(79, 70, 229, 0.85); }
        .stat-tile, .art-card { transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease; }
        .stat-tile:hover, .art-card:hover { transform: translateY(-3px); border-color: #cbd5e1; box-shadow: 0 18px 34px -26px rgba(15, 23, 42, 0.45); }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
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

    <?php
        $displayName = $profile['display_name'] ?? ($targetUser['username'] ?? 'User');
        $usernameSlug = $targetUser['username'] ?? '';
        $bio = trim((string)($profile['bio'] ?? ''));
        $role = strtolower((string)($targetUser['role'] ?? 'user'));
        $isCreator = in_array($role, ['creator', 'kreator'], true);
        $workCount = count($works ?? []);
        $profileImage = $profile['profile_image'] ?? null;
        $initial = strtoupper(substr($displayName, 0, 1));
    ?>

    <!-- Main Wrapper -->
    <div class="<?= ($isLoggedIn ?? false) ? 'flex-1 flex flex-col h-full overflow-hidden' : 'min-h-screen' ?>">

        <!-- Navbar -->
        <nav class="<?= ($isLoggedIn ?? false) ? 'bg-white/95 border-b border-slate-200 px-4 md:px-8 py-4 flex items-center justify-between sticky top-0 z-40 backdrop-blur' : 'fixed top-0 w-full z-40 bg-white/85 backdrop-blur-md border-b border-slate-200' ?>">
            <div class="<?= ($isLoggedIn ?? false) ? 'w-full flex items-center justify-between' : 'max-w-7xl mx-auto px-4 md:px-6 h-16 flex items-center justify-between' ?>">
                <a href="<?= base_url('explore') ?>" class="flex items-center gap-2 group">
                    <img src="<?= base_url('assets/icon/logonus.png') ?>" alt="Logo N" class="w-8 h-8"/>
                    <span class="font-extrabold text-lg text-slate-950 tracking-tight">NusaShare</span>
                </a>

                <div class="flex items-center gap-3">
                    <?php if (!($isLoggedIn ?? false)): ?>
                        <a href="<?= base_url('explore') ?>" class="hidden sm:inline-flex px-4 py-2 rounded-full text-sm font-bold text-slate-600 hover:text-slate-950 hover:bg-slate-100 transition-colors">Explore</a>
                        <a href="<?= base_url('login') ?>" class="btn-primary px-5 py-2 rounded-full text-sm font-bold shadow-sm">Masuk</a>
                    <?php else: ?>
                        <?php
                            $creditModel = new \App\Models\CreditModel();
                            $userCredit = $creditModel->find(session()->get('userId'));
                            $balance = $userCredit ? $userCredit['balance'] : 0;
                        ?>
                        <a href="<?= base_url('topup') ?>" class="bg-indigo-50 px-3 py-1.5 rounded-full flex items-center gap-2 border border-indigo-100 hover:bg-indigo-100 transition-colors">
                            <span class="material-symbols-outlined text-indigo-600 text-lg">account_balance_wallet</span>
                            <span class="text-xs font-extrabold text-indigo-950"><?= number_format($balance) ?> CC</span>
                        </a>
                        <div class="text-right hidden sm:block">
                            <p class="text-xs font-extrabold text-slate-900"><?= esc($username ?? '') ?></p>
                            <p class="text-[10px] text-slate-500">Mode User</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </nav>

        <!-- Profile Content -->
        <main class="profile-shell flex-1 overflow-y-auto pt-24 md:pt-28 pb-14 px-4 md:px-8">
            <div class="max-w-6xl mx-auto">
                <!-- Hero -->
                <section class="hero-panel relative overflow-hidden rounded-[28px] md:rounded-[36px] text-white">
                    <div class="hero-pattern absolute inset-0 opacity-70"></div>
                    <div class="relative p-5 sm:p-8 md:p-10 lg:p-12">
                        <div class="flex flex-col lg:flex-row lg:items-end gap-8">
                            <div class="flex flex-col sm:flex-row gap-6 sm:items-center flex-1 min-w-0">
                                <div class="relative shrink-0">
                                    <div class="w-28 h-28 md:w-36 md:h-36 rounded-[30px] bg-white/10 border border-white/20 p-2 shadow-2xl">
                                        <div class="w-full h-full rounded-[24px] bg-gradient-to-br from-white/20 to-white/5 flex items-center justify-center text-white text-5xl font-black overflow-hidden">
                                            <?php if (!empty($profileImage)): ?>
                                                <img src="<?= profile_url($profileImage) ?>" alt="<?= esc($displayName) ?>" class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <?= esc($initial) ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php if ($isCreator): ?>
                                        <div class="absolute -right-2 -bottom-2 w-11 h-11 rounded-2xl bg-amber-300 text-slate-950 flex items-center justify-center border-4 border-slate-900 shadow-lg">
                                            <span class="material-symbols-outlined">verified</span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2 mb-3">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 border border-white/15 text-[11px] font-extrabold uppercase tracking-widest text-white/85">
                                            <span class="material-symbols-outlined text-sm">alternate_email</span>
                                            <?= esc($usernameSlug) ?>
                                        </span>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full <?= $isCreator ? 'bg-amber-300 text-slate-950' : 'bg-teal-300 text-slate-950' ?> text-[11px] font-black uppercase tracking-widest">
                                            <?= $isCreator ? 'Kreator' : 'Member' ?>
                                        </span>
                                    </div>
                                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-black tracking-tight break-words"><?= esc($displayName) ?></h1>
                                    <p class="mt-4 text-white/75 text-base md:text-lg leading-relaxed max-w-2xl">
                                        <?= esc($bio ?: 'Belum ada bio. Karya, koleksi, dan aktivitas kreatif akun ini akan tampil di sini.') ?>
                                    </p>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-3 lg:justify-end">
                                <?php if (($targetUser['id'] ?? '') !== (session()->get('userId') ?? '')): ?>
                                    <button
                                        id="followBtn"
                                        data-creator-id="<?= esc($targetUser['id']) ?>"
                                        data-is-following="<?= $isFollowing ? 'true' : 'false' ?>"
                                        class="<?= $isFollowing ? 'bg-white/15 text-white border border-white/20 hover:bg-white/20' : 'btn-primary text-white shadow-lg shadow-indigo-950/20' ?> px-6 py-3 rounded-2xl font-extrabold text-sm flex items-center gap-2 transition-all active:scale-95"
                                    >
                                        <span class="material-symbols-outlined text-lg"><?= $isFollowing ? 'check' : 'add' ?></span>
                                        <span class="btn-text"><?= $isFollowing ? 'Diikuti' : 'Follow' ?></span>
                                    </button>
                                <?php endif; ?>
                                <a href="#portfolio" class="px-6 py-3 rounded-2xl font-extrabold text-sm flex items-center gap-2 bg-white text-slate-950 hover:bg-slate-100 transition-colors">
                                    <span class="material-symbols-outlined text-lg">grid_view</span>
                                    Lihat Karya
                                </a>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-8">
                            <div class="stat-tile rounded-3xl bg-white/10 border border-white/15 p-5 backdrop-blur">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-xs font-extrabold uppercase tracking-widest text-white/60">Followers</p>
                                    <span class="material-symbols-outlined text-white/45">group</span>
                                </div>
                                <p id="followerCount" class="mt-3 text-3xl font-black"><?= number_format($followerCount ?? 0) ?></p>
                            </div>
                            <div class="stat-tile rounded-3xl bg-white/10 border border-white/15 p-5 backdrop-blur">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-xs font-extrabold uppercase tracking-widest text-white/60">Readers</p>
                                    <span class="material-symbols-outlined text-white/45">visibility</span>
                                </div>
                                <p id="readerCount" class="mt-3 text-3xl font-black"><?= number_format($readerCount ?? 0) ?></p>
                            </div>
                            <div class="stat-tile rounded-3xl bg-white/10 border border-white/15 p-5 backdrop-blur">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-xs font-extrabold uppercase tracking-widest text-white/60">Karya</p>
                                    <span class="material-symbols-outlined text-white/45">palette</span>
                                </div>
                                <p class="mt-3 text-3xl font-black"><?= number_format($workCount) ?></p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Portfolio -->
                <section id="portfolio" class="mt-10 md:mt-12">
                    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-6">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.18em] text-indigo-600">Portfolio</p>
                            <h2 class="text-2xl md:text-3xl font-black text-slate-950 mt-2">Karya dari <?= esc($displayName) ?></h2>
                        </div>
                        <div class="inline-flex self-start md:self-auto items-center gap-2 rounded-full bg-white border border-slate-200 px-4 py-2 text-sm font-bold text-slate-600 shadow-sm">
                            <span class="material-symbols-outlined text-lg text-teal-600">auto_awesome</span>
                            <?= number_format($workCount) ?> karya tersedia
                        </div>
                    </div>

                    <?php if (!empty($works)): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5 md:gap-6">
                            <?php foreach ($works as $work): ?>
                                <?php
                                    $coverUrl = base_url('image/cover/' . $work['id']);
                                    if (empty($work['cover_url'])) {
                                        $coverUrl = base_url('assets/icon/logonus.png');
                                    }

                                    $isPaid = !empty($work['is_paid']);
                                    $displayPrice = (int)($work['purchase_price'] ?? 0);
                                    if ($displayPrice <= 0) {
                                        $displayPrice = (int)($work['price'] ?? 0);
                                    }
                                    $typeLabel = strtoupper((string)($work['content_type'] ?? 'Karya'));
                                ?>

                                <a href="<?= base_url('works/' . $work['id']) ?>" class="art-card group block overflow-hidden rounded-3xl border border-slate-200 bg-white">
                                    <div class="relative aspect-[16/11] bg-slate-100 overflow-hidden">
                                        <img src="<?= $coverUrl ?>" alt="<?= esc($work['title']) ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy">
                                        <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-slate-950/60 to-transparent"></div>
                                        <div class="absolute left-4 top-4 flex flex-wrap gap-2">
                                            <span class="px-3 py-1 rounded-full bg-white/90 text-slate-800 text-[10px] font-black uppercase tracking-widest shadow-sm">
                                                <?= esc($typeLabel) ?>
                                            </span>
                                        </div>
                                        <span class="absolute right-4 top-4 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider shadow-sm <?= $isPaid ? 'bg-amber-300 text-slate-950' : 'bg-emerald-300 text-slate-950' ?>">
                                            <?= $isPaid ? number_format($displayPrice) . ' CC' : 'Gratis' ?>
                                        </span>
                                    </div>
                                    <div class="p-5">
                                        <h3 class="font-black text-slate-950 leading-tight text-lg group-hover:text-indigo-600 transition-colors line-clamp-2"><?= esc($work['title']) ?></h3>
                                        <p class="mt-3 text-sm text-slate-500 line-clamp-2"><?= esc(strip_tags($work['description'] ?? '')) ?></p>
                                        <div class="mt-5 flex items-center justify-between gap-4 border-t border-slate-100 pt-4">
                                            <div class="flex items-center gap-3 text-xs font-bold text-slate-500">
                                                <span class="inline-flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-base">visibility</span>
                                                    <?= number_format($work['view_count'] ?? 0) ?>
                                                </span>
                                                <span class="inline-flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-base">favorite</span>
                                                    <?= number_format($work['like_count'] ?? 0) ?>
                                                </span>
                                            </div>
                                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-slate-950 text-white group-hover:bg-indigo-600 transition-colors">
                                                <span class="material-symbols-outlined text-lg">arrow_forward</span>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="rounded-[28px] border border-dashed border-slate-300 bg-white p-8 md:p-14 text-center shadow-sm">
                            <div class="w-20 h-20 rounded-3xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-5">
                                <span class="material-symbols-outlined text-4xl">folder_off</span>
                            </div>
                            <h3 class="text-xl font-black text-slate-950 mb-2">Belum ada karya</h3>
                            <p class="text-slate-500 text-sm max-w-md mx-auto">Saat akun ini mulai menerbitkan karya, daftar karyanya akan tampil di sini dengan rapi.</p>
                        </div>
                    <?php endif; ?>
                </section>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-slate-100 py-8">
            <div class="max-w-6xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
                <div class="flex items-center justify-center gap-2">
                    <img src="<?= base_url('assets/icon/logonus.png') ?>" alt="Logo N" class="w-6 h-6">
                    <span class="font-extrabold text-slate-950">NusaShare</span>
                </div>
                <p class="text-slate-400 text-xs">&copy;<?= date("Y") ?> NusaShare. Platform kreator independen Indonesia.</p>
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
                    const newIsFollowing = !isFollowing;
                    followBtn.dataset.isFollowing = newIsFollowing;

                    const icon = followBtn.querySelector('.material-symbols-outlined');
                    const text = followBtn.querySelector('.btn-text');

                    if (newIsFollowing) {
                        followBtn.className = 'bg-white/15 text-white border border-white/20 hover:bg-white/20 px-6 py-3 rounded-2xl font-extrabold text-sm flex items-center gap-2 transition-all active:scale-95';
                        icon.textContent = 'check';
                        text.textContent = 'Diikuti';
                    } else {
                        followBtn.className = 'btn-primary text-white shadow-lg shadow-indigo-950/20 px-6 py-3 rounded-2xl font-extrabold text-sm flex items-center gap-2 transition-all active:scale-95';
                        icon.textContent = 'add';
                        text.textContent = 'Follow';
                    }

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
