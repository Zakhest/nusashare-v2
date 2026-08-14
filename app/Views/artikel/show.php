<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($article['title']) ?> - NusaShare</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">
    <link rel="icon" href="<?= base_url('assets/icon/logonus.png') ?>" type="image/x-icon">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F8FAFC;
        }

        /* Parser styles for the article content */
        .article-content {
            font-size: 1.05rem;
            line-height: 1.8;
            color: #334155; /* slate-700 */
        }

        .article-para {
            margin-bottom: 1.5rem;
        }

        /* Infobox Styles */
        .article-infobox {
            margin: 1.5rem 0;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 1.25rem;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }

        /* If center layout or inline on mobile, we can align it nicely */
        @media (min-width: 640px) {
            .article-infobox {
                float: right;
                margin-left: 1.5rem;
                margin-top: 0.5rem;
            }
        }

        .article-infobox-header {
            background-color: #EEF2FF;
            color: #4F46E5;
            padding: 0.75rem 1.25rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border-bottom: 1px solid #E2E8F0;
        }

        .article-infobox-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }

        .article-infobox-table tr {
            border-bottom: 1px solid #F1F5F9;
        }

        .article-infobox-table tr:last-child {
            border-bottom: none;
        }

        .article-infobox-key {
            text-align: left;
            padding: 0.75rem 1.25rem;
            font-weight: 600;
            color: #475569; /* slate-600 */
            width: 35%;
            background-color: #F8FAFC;
        }

        .article-infobox-value {
            padding: 0.75rem 1.25rem;
            color: #1E293B; /* slate-800 */
        }

        /* Table Styles */
        .article-table-wrap {
            width: 100%;
            overflow-x: auto;
            margin: 2rem 0;
            border: 1px solid #E2E8F0;
            border-radius: 1rem;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
        }

        .article-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.95rem;
        }

        .article-table th {
            background-color: #F1F5F9;
            color: #334155;
            font-weight: 700;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #E2E8F0;
        }

        .article-table td {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #F1F5F9;
            color: #475569;
        }

        .article-table tbody tr:hover {
            background-color: #F8FAFC;
        }

        /* Image Layouts */
        .article-img {
            margin: 2rem 0;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .article-img img {
            border-radius: 1.25rem;
            width: 100%;
            object-fit: cover;
            box-shadow: 0 4px 10px -3px rgb(0 0 0 / 0.1);
        }

        .article-img-caption {
            font-size: 0.85rem;
            color: #64748B;
            text-align: center;
            font-style: italic;
        }

        .article-img--center {
            align-items: center;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        .article-img--full {
            max-width: 100%;
        }

        @media (min-width: 768px) {
            .article-img--left {
                float: left;
                width: 45%;
                margin-right: 1.5rem;
                margin-top: 0.5rem;
            }
            .article-img--right {
                float: right;
                width: 45%;
                margin-left: 1.5rem;
                margin-top: 0.5rem;
            }
        }

        .article-parse-error {
            color: #EF4444;
            background-color: #FEF2F2;
            border: 1px solid #FEE2E2;
            padding: 1rem;
            border-radius: 1rem;
            margin: 1.5rem 0;
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Clear floats after content */
        .article-content::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    <!-- Navbar -->
    <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200 py-3 px-4 sm:py-4 sm:px-6">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <a href="<?= base_url('explore') ?>" class="flex items-center gap-2 group">
                <span class="material-symbols-outlined text-slate-400 group-hover:text-indigo-600 transition-colors">arrow_back</span>
                <span class="font-bold text-slate-900 group-hover:text-indigo-600 transition-colors">Kembali Jelajah</span>
            </a>
            <div class="flex items-center gap-4">
                <?php if (session()->get('isLoggedIn')): ?>
                    <?php 
                        $creditModel = new \App\Models\CreditModel();
                        $userCredit = $creditModel->find(session()->get('userId'));
                        $balance = $userCredit ? $userCredit['balance'] : 0;
                    ?>
                    <a href="<?= base_url('topup') ?>" class="bg-indigo-50 px-3 py-1.5 rounded-full flex items-center gap-2 border border-indigo-100 hover:bg-indigo-100 transition-colors mr-2">
                        <span class="material-symbols-outlined text-indigo-600 text-lg">account_balance_wallet</span>
                        <span class="text-xs font-bold text-indigo-900 cc-balance"><?= number_format($balance) ?> CC</span>
                    </a>
                <?php endif; ?>
                <img src="<?= base_url('assets/icon/logonus.png') ?>" alt="Logo" class="w-8 h-8">
                <span class="font-bold text-slate-900 hidden sm:block">NusaShare</span>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <main class="flex-1 max-w-4xl mx-auto w-full px-4 sm:px-6 py-8 sm:py-12">
        <article class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 sm:p-10 mb-8">
            
            <!-- Breadcrumbs / Tag -->
            <div class="flex items-center gap-3 mb-6">
                <span class="inline-block px-3 py-1 rounded-full bg-amber-50 text-amber-600 text-xs font-bold uppercase tracking-wider">
                    Artikel
                </span>
                <?php if (!empty($article['genre'])): ?>
                    <span class="text-xs font-medium text-slate-400">•</span>
                    <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg">
                        <?= esc($article['genre']) ?>
                    </span>
                <?php endif; ?>
            </div>

            <!-- Title -->
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-slate-900 mb-6 leading-tight">
                <?= esc($article['title']) ?>
            </h1>

            <!-- Author & Metadata Meta Header -->
            <div class="flex items-center gap-4 mb-8 pb-6 border-b border-slate-100">
                <a href="<?= base_url('user/' . $article['creator_name']) ?>" class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xl hover:scale-105 transition-transform shrink-0">
                    <?= strtoupper(substr($article['creator_name'], 0, 1)) ?>
                </a>
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] text-slate-400 font-medium uppercase tracking-widest">Kreator</p>
                    <a href="<?= base_url('user/' . $article['creator_name']) ?>" class="font-bold text-slate-900 hover:text-indigo-600 transition-colors block text-sm truncate"><?= esc($article['creator_name']) ?></a>
                </div>
                <div class="text-right shrink-0 text-xs text-slate-400">
                    <p class="font-medium"><?= date('d M Y', strtotime($article['created_at'])) ?></p>
                    <p class="flex items-center gap-1 justify-end mt-0.5">
                        <span class="material-symbols-outlined text-xs">visibility</span>
                        <span id="display-view-count"><?= number_format($article['view_count']) ?></span>
                    </p>
                </div>
            </div>

            <!-- Brief Description/Summary as Blockquote -->
            <?php if (!empty($article['description'])): ?>
                <div class="pl-4 border-l-4 border-slate-300 italic text-slate-500 mb-8 text-base">
                    <?= nl2br(esc($article['description'])) ?>
                </div>
            <?php endif; ?>

            <!-- Article Body -->
            <div class="article-content prose prose-slate max-w-none">
                <?= $article['body_html'] ?>
            </div>

            <!-- Article Interaction Buttons -->
            <div class="flex justify-between items-center mt-12 pt-6 border-t border-slate-100">
                <div class="flex items-center gap-6">
                    <!-- Like Button -->
                    <button id="likeBtn" class="flex items-center gap-2 group transition-all" data-work-id="<?= $article['work_id'] ?>">
                        <div class="w-10 h-10 rounded-full border border-slate-200 flex items-center justify-center group-hover:border-red-200 transition-colors">
                            <span class="material-symbols-outlined <?= $hasLiked ? 'text-red-500 FILL' : 'text-slate-400' ?> group-hover:scale-110 transition-transform" id="likeIcon" style="<?= $hasLiked ? 'font-variation-settings: \'FILL\' 1' : '' ?>">favorite</span>
                        </div>
                        <span class="text-sm font-bold <?= $hasLiked ? 'text-red-500' : 'text-slate-400' ?>" id="likeCount"><?= number_format($likeCount) ?></span>
                    </button>

                    <!-- Wishlist (Bookmark) Button -->
                    <?php if (session()->get('isLoggedIn')): ?>
                        <button id="wishlistBtn" class="flex items-center gap-2 group transition-all" data-work-id="<?= $article['work_id'] ?>" data-bookmarked="<?= $hasBookmarked ? '1' : '0' ?>">
                            <div class="w-10 h-10 rounded-full border border-slate-200 flex items-center justify-center group-hover:border-indigo-200 transition-colors">
                                <span class="material-symbols-outlined <?= $hasBookmarked ? 'text-indigo-600' : 'text-slate-400' ?> group-hover:scale-110 transition-transform" id="wishlistIcon" style="<?= $hasBookmarked ? "font-variation-settings: 'FILL' 1" : '' ?>">bookmark</span>
                            </div>
                            <span class="text-sm font-bold <?= $hasBookmarked ? 'text-indigo-600' : 'text-slate-400' ?>" id="wishlistLabel"><?= $hasBookmarked ? 'Tersimpan' : 'Simpan' ?></span>
                        </button>
                    <?php endif; ?>
                </div>

                <button id="shareBtn" class="flex items-center gap-2 group transition-all text-slate-400 hover:text-indigo-600">
                    <span class="material-symbols-outlined text-lg group-hover:scale-110 transition-transform">share</span>
                    <span class="text-sm font-bold">Bagikan</span>
                </button>
            </div>
        </article>

        <!-- Discussion Section -->
        <section id="comments-section" class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 sm:p-10">
            <h2 class="text-2xl font-bold text-slate-900 mb-8 flex items-center gap-2">
                <span class="material-symbols-outlined text-indigo-600">forum</span>
                Diskusi (<?= count($comments) ?>)
            </h2>

            <?php if (session()->get('isLoggedIn')): ?>
                <form action="<?= base_url("works/{$article['work_id']}/comment") ?>" method="POST" class="mb-12">
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 focus-within:border-indigo-500 focus-within:bg-white transition-all">
                        <textarea name="content" placeholder="Tulis komentar anda..." class="w-full bg-transparent border-none focus:ring-0 text-slate-700 min-h-[100px] resize-none outline-none" required></textarea>
                    </div>
                    <div class="flex justify-end mt-4">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-md shadow-indigo-100 transition-all">Kirim Komentar</button>
                    </div>
                </form>
            <?php else: ?>
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-8 text-center mb-12">
                    <p class="text-slate-600 mb-4">Anda harus login untuk ikut berdiskusi.</p>
                    <a href="<?= base_url('login') ?>" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-all shadow-md">Masuk Sekarang</a>
                </div>
            <?php endif; ?>

            <div class="space-y-6">
                <?php if (!empty($comments)): ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="flex gap-4 group">
                            <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold shrink-0">
                                <?= strtoupper(substr($comment['username'], 0, 1)) ?>
                            </div>
                            <div class="flex-1">
                                <div class="bg-slate-50 p-4 rounded-2xl rounded-tl-none relative border border-slate-100">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <span class="font-bold text-slate-900 text-sm"><?= esc($comment['username']) ?></span>
                                            <?php if ($comment['role'] === 'creator'): ?>
                                                <span class="px-1.5 py-0.5 rounded bg-indigo-100 text-indigo-600 text-[8px] font-black uppercase ml-1">Kreator</span>
                                            <?php endif; ?>
                                        </div>
                                        <span class="text-[10px] text-slate-400"><?= date('d M Y, H:i', strtotime($comment['created_at'])) ?></span>
                                    </div>
                                    <div class="text-sm text-slate-600 leading-relaxed">
                                        <?= nl2br(esc($comment['content'])) ?>
                                    </div>

                                    <?php if (session()->get('userId') === $comment['user_id']): ?>
                                        <form action="<?= base_url("comments/{$comment['id']}/delete") ?>" method="POST" class="absolute -right-2 -top-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button type="submit" class="w-8 h-8 rounded-full bg-white shadow-md border border-slate-100 flex items-center justify-center text-red-500 hover:bg-red-50" onclick="return confirm('Hapus komentar?')">
                                                <span class="material-symbols-outlined text-sm">delete</span>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="py-12 text-center text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                        <span class="material-symbols-outlined text-4xl mb-2">chat_bubble_outline</span>
                        <p>Belum ada komentar. Jadi yang pertama!</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="py-12 bg-white border-t border-slate-200 mt-auto">
        <div class="max-w-7xl mx-auto px-6 text-center text-slate-400 text-sm">
            &copy; <?= date('Y') ?> NusaShare. Platform Kreator Indonesia.
        </div>
    </footer>

    <!-- Toast Notification -->
    <div id="wishlist-toast" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[9999] flex items-center gap-3 bg-slate-900 text-white px-6 py-3.5 rounded-2xl shadow-2xl text-sm font-semibold opacity-0 pointer-events-none transition-all duration-300 translate-y-4">
        <span class="material-symbols-outlined text-lg" id="toast-icon">bookmark</span>
        <span id="toast-message">Berhasil!</span>
    </div>

    <!-- JS Scripts -->
    <script>
        window.nusaAppData = {
            baseUrl: '<?= base_url() ?>/',
            isLoggedIn: <?= session()->get('isLoggedIn') ? 'true' : 'false' ?>
        };
    </script>

    <!-- Like/Wishlist/Views/Share Logic -->
    <script>
    (function () {
        const BASE_URL = '<?= rtrim(base_url(), '/') ?>';
        const WORK_ID  = <?= (int)$article['work_id'] ?>;

        function showToast(message, icon, color) {
            const toast = document.getElementById('wishlist-toast');
            const toastIcon = document.getElementById('toast-icon');
            const toastMsg  = document.getElementById('toast-message');
            toastIcon.textContent = icon || 'bookmark';
            toastMsg.textContent  = message;
            toast.style.background = color || '#0F172A';
            
            toast.classList.remove('opacity-0', 'translate-y-4', 'pointer-events-none');
            toast.classList.add('opacity-100', 'translate-y-0');
            
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-4', 'pointer-events-none');
                toast.classList.remove('opacity-100', 'translate-y-0');
            }, 3000);
        }

        // Bookmark (Wishlist) System
        function syncWishlistUI(isBookmarked) {
            const btn   = document.getElementById('wishlistBtn');
            const icon  = document.getElementById('wishlistIcon');
            const label = document.getElementById('wishlistLabel');

            if (btn) btn.dataset.bookmarked = isBookmarked ? '1' : '0';
            if (icon) {
                if (isBookmarked) {
                    icon.style.fontVariationSettings = "'FILL' 1";
                    icon.classList.remove('text-slate-400');
                    icon.classList.add('text-indigo-600');
                } else {
                    icon.style.fontVariationSettings = "'FILL' 0";
                    icon.classList.remove('text-indigo-600');
                    icon.classList.add('text-slate-400');
                }
            }
            if (label) {
                label.textContent = isBookmarked ? 'Tersimpan' : 'Simpan';
                if (isBookmarked) {
                    label.classList.remove('text-slate-400');
                    label.classList.add('text-indigo-600');
                } else {
                    label.classList.remove('text-indigo-600');
                    label.classList.add('text-slate-400');
                }
            }
        }

        async function toggleWishlist(workId) {
            const btnEl = document.getElementById('wishlistBtn');
            if (!btnEl) return;
            const isCurrentlyBookmarked = (btnEl.dataset.bookmarked === '1');

            syncWishlistUI(!isCurrentlyBookmarked);

            const url = isCurrentlyBookmarked
                ? BASE_URL + '/bookmark/' + workId + '/remove'
                : BASE_URL + '/bookmark/' + workId;

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();

                if (data.status === 'success') {
                    if (!isCurrentlyBookmarked) {
                        showToast('Berhasil disimpan ke Wishlist!', 'bookmark_added', '#4F46E5');
                    } else {
                        showToast('Dihapus dari Wishlist.', 'bookmark_remove', '#64748B');
                    }
                } else {
                    syncWishlistUI(isCurrentlyBookmarked);
                    showToast(data.message || 'Terjadi kesalahan.', 'error', '#EF4444');
                }
            } catch (err) {
                syncWishlistUI(isCurrentlyBookmarked);
                showToast('Gagal terhubung. Coba lagi.', 'wifi_off', '#EF4444');
            }
        }

        // Like System
        let isLiking = false;
        async function toggleLike(workId) {
            if (isLiking) return;
            if (!window.nusaAppData.isLoggedIn) {
                showToast('Silakan login terlebih dahulu untuk menyukai.', 'lock', '#EF4444');
                return;
            }

            isLiking = true;
            const likeIcon = document.getElementById('likeIcon');
            const likeCountEl = document.getElementById('likeCount');
            
            const hasLiked = likeIcon.classList.contains('text-red-500');
            const currentCount = parseInt(likeCountEl.textContent.replace(/,/g, '')) || 0;

            // Optimistic update
            if (hasLiked) {
                likeIcon.classList.remove('text-red-500');
                likeIcon.classList.add('text-slate-400');
                likeIcon.style.fontVariationSettings = "'FILL' 0";
                likeCountEl.textContent = (currentCount - 1).toLocaleString();
                likeCountEl.classList.remove('text-red-500');
                likeCountEl.classList.add('text-slate-400');
            } else {
                likeIcon.classList.remove('text-slate-400');
                likeIcon.classList.add('text-red-500');
                likeIcon.style.fontVariationSettings = "'FILL' 1";
                likeCountEl.textContent = (currentCount + 1).toLocaleString();
                likeCountEl.classList.remove('text-slate-400');
                likeCountEl.classList.add('text-red-500');
            }

            try {
                const res = await fetch(BASE_URL + '/works/' + workId + '/like', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();

                if (data.status === 'success') {
                    // Update exact count from response
                    likeCountEl.textContent = parseInt(data.likes).toLocaleString();
                } else {
                    // Revert
                    if (hasLiked) {
                        likeIcon.classList.remove('text-slate-400');
                        likeIcon.classList.add('text-red-500');
                        likeIcon.style.fontVariationSettings = "'FILL' 1";
                        likeCountEl.textContent = currentCount.toLocaleString();
                        likeCountEl.classList.remove('text-slate-400');
                        likeCountEl.classList.add('text-red-500');
                    } else {
                        likeIcon.classList.remove('text-red-500');
                        likeIcon.classList.add('text-slate-400');
                        likeIcon.style.fontVariationSettings = "'FILL' 0";
                        likeCountEl.textContent = currentCount.toLocaleString();
                        likeCountEl.classList.remove('text-red-500');
                        likeCountEl.classList.add('text-slate-400');
                    }
                    showToast(data.message || 'Terjadi kesalahan.', 'error', '#EF4444');
                }
            } catch (err) {
                // Revert on network error
                if (hasLiked) {
                    likeIcon.classList.remove('text-slate-400');
                    likeIcon.classList.add('text-red-500');
                    likeIcon.style.fontVariationSettings = "'FILL' 1";
                    likeCountEl.textContent = currentCount.toLocaleString();
                    likeCountEl.classList.remove('text-slate-400');
                    likeCountEl.classList.add('text-red-500');
                } else {
                    likeIcon.classList.remove('text-red-500');
                    likeIcon.classList.add('text-slate-400');
                    likeIcon.style.fontVariationSettings = "'FILL' 0";
                    likeCountEl.textContent = currentCount.toLocaleString();
                    likeCountEl.classList.remove('text-red-500');
                    likeCountEl.classList.add('text-slate-400');
                }
            } finally {
                isLiking = false;
            }
        }

        // Share button
        const shareBtn = document.getElementById('shareBtn');
        if (shareBtn) {
            shareBtn.addEventListener('click', async () => {
                if (navigator.share) {
                    try {
                        await navigator.share({
                            title: '<?= esc($article['title']) ?>',
                            text: 'Baca artikel "<?= esc($article['title']) ?>" oleh <?= esc($article['creator_name']) ?> di NusaShare.',
                            url: window.location.href
                        });
                    } catch (err) {
                        // ignore or fallback
                    }
                } else {
                    navigator.clipboard.writeText(window.location.href);
                    showToast('Link berhasil disalin ke clipboard!', 'link', '#10B981');
                }
            });
        }

        // View recording system (AJAX trigger after 120 seconds)
        let timerStarted = false;
        function recordViewCount() {
            setTimeout(async () => {
                try {
                    const res = await fetch(BASE_URL + '/works/' + WORK_ID + '/view', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await res.json();
                    if (data.status === 'success') {
                        const viewCountEl = document.getElementById('display-view-count');
                        if (viewCountEl) {
                            viewCountEl.textContent = parseInt(data.view_count).toLocaleString();
                        }
                    }
                } catch(e) {
                    // fail silently
                }
            }, 120000); // 2 minutes
        }

        document.addEventListener('DOMContentLoaded', () => {
            const wishlistBtn = document.getElementById('wishlistBtn');
            const likeBtn = document.getElementById('likeBtn');

            if (wishlistBtn) wishlistBtn.addEventListener('click', () => toggleWishlist(WORK_ID));
            if (likeBtn) likeBtn.addEventListener('click', () => toggleLike(WORK_ID));
            
            recordViewCount();
        });

    })();
    </script>
</body>
</html>
