<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($work['title']) ?> - NusaShare</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">
    <link rel="icon" href="<?= base_url('assets/icon/logonus.png') ?>" type="image/x-icon">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="stylesheet" href="<?= base_url('assets/css/works.css') ?>">
</head>
<body class="min-h-screen flex flex-col">

    <!-- Navbar Minimal -->
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

    <main class="flex-1 max-w-5xl mx-auto w-full px-4 sm:px-6 py-8 sm:py-12">
        <!-- Hero Section -->
        <div class="grid md:grid-cols-3 gap-6 sm:gap-10 md:gap-12 items-start mb-10 sm:mb-16">
            <div class="md:col-span-1">
                <div class="aspect-[3/4] max-w-[240px] sm:max-w-none mx-auto rounded-2xl overflow-hidden shadow-xl sm:shadow-2xl border-4 border-white">
                    <?php 
                        $coverUrl = base_url('image/cover/' . $work['id']);
                        if (empty($work['cover_url'])) {
                            $coverUrl = base_url('assets/icon/logonuss.png');
                        }
                    ?>
                    <img src="<?= $coverUrl ?>" alt="Cover" class="w-full h-full object-cover" loading="eager" decoding="async" draggable="false" oncontextmenu="return false;">
                </div>
            </div>
            <div class="md:col-span-2">
                <div class="flex items-center gap-3 mb-4">
                    <?php 
                        $typeLabel = esc($work['content_type']);
                        $typeClass = 'bg-indigo-50 text-indigo-600';
                        if ($work['content_type'] === 'novel') { $typeLabel = 'Novel'; $typeClass = 'bg-blue-50 text-blue-600'; }
                        elseif ($work['content_type'] === 'light_novel') { $typeLabel = 'Light Novel'; $typeClass = 'bg-teal-50 text-teal-600'; }
                        elseif ($work['content_type'] === 'comic') { $typeLabel = 'Comic'; $typeClass = 'bg-purple-50 text-purple-600'; }
                        elseif ($work['content_type'] === 'text') { $typeLabel = 'Teks'; }
                        elseif ($work['content_type'] === 'artikel') { $typeLabel = 'Artikel'; $typeClass = 'bg-amber-50 text-amber-600'; }
                    ?>
                    <span class="inline-block px-3 py-1 rounded-full <?= $typeClass ?> text-xs font-bold uppercase tracking-wider">
                        <?= $typeLabel ?>
                    </span>
                    <?php if (in_array($work['content_type'], ['text', 'novel', 'light_novel', 'comic'])): ?>
                    <span class="inline-block px-3 py-1 rounded-full <?= ($work['work_status'] ?? 'ongoing') === 'ongoing' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' ?> text-xs font-bold uppercase tracking-wider">
                        <?= esc($work['work_status'] ?? 'ongoing') ?>
                    </span>
                    <?php endif; ?>
                </div>

                <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-slate-900 mb-5 sm:mb-6 leading-tight"><?= esc($work['title']) ?></h1>
                
                <div class="flex flex-wrap items-center gap-4 mb-8">
                    <a href="<?= base_url('user/' . $work['creator_name']) ?>" class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xl hover:scale-105 transition-transform">
                        <?= strtoupper(substr($work['creator_name'], 0, 1)) ?>
                    </a>
                    <div>
                        <p class="text-xs text-slate-400 font-medium uppercase tracking-widest">Kreator</p>
                        <a href="<?= base_url('user/' . $work['creator_name']) ?>" class="font-bold text-slate-900 hover:text-indigo-600 transition-colors block"><?= esc($work['creator_name']) ?></a>
                    </div>
                    <div class="w-full sm:w-auto sm:ml-auto grid grid-cols-3 gap-3 sm:flex sm:items-center sm:gap-4">
                        <div class="text-center sm:pr-4 sm:border-r border-slate-100 bg-white sm:bg-transparent rounded-2xl sm:rounded-none border sm:border-0 border-slate-100 p-3 sm:p-0">
                            <span class="block text-xl font-bold text-slate-900" data-view-count><?= number_format($work['view_count']) ?></span>
                            <span class="text-[10px] text-slate-400 uppercase tracking-widest">Tayangan</span>
                        </div>
                        <button id="likeBtn" class="flex flex-col items-center gap-1 group transition-all bg-white sm:bg-transparent rounded-2xl border sm:border-0 border-slate-100 p-3 sm:p-0" data-work-id="<?= $work['id'] ?>">
                            <span class="material-symbols-outlined <?= $hasLiked ? 'text-red-500 FILL' : 'text-slate-400' ?> group-hover:scale-110 transition-transform" id="likeIcon" style="<?= $hasLiked ? 'font-variation-settings: \'FILL\' 1' : '' ?>">favorite</span>
                            <span class="text-[10px] font-bold <?= $hasLiked ? 'text-red-500' : 'text-slate-400' ?>" id="likeCount"><?= number_format($likeCount) ?></span>
                        </button>
                        <?php if (session()->get('isLoggedIn')): ?>
                        <button id="wishlistBtn"
                            class="flex flex-col items-center gap-1 group transition-all bg-white sm:bg-transparent rounded-2xl border sm:border-0 border-slate-100 p-3 sm:p-0"
                            data-work-id="<?= $work['id'] ?>"
                            data-bookmarked="<?= $hasBookmarked ? '1' : '0' ?>">
                            <span class="material-symbols-outlined group-hover:scale-110 transition-transform <?= $hasBookmarked ? 'text-indigo-600' : 'text-slate-400' ?>"
                                id="wishlistIcon"
                                style="<?= $hasBookmarked ? "font-variation-settings: 'FILL' 1" : '' ?>">
                                bookmark
                            </span>
                            <span class="text-[10px] font-bold <?= $hasBookmarked ? 'text-indigo-600' : 'text-slate-400' ?>" id="wishlistLabel">
                                <?= $hasBookmarked ? 'Tersimpan' : 'Simpan' ?>
                            </span>
                        </button>
                        <?php endif; ?>
                        <button id="shareBtn" class="flex flex-col items-center gap-1 group transition-all bg-white sm:bg-transparent rounded-2xl border sm:border-0 border-slate-100 p-3 sm:p-0">
                            <span class="material-symbols-outlined text-slate-400 group-hover:text-indigo-600 group-hover:scale-110 transition-transform">share</span>
                            <span class="text-[10px] font-bold text-slate-400">Bagikan</span>
                        </button>
                    </div>
                </div>

                <div class="prose prose-slate max-w-none text-slate-600 leading-relaxed">
                    <?= nl2br(esc($work['description'])) ?>
                </div>

                <?php if (isset($lastRead) && !empty($lastRead)): ?>
                    <div class="mb-8 p-6 bg-indigo-600 rounded-3xl shadow-xl shadow-indigo-100 flex items-center justify-between group overflow-hidden relative">
                        <!-- Decorative background elements -->
                        <div class="absolute -right-4 -top-4 w-32 h-32 bg-white/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
                        
                        <div class="relative z-10">
                            <p class="text-[10px] text-white/70 font-black uppercase tracking-[0.2em] mb-1">Your Last Reading</p>
                            <h4 class="text-white font-bold text-lg mb-0">Bab <?= $lastRead['order_num'] ?>: <?= esc($lastRead['chapter_title']) ?></h4>
                        </div>
                        <a href="<?= base_url("works/{$work['id']}/read/{$lastRead['chapter_id']}") ?>" class="relative z-10 bg-white text-indigo-600 px-6 py-3 rounded-2xl font-black text-sm hover:bg-slate-50 transition-all flex items-center gap-2 shadow-lg active:scale-95">
                            Lanjutkan Baca
                            <span class="material-symbols-outlined text-sm font-black">fast_forward</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <hr class="border-slate-200 mb-12">

        <!-- Content Area -->
        <section>
            <?php 
                // Cek apakah user yang login adalah pemilik/kreator karya ini
                $isOwner = session()->get('isLoggedIn') && session()->get('userId') == $work['creator_id'];
                $isChapterBased = in_array($work['content_type'], ['text', 'novel', 'light_novel', 'comic']);
                $icon = 'menu_book';
                if ($work['content_type'] === 'comic') $icon = 'collections_bookmark';
                elseif ($work['content_type'] === 'light_novel') $icon = 'auto_stories';
                elseif ($work['content_type'] === 'image') $icon = 'collections';
            ?>
            <h2 class="text-2xl font-bold text-slate-900 mb-8 flex items-center gap-2">
                <span class="material-symbols-outlined text-indigo-600">
                    <?= $icon ?>
                </span>
                <?= $isChapterBased ? 'Daftar Bab' : 'Galeri Karya' ?>
            </h2>

            <?php if ($isChapterBased): ?>
                <div class="grid gap-4">
                    <?php if (!empty($chapters)): ?>
                        <?php foreach ($chapters as $chapter): ?>
                            <div class="bg-white p-6 rounded-2xl border border-slate-100 hover:border-indigo-200 transition-all group flex justify-between items-center shadow-sm">
                                <div class="flex items-center gap-4">
                                    <?php if ($chapter['is_locked'] && !($chapter['is_unlocked'] ?? false) && !$isOwner): ?>
                                        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                                            <span class="material-symbols-outlined text-lg">lock</span>
                                        </div>
                                    <?php else: ?>
                                        <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                                            <span class="material-symbols-outlined text-lg">menu_book</span>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <h4 class="font-bold text-slate-900 group-hover:text-indigo-600 transition-colors">
                                            <?= esc($chapter['title']) ?>
                                            <?php if ($chapter['is_locked'] && !($chapter['is_unlocked'] ?? false) && !$isOwner): ?>
                                                <span class="ml-2 px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[10px] font-black uppercase"><?= $chapter['price'] ?> CC</span>
                                            <?php endif; ?>
                                        </h4>
                                        <p class="text-xs text-slate-400 mt-1">Dirilis <?= date('d M Y', strtotime($chapter['created_at'])) ?></p>
                                    </div>
                                </div>
                                <a href="<?= base_url("works/{$work['id']}/read/{$chapter['id']}") ?>" class="btn-primary px-6 py-2 rounded-xl text-sm font-bold flex items-center gap-2">
                                    <?= ($chapter['is_locked'] && !($chapter['is_unlocked'] ?? false) && !$isOwner) ? 'Buka' : 'Baca' ?> 
                                    <span class="material-symbols-outlined text-sm">chevron_right</span>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="py-12 text-center text-slate-400">
                            <span class="material-symbols-outlined text-4xl mb-2">drafts</span>
                            <p>Belum ada bab yang dirilis.</p>
                        </div>
                    <?php endif; ?>
                </div>

            <?php elseif ($work['content_type'] === 'image'): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 images-grid">
                    <?php if (!empty($images)): ?>
                        <?php foreach ($images as $img): ?>
                            <div class="aspect-[4/5] rounded-2xl overflow-hidden bg-slate-100 shadow-md border border-slate-100">
                                <?php 
                                    $imgUrl = base_url('image/gallery/' . $img['id']);
                                    $unlockedUntil = session()->get('unlocked_' . $work['id']);
                                    $isUnlocked = $unlockedUntil && time() <= $unlockedUntil;
                                    // Pemilik karya selalu bisa melihat kontennya sendiri secara gratis
                                    $isLocked = $work['is_paid'] && !$isUnlocked && !$isOwner;
                                ?>

                                <div class="relative group h-full">
                                    <img src="<?= $imgUrl ?>" alt="Karya" 
                                        class="w-full h-full object-cover transition-all duration-700 gallery-image <?= $isLocked ? 'blur-xl' : '' ?>"
                                        id="content-img-<?= $img['id'] ?>"
                                        data-img-id="<?= $img['id'] ?>"
                                        data-img-url="<?= $imgUrl ?>"
                                        loading="lazy"
                                        decoding="async"
                                        draggable="false"
                                        oncontextmenu="return false;"
                                        onclick="openLightbox(<?= $img['id'] ?>)">
                                    
                                    <?php if ($isLocked): ?>
                                        <div class="absolute inset-0 z-10 flex flex-col items-center justify-center bg-black/20 backdrop-blur-[2px] unlock-overlay">
                                            <div class="bg-white/90 p-6 rounded-3xl shadow-xl border border-white/50 text-center max-w-[80%]">
                                                <span class="material-symbols-outlined text-indigo-600 text-3xl mb-2">lock</span>
                                                <h5 class="font-bold text-slate-900 text-sm mb-1">Konten Berbayar</h5>
                                                <p class="text-[10px] text-slate-500 mb-4">Bayar <?= number_format($work['price']) ?> CC untuk melihat preview selama <?= $work['timer_duration'] ?> detik.</p>
                                                <button onclick="unlockContent(<?= $work['id'] ?>)" class="btn-primary px-6 py-2 rounded-xl text-xs font-bold w-full unlock-btn">
                                                    Buka Sekarang
                                                </button>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Watermark (Hidden by default) -->
                                    <div class="absolute inset-0 z-20 pointer-events-none opacity-0 flex flex-col justify-between items-center py-12 overflow-hidden watermark-container" id="watermark-<?= $img['id'] ?>">
                                        <!-- Top -->
                                        <div class="text-white/60 text-3xl font-black rotate-[-15deg] uppercase whitespace-nowrap p-2 border-2 border-white/40 rounded-xl select-none">
                                            <?= esc($work['watermark_text'] ?: $work['creator_name']) ?>
                                        </div>
                                        <!-- Middle -->
                                        <div class="text-white/60 text-5xl font-black rotate-[-15deg] uppercase whitespace-nowrap p-4 border-4 border-white/40 rounded-2xl select-none scale-125">
                                            <?= esc($work['watermark_text'] ?: $work['creator_name']) ?>
                                        </div>
                                        <!-- Bottom -->
                                        <div class="text-white/60 text-3xl font-black rotate-[-15deg] uppercase whitespace-nowrap p-2 border-2 border-white/40 rounded-xl select-none">
                                            <?= esc($work['watermark_text'] ?: $work['creator_name']) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-full py-12 text-center text-slate-400">
                            <span class="material-symbols-outlined text-4xl mb-2">image_not_supported</span>
                            <p>Belum ada gambar di galeri ini.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Action Button for Image Works -->
                <div class="mt-10 sm:mt-12 flex flex-col sm:flex-row justify-center gap-3 sm:gap-4">
                    <?php if (session()->get('isLoggedIn')): ?>
                        <button id="wishlistBtnImg"
                            class="w-full sm:w-auto justify-center flex items-center gap-2 px-8 py-3 rounded-xl font-bold text-sm transition-all group shadow-sm border <?= $hasBookmarked ? 'bg-indigo-600 border-indigo-600 text-white hover:bg-indigo-700' : 'bg-indigo-50 border-indigo-200 text-indigo-600 hover:bg-indigo-100' ?>"
                            data-work-id="<?= $work['id'] ?>"
                            data-bookmarked="<?= $hasBookmarked ? '1' : '0' ?>">
                            <span class="material-symbols-outlined group-hover:scale-110 transition-transform" id="wishlistIconImg"
                                style="<?= $hasBookmarked ? "font-variation-settings: 'FILL' 1" : '' ?>">
                                <?= $hasBookmarked ? 'bookmark' : 'bookmark_add' ?>
                            </span>
                            <span id="wishlistLabelImg"><?= $hasBookmarked ? 'Tersimpan di Wishlist' : 'Tambahkan ke Wishlist' ?></span>
                        </button>
                    <?php else: ?>
                        <a href="<?= base_url('login') ?>" class="w-full sm:w-auto justify-center bg-indigo-50 border border-indigo-200 text-indigo-600 px-8 py-3 rounded-xl font-bold text-sm hover:bg-indigo-100 transition-all flex items-center gap-2 group shadow-sm">
                            <span class="material-symbols-outlined group-hover:scale-110 transition-transform">bookmark_add</span>
                            Login untuk Simpan ke Wishlist
                        </a>
                    <?php endif; ?>
                    <?php if (!$work['is_paid']): ?>
                        <a href="<?= base_url('works/' . $work['id'] . '/download') ?>" class="w-full sm:w-auto justify-center btn-primary px-8 py-3 rounded-xl font-bold text-sm flex items-center gap-2 shadow-lg hover:shadow-indigo-200/50 transition-all group">
                            <span class="material-symbols-outlined group-hover:-translate-y-1 transition-transform">download</span>
                            Download Gambar (Gratis)
                        </a>
                    <?php endif; ?>
                </div>

            <?php endif; ?>
        </section>

        <hr class="border-slate-200 my-16">

        <!-- Comment Section -->
        <section id="comments-section">
            <h2 class="text-2xl font-bold text-slate-900 mb-8 flex items-center gap-2">
                <span class="material-symbols-outlined text-indigo-600">forum</span>
                Diskusi (<?= count($comments) ?>)
            </h2>

            <?php if (session()->get('isLoggedIn')): ?>
                <form action="<?= base_url("works/{$work['id']}/comment") ?>" method="POST" class="mb-12">
                    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm focus-within:border-indigo-500 transition-colors">
                        <textarea name="content" placeholder="Tulis komentar anda..." class="w-full bg-transparent border-none focus:ring-0 text-slate-700 min-h-[100px] resize-none" required></textarea>
                    </div>
                    <div class="flex justify-end mt-4">
                        <button type="submit" class="btn-primary px-8 py-2 rounded-xl text-sm font-bold">Kirim Komentar</button>
                    </div>
                </form>
            <?php else: ?>
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-8 text-center mb-12">
                    <p class="text-slate-600 mb-4">Anda harus login untuk ikut berdiskusi.</p>
                    <a href="<?= base_url('login') ?>" class="inline-block btn-primary px-6 py-2 rounded-xl text-sm font-bold">Masuk Sekarang</a>
                </div>
            <?php endif; ?>

            <div class="space-y-6">
                <?php if (!empty($comments)): ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="flex gap-4 group">
                            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold shrink-0">
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

    <footer class="mt-20 py-12 bg-white border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-6 text-center text-slate-400 text-sm">
            &copy; <?= date('Y') ?> NusaShare. Platform Kreator Indonesia.
        </div>
    </footer>



    <!-- Toast Notification -->
    <div id="wishlist-toast" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[9999] flex items-center gap-3 bg-slate-900 text-white px-6 py-3.5 rounded-2xl shadow-2xl text-sm font-semibold opacity-0 pointer-events-none transition-all duration-300 translate-y-4">
        <span class="material-symbols-outlined text-lg" id="toast-icon">bookmark</span>
        <span id="toast-message">Berhasil!</span>
    </div>

    <script>
        window.nusaAppData = {
            baseUrl: '<?= base_url() ?>/',
            isLoggedIn: <?= session()->get('isLoggedIn') ? 'true' : 'false' ?>,
            watermarkText: '<?= esc($work['watermark_text'] ?: $work['creator_name']) ?>'
        };
    </script>
    <script src="<?= base_url('assets/js/main.js') ?>?v=<?= time() ?>"></script>

    <!-- Wishlist (Bookmark) AJAX System -->
    <script>
    (function () {
        const BASE_URL = '<?= rtrim(base_url(), '/') ?>';
        const WORK_ID  = <?= (int)$work['id'] ?>;

        function showToast(message, icon, color) {
            const toast = document.getElementById('wishlist-toast');
            const toastIcon = document.getElementById('toast-icon');
            const toastMsg  = document.getElementById('toast-message');
            toastIcon.textContent = icon || 'bookmark';
            toastMsg.textContent  = message;
            toast.style.background = color || '#0F172A';
            // Show
            toast.classList.remove('opacity-0', 'translate-y-4', 'pointer-events-none');
            toast.classList.add('opacity-100', 'translate-y-0');
            // Hide after 3s
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-4', 'pointer-events-none');
                toast.classList.remove('opacity-100', 'translate-y-0');
            }, 3000);
        }

        function syncWishlistUI(isBookmarked) {
            // --- Sidebar button (likeBtn area) ---
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
                if (isBookmarked) { label.classList.remove('text-slate-400'); label.classList.add('text-indigo-600'); }
                else              { label.classList.remove('text-indigo-600'); label.classList.add('text-slate-400'); }
            }

            // --- Image-area button ---
            const btnImg   = document.getElementById('wishlistBtnImg');
            const iconImg  = document.getElementById('wishlistIconImg');
            const labelImg = document.getElementById('wishlistLabelImg');

            if (btnImg) {
                btnImg.dataset.bookmarked = isBookmarked ? '1' : '0';
                if (isBookmarked) {
                    btnImg.classList.remove('bg-indigo-50','border-indigo-200','text-indigo-600','hover:bg-indigo-100');
                    btnImg.classList.add('bg-indigo-600','border-indigo-600','text-white','hover:bg-indigo-700');
                } else {
                    btnImg.classList.remove('bg-indigo-600','border-indigo-600','text-white','hover:bg-indigo-700');
                    btnImg.classList.add('bg-indigo-50','border-indigo-200','text-indigo-600','hover:bg-indigo-100');
                }
            }
            if (iconImg) {
                iconImg.textContent = isBookmarked ? 'bookmark' : 'bookmark_add';
                iconImg.style.fontVariationSettings = isBookmarked ? "'FILL' 1" : "'FILL' 0";
            }
            if (labelImg) labelImg.textContent = isBookmarked ? 'Tersimpan di Wishlist' : 'Tambahkan ke Wishlist';
        }

        async function toggleWishlist(workId) {
            const btnEl = document.getElementById('wishlistBtn') || document.getElementById('wishlistBtnImg');
            const isCurrentlyBookmarked = (btnEl && btnEl.dataset.bookmarked === '1');

            // Optimistic UI update
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
                    const added = !isCurrentlyBookmarked;
                    if (added) {
                        showToast('Berhasil disimpan ke Wishlist!', 'bookmark_added', '#4F46E5');
                    } else {
                        showToast('Dihapus dari Wishlist.', 'bookmark_remove', '#64748B');
                    }
                } else {
                    // Revert on error
                    syncWishlistUI(isCurrentlyBookmarked);
                    showToast(data.message || 'Terjadi kesalahan.', 'error', '#EF4444');
                }
            } catch (err) {
                // Revert on network error
                syncWishlistUI(isCurrentlyBookmarked);
                showToast('Gagal terhubung. Coba lagi.', 'wifi_off', '#EF4444');
            }
        }

        // Attach event listeners after DOM ready
        document.addEventListener('DOMContentLoaded', function () {
            const btn    = document.getElementById('wishlistBtn');
            const btnImg = document.getElementById('wishlistBtnImg');

            if (btn)    btn.addEventListener('click',    () => toggleWishlist(WORK_ID));
            if (btnImg) btnImg.addEventListener('click', () => toggleWishlist(WORK_ID));
        });
    })();
    </script>

    <!-- 2-Menit View Threshold System -->
    <script>
    (function () {
        const WORK_ID      = <?= (int)$work['id'] ?>;
        const THRESHOLD_MS = 2 * 60 * 1000; // 2 menit
        const BASE_URL     = '<?= rtrim(base_url(), '/') ?>';
        const LS_KEY       = 'nusa_view_' + WORK_ID;
        const VIEW_URL     = BASE_URL + '/works/' + WORK_ID + '/view';

        // Cek apakah view sudah dihitung dalam sesi ini (pakai sessionStorage agar reset tiap tab baru)
        if (sessionStorage.getItem(LS_KEY)) {
            return; // Sudah dihitung, skip
        }

        const timer = setTimeout(function () {
            // Tandai dulu agar tidak double-count jika AJAX lambat
            sessionStorage.setItem(LS_KEY, '1');

            fetch(VIEW_URL, {
                method      : 'POST',
                credentials : 'same-origin',
                headers     : { 'X-Requested-With': 'XMLHttpRequest' },
            })
            .then(function (res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(function (data) {
                if (data.status === 'success') {
                    // Update tampilan view_count di halaman (jika elemen ada)
                    const el = document.querySelector('[data-view-count]');
                    if (el) {
                        const n = data.view_count;
                        el.textContent = n >= 1000 ? (n / 1000).toFixed(1) + 'k' : n.toLocaleString('id-ID');
                    }
                }
            })
            .catch(function (err) {
                // Jika gagal, hapus flag agar bisa dicoba lagi saat refresh
                console.warn('[ViewThreshold] Gagal mencatat view:', err);
                sessionStorage.removeItem(LS_KEY);
            });
        }, THRESHOLD_MS);

        // Batalkan timer jika user meninggalkan halaman sebelum 2 menit
        window.addEventListener('beforeunload', function () {
            clearTimeout(timer);
        });
    })();
    </script>
</body>
</html>
