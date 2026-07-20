<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($chapter['title']) ?> - <?= esc($work['title']) ?> - NusaShare</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">
    <link rel="icon" href="<?= base_url('assets/icon/logonus.png') ?>" type="image/x-icon">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #FAFAFA; color: #1E293B; transition: background-color 0.3s ease; }
        .font-serif-reading { font-family: 'Lora', serif; }
        .reader-container { max-width: 800px; margin: 0 auto; }
        .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(0, 0, 0, 0.05); }
        
        /* Reading Modes */
        .mode-dark { background-color: #0F172A; color: #E2E8F0; }
        .mode-dark .glass { background: rgba(15, 23, 42, 0.8); border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
        .mode-dark .text-slate-600 { color: #94A3B8; }
        .mode-dark .text-slate-900 { color: #F1F5F9; }
        .mode-dark .bg-white { background-color: #1E293B; }
        .mode-dark .border-slate-200 { border-color: #334155; }
        .mode-dark article { color: #FFFFFF !important; }
        
        .mode-sepia { background-color: #F4ECD8; color: #5B4636; }
        .mode-sepia .glass { background: rgba(244, 236, 216, 0.8); border-bottom: 1px solid rgba(0, 0, 0, 0.05); }
        
        .prose blockquote { font-style: italic; border-left: 4px solid #4F46E5; padding-left: 1.5rem; margin: 1.5rem 0; }
        .prose p { margin-bottom: 1.5rem; line-height: 1.8; font-size: 1.2rem; }
        
        .nav-btn { transition: all 0.2s ease; }
        .nav-btn:hover { transform: translateY(-1px); }
        
        #progress-bar { height: 3px; background: linear-gradient(90deg, #4F46E5, #22D3EE); width: 0%; position: absolute; bottom: 0; left: 0; transition: width 0.1s ease; }

        /* Drawer Scrollbar styling */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 99px;
        }
        .mode-dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
        }

        /* Theme adaptation for Chapters Drawer */
        .mode-dark #drawerPanel {
            background-color: #1E293B; /* slate-800 */
            border-color: #334155;     /* slate-700 */
            color: #F1F5F9;            /* slate-100 */
        }
        .mode-dark #drawerPanel .border-b {
            border-color: #334155;
        }
        .mode-dark #drawerPanel h3 {
            color: #F8FAFC;
        }
        .mode-dark #drawerPanel p {
            color: #94A3B8;
        }
        .mode-dark #chapterSearch {
            background-color: #0F172A; /* slate-900 */
            border-color: #334155;
            color: #F1F5F9;
        }
        .mode-dark #chapterSearch::placeholder {
            color: #64748B;
        }
        .mode-dark #chapterSearch:focus {
            border-color: #6366F1; /* indigo-500 */
        }
        .mode-dark .chapter-item:not(.bg-gradient-to-r) {
            background-color: #0F172A;
            border-color: #1E293B;
            color: #CBD5E1;
        }
        .mode-dark .chapter-item:not(.bg-gradient-to-r):hover {
            background-color: #334155;
            border-color: #4F46E5;
            color: #F1F5F9;
        }
        .mode-dark .chapter-item:not(.bg-gradient-to-r) p {
            color: #E2E8F0;
        }
        .mode-dark .chapter-item:not(.bg-gradient-to-r):hover p {
            color: #FFFFFF;
        }

        /* Sepia Mode adaptation for Chapters Drawer */
        .mode-sepia #drawerPanel {
            background-color: #EFE6CF; /* Darker sepia */
            border-color: #E3D9BE;
            color: #5B4636;
        }
        .mode-sepia #drawerPanel .border-b {
            border-color: #E3D9BE;
        }
        .mode-sepia #drawerPanel h3 {
            color: #433225;
        }
        .mode-sepia #drawerPanel p {
            color: #8C7662;
        }
        .mode-sepia #chapterSearch {
            background-color: #FBF7ED;
            border-color: #E3D9BE;
            color: #5B4636;
        }
        .mode-sepia #chapterSearch::placeholder {
            color: #A38F7C;
        }
        .mode-sepia #chapterSearch:focus {
            border-color: #8C7662;
        }
        .mode-sepia .chapter-item:not(.bg-gradient-to-r) {
            background-color: #F4ECD8;
            border-color: #E3D9BE;
            color: #5B4636;
        }
        .mode-sepia .chapter-item:not(.bg-gradient-to-r):hover {
            background-color: #E8DDCA;
            border-color: #CBBCA4;
            color: #433225;
        }
        .mode-sepia .chapter-item:not(.bg-gradient-to-r) p {
            color: #5B4636;
        }
        .mode-sepia .chapter-item:not(.bg-gradient-to-r):hover p {
            color: #433225;
        }

        /* Reader Enhancements */
        .reader-content { 
            position: relative; 
            user-select: none; 
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
        }
        .reading-para { 
            padding: 8px 12px; 
            margin: 0 -12px 1.5rem !important;
            border-radius: 8px; 
            cursor: pointer; 
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
            position: relative;
        }
        .reading-para:hover { background: rgba(79, 70, 229, 0.03); }
        .reading-para.active-marker { 
            background: rgba(79, 70, 229, 0.05); 
            border-left-color: #4F46E5;
        }
        .marker-label {
            position: absolute;
            left: -100px;
            top: 50%;
            transform: translateY(-50%);
            background: #4F46E5;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            display: none;
            font-family: 'Inter', sans-serif;
        }
        .active-marker .marker-label { display: block; }
        
        @media (max-width: 1024px) {
            .marker-label { display: none !important; }
        }

        /* Comment Animations */
        @keyframes commentIn {
            from { opacity: 0; transform: translateY(-12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .comment-enter { animation: commentIn 0.3s ease forwards; }
        @keyframes commentOut {
            from { opacity: 1; transform: scale(1); max-height: 200px; }
            to   { opacity: 0; transform: scale(0.95); max-height: 0; margin: 0; padding: 0; }
        }
        .comment-exit { animation: commentOut 0.3s ease forwards; overflow: hidden; }
    </style>
</head>
<body class="min-h-screen">

    <!-- Top Navigation -->
    <nav class="sticky top-0 z-50 glass py-3 px-6">
        <div class="max-w-7xl mx-auto flex justify-between items-center transition-all">
            <div class="flex items-center gap-2">
                <a href="<?= base_url('works/' . $work['id']) ?>" class="p-2 rounded-full hover:bg-slate-100/50 transition-colors" title="Kembali ke Daftar Isi">
                    <span class="material-symbols-outlined text-slate-500">list_alt</span>
                </a>
                <button onclick="toggleChaptersDrawer(true)" class="p-2 rounded-full hover:bg-slate-100/50 transition-colors flex items-center justify-center" title="Buka Daftar Bab">
                    <span class="material-symbols-outlined text-slate-500">format_list_bulleted</span>
                </button>
                <div class="hidden sm:block ml-2">
                    <h1 class="text-sm font-bold text-slate-900 line-clamp-1"><?= esc($work['title']) ?></h1>
                    <p class="text-[10px] text-slate-500 uppercase tracking-widest font-medium"><?= esc($chapter['title']) ?></p>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                <?php 
                    $isLoggedIn = session()->get('isLoggedIn');
                    $balance = 0;
                    if ($isLoggedIn): 
                        $creditModel = new \App\Models\CreditModel();
                        $userCredit = $creditModel->find(session()->get('userId'));
                        $balance = $userCredit ? $userCredit['balance'] : 0;
                ?>
                    <a href="<?= base_url('topup') ?>" class="bg-indigo-50 px-3 py-1.5 rounded-full flex items-center gap-2 border border-indigo-100 hover:bg-indigo-100 transition-colors mr-2">
                        <span class="material-symbols-outlined text-indigo-600 text-lg">account_balance_wallet</span>
                        <span class="text-xs font-bold text-indigo-900"><?= number_format($balance) ?> CC</span>
                    </a>
                <?php endif; ?>
                <!-- Reading Settings -->
                <div class="flex bg-slate-100 rounded-full p-1 gap-1">
                    <button onclick="setMode('light')" class="w-8 h-8 rounded-full bg-white shadow-sm flex items-center justify-center" title="Light Mode">
                        <span class="material-symbols-outlined text-[20px]">light_mode</span>
                    </button>
                    <button onclick="setMode('sepia')" class="w-8 h-8 rounded-full flex items-center justify-center text-amber-900" title="Sepia Mode">
                        <span class="material-symbols-outlined text-[20px]">menu_book</span>
                    </button>
                    <button onclick="setMode('dark')" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-500" title="Dark Mode">
                        <span class="material-symbols-outlined text-[20px]">dark_mode</span>
                    </button>
                </div>
            </div>
        </div>
        <div id="progress-bar"></div>
    </nav>

    <main class="reader-container px-6 py-12 md:py-20 lg:py-24">
        <!-- Chapter Header -->
        <header class="mb-12 text-center">
            <div class="inline-block px-3 py-1 rounded-full bg-indigo-50 text-indigo-600 text-[10px] font-bold uppercase tracking-widest mb-4">
                Bab <?= esc($chapter['order_num'] ?? '') ?>
            </div>
            <h2 class="text-3xl md:text-4xl font-black text-slate-900 mb-6 leading-tight"><?= esc($chapter['title']) ?></h2>
            <div class="w-12 h-1 bg-indigo-600 mx-auto rounded-full"></div>
        </header>

        <!-- Chapter Body -->
        <article id="reader-article" class="reader-content prose prose-slate max-w-none font-serif-reading text-slate-800 selection:bg-indigo-100 min-h-[400px]">
            <?php if ($chapter['is_locked'] && !($is_unlocked ?? false)): ?>
                <div class="my-12 p-8 md:p-12 bg-white rounded-3xl border border-slate-200 shadow-xl shadow-slate-200/50 text-center relative overflow-hidden group">
                    <!-- Decorative background -->
                    <div class="absolute -right-12 -top-12 w-48 h-48 bg-indigo-50 rounded-full blur-3xl group-hover:bg-indigo-100 transition-colors duration-700"></div>
                    
                    <div class="relative z-10">
                        <div class="w-20 h-20 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600 mx-auto mb-6 shadow-sm">
                            <span class="material-symbols-outlined text-4xl">lock</span>
                        </div>
                        <h3 class="text-2xl font-black text-slate-900 mb-3">Bab Terkunci</h3>
                        <p class="text-slate-500 mb-8 max-w-sm mx-auto leading-relaxed">
                            Bab ini adalah konten khusus. Dukung kreator dengan membuka bab ini seharga <span class="font-bold text-indigo-600"><?= $chapter['price'] ?> CC</span>.
                        </p>
                        
                        <?php if ($isLoggedIn): ?>
                            <button 
                                id="unlockBtn" 
                                data-chapter-id="<?= $chapter['id'] ?>"
                                data-price="<?= $chapter['price'] ?>"
                                class="bg-indigo-600 text-white px-10 py-4 rounded-2xl font-black text-sm hover:bg-indigo-700 shadow-lg shadow-indigo-100 active:scale-95 transition-all flex items-center gap-3 mx-auto"
                            >
                                Buka Sekarang
                                <span class="material-symbols-outlined text-sm font-black">key</span>
                            </button>
                            
                            <p class="mt-6 text-[10px] text-slate-400 uppercase tracking-widest font-bold">Saldo CC Anda: <span class="cc-balance text-indigo-600"><?= number_format($balance) ?></span> CC</p>
                        <?php else: ?>
                            <a 
                                href="<?= base_url('login') ?>" 
                                class="bg-indigo-600 text-white px-10 py-4 rounded-2xl font-black text-sm hover:bg-indigo-700 shadow-lg shadow-indigo-100 active:scale-95 transition-all flex items-center gap-3 mx-auto max-w-xs"
                            >
                                Silakan Login Untuk Membuka
                                <span class="material-symbols-outlined text-sm font-black">login</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Blurred preview (optional placeholder text) -->
                <div class="opacity-20 select-none pointer-events-none blur-sm">
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                    <p>Curabitur pretium tincidunt lacus. Nulla gravida orci a odio. Nullam varius, turpis et commodo pharetra, est eros bibendum elit, nec luctus magna felis sollicitudin mauris. Integer in mauris eu nibh euismod gravida. Duis ac tellus et risus vulputate vehicula. Donec lobortis risus a elit. Etiam tempor. Ut ullamcorper, ligula eu tempor congue, eros est euismod turpis, id tincidunt sapien risus a quam. Maecenas fermentum consequat mi. Donec fermentum. Pellentesque malesuada nulla a mi. Duis sapien sem, aliquet nec, commodo eget, consequat quis, neque. Aliquam faucibus, elit ut dictum aliquet, felis nisl adipiscing sapien, sed malesuada diam lacus eget erat. Cras mollis scelerisque nunc. Donec vehicula cursus purus. Mauris nulla.</p>
                </div>
            <?php else: ?>
                <?php if ($work['content_type'] === 'comic'): ?>
                    <div class="comic-container -mx-6 md:-mx-12">
                        <?php 
                            $images = json_decode($chapter['body'] ?? '[]', true);
                            if (!empty($images)):
                                foreach ($images as $img):
                                    $src = $img;
                                    if (filter_var($img, FILTER_VALIDATE_URL)) {
                                        // Gunakan proxy untuk URL eksternal
                                        $src = base_url('image/proxy-remote?q=' . urlencode(base64_encode($img)));
                                    } else {
                                        if (strpos($img, 'chapters/') === 0) {
                                            $parts = explode('/', $img);
                                            $imgWorkId = $parts[1] ?? 0;
                                            $imgFile   = $parts[2] ?? basename($img);
                                            $src = base_url('image/chapter/' . $imgWorkId . '/' . $imgFile);
                                        } else {
                                            $src = base_url($img);
                                        }
                                    }
                        ?>
                            <img src="<?= $src ?>" class="w-full h-auto mb-0" loading="lazy" draggable="false" oncontextmenu="return false;">
                        <?php 
                                endforeach;
                            else:
                        ?>
                            <p class="text-center text-slate-400 py-12">Tidak ada gambar dalam bab ini.</p>
                        <?php endif; ?>
                    </div>
                <?php elseif ($work['content_type'] === 'light_novel'): ?>
                    <?php 
                        $body = $chapter['body'] ?? '';
                        $images = [];
                        if (preg_match('/<!--chapter_images:(.*?)-->/', $body, $matches)) {
                            $images = json_decode($matches[1], true);
                            $body = str_replace($matches[0], '', $body);
                        }
                    ?>
                    <div class="ln-content mb-12">
                        <?= $body ?>
                    </div>
                    <?php if (!empty($images)): ?>
                        <div class="ln-illustrations space-y-8 -mx-6 md:-mx-0">
                            <?php foreach ($images as $img): ?>
                                <?php
                                    $src = $img;
                                    if (filter_var($img, FILTER_VALIDATE_URL)) {
                                        $src = base_url('image/proxy-remote?q=' . urlencode(base64_encode($img)));
                                    } else {
                                        if (strpos($img, 'chapters/') === 0) {
                                            $parts = explode('/', $img);
                                            // path: chapters/{workId}/{filename}
                                            $imgWorkId = $parts[1] ?? 0;
                                            $imgFile   = $parts[2] ?? basename($img);
                                            $src = base_url('image/chapter/' . $imgWorkId . '/' . $imgFile);
                                        } else {
                                            $src = base_url($img);
                                        }
                                    }
                                ?>
                                <img src="<?= $src ?>" class="max-w-full h-auto mx-auto rounded-lg shadow-xl" loading="lazy" draggable="false" oncontextmenu="return false;">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <?php 
                        $body = $chapter['body'] ?? '';
                        if (strpos($body, '<p>') !== false || strpos($body, '<div>') !== false):
                    ?>
                        <div class="novel-content">
                            <?= $body ?>
                        </div>
                    <?php else: ?>
                        <?php 
                            $paragraphs = explode("\n", $body);
                            foreach ($paragraphs as $idx => $p): 
                                $p = trim($p);
                                if (empty($p)) continue;
                        ?>
                            <p class="reading-para" data-para-id="<?= $idx ?>">
                                <span class="marker-label">TERAKHIR BACA</span>
                                <?= $p ?>
                            </p>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </article>

        <!-- Comments Section -->
        <section class="mt-20 pt-12 border-t border-slate-200" id="comments-section">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-xl font-black text-slate-900 flex items-center gap-3">
                    <span class="material-symbols-outlined text-indigo-600">chat_bubble</span>
                    Komentar Bab
                </h3>
                <span id="comment-count-badge" class="px-3 py-1 bg-slate-100 rounded-full text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                    <?= count($comments) ?> Komentar
                </span>
            </div>

            <!-- Comment Form -->
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm mb-8">
                <?php if (session()->get('isLoggedIn')): ?>
                    <form id="comment-form" data-work-id="<?= $work['id'] ?>" data-chapter-id="<?= $chapter['id'] ?>">
                        <?= csrf_field() ?>
                        <div class="flex gap-3 items-start">
                            <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-sm shrink-0 mt-1">
                                <?= strtoupper(substr(session()->get('username'), 0, 1)) ?>
                            </div>
                            <div class="flex-1">
                                <textarea
                                    id="comment-input"
                                    name="content"
                                    rows="3"
                                    class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 text-sm placeholder:text-slate-400 resize-none transition-all"
                                    placeholder="Tuliskan pendapatmu tentang bab ini..."
                                    required
                                ></textarea>
                                <div class="flex justify-between items-center mt-3">
                                    <span id="comment-error" class="text-xs text-red-500 hidden"></span>
                                    <button
                                        type="submit"
                                        id="comment-submit-btn"
                                        class="ml-auto bg-indigo-600 text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-indigo-700 transition-all flex items-center gap-2 shadow-lg shadow-indigo-100 active:scale-95"
                                    >
                                        <span id="submit-text">Kirim</span>
                                        <span class="material-symbols-outlined text-xs">send</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="text-center py-4">
                        <p class="text-sm text-slate-500 mb-4">Silakan login untuk memberikan komentar.</p>
                        <a href="<?= base_url('login') ?>" class="inline-block text-indigo-600 font-bold text-sm hover:underline">Login Sekarang</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Comments List -->
            <div id="comments-list" class="space-y-4">
                <?php if (empty($comments)): ?>
                    <div id="empty-state" class="text-center py-12 bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200">
                        <span class="material-symbols-outlined text-slate-300 text-4xl mb-3">forum</span>
                        <p class="text-slate-400 text-sm italic">Belum ada komentar untuk bab ini. Jadilah yang pertama!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment-item flex gap-3 group" id="comment-<?= $comment['id'] ?>">
                            <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-sm shrink-0 mt-1">
                                <?= strtoupper(substr($comment['username'], 0, 1)) ?>
                            </div>
                            <div class="flex-1">
                                <div class="bg-slate-50 rounded-2xl rounded-tl-none p-4 border border-slate-100">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-sm font-bold text-slate-900"><?= esc($comment['username']) ?></span>
                                        <?php if ($comment['role'] === 'creator'): ?>
                                            <span class="px-1.5 py-0.5 rounded bg-indigo-600 text-[8px] text-white font-black uppercase">Creator</span>
                                        <?php endif; ?>
                                        <span class="text-[10px] text-slate-400 font-medium ml-auto"><?= date('d M Y, H:i', strtotime($comment['created_at'])) ?></span>
                                    </div>
                                    <div class="text-sm text-slate-700 leading-relaxed"><?= nl2br(esc($comment['content'])) ?></div>
                                </div>
                                <?php if (session()->get('userId') === $comment['user_id']): ?>
                                    <button
                                        class="delete-comment-btn mt-1.5 text-[10px] text-slate-400 font-bold hover:text-red-500 uppercase tracking-tighter opacity-0 group-hover:opacity-100 transition-all flex items-center gap-1"
                                        data-comment-id="<?= $comment['id'] ?>"
                                    >
                                        <span class="material-symbols-outlined text-xs">delete</span> Hapus
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <!-- Bottom Navigation -->
        <div class="mt-20 pt-12 border-t border-slate-200">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-6">
                <?php if ($prevChapter): ?>
                    <a href="<?= base_url("works/{$work['id']}/read/{$prevChapter['id']}") ?>" class="nav-btn w-full sm:w-auto px-8 py-3 rounded-2xl bg-white border border-slate-200 shadow-sm text-slate-600 font-bold flex items-center justify-center gap-3 hover:border-indigo-400 hover:text-indigo-600 transition-all">
                        <span class="material-symbols-outlined text-sm">arrow_back</span>
                        <div>
                            <span class="block text-[10px] uppercase tracking-widest text-slate-400">Sebelumnya</span>
                            <span class="block text-sm line-clamp-1"><?= esc($prevChapter['title']) ?></span>
                        </div>
                    </a>
                <?php else: ?>
                    <div class="w-full sm:w-auto sm:invisible"></div>
                <?php endif; ?>

                <div class="flex items-center gap-3">
                    <a href="<?= base_url('works/' . $work['id']) ?>" class="p-4 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 transition-all flex items-center justify-center" title="Daftar Isi">
                        <span class="material-symbols-outlined">grid_view</span>
                    </a>
                    <button onclick="toggleChaptersDrawer(true)" class="p-4 rounded-full bg-indigo-50 text-indigo-600 hover:bg-indigo-100 hover:text-indigo-700 transition-all flex items-center justify-center" title="Buka Daftar Bab">
                        <span class="material-symbols-outlined">format_list_bulleted</span>
                    </button>
                </div>

                <?php if ($nextChapter): ?>
                    <a href="<?= base_url("works/{$work['id']}/read/{$nextChapter['id']}") ?>" class="nav-btn w-full sm:w-auto px-8 py-3 rounded-2xl bg-indigo-600 text-white font-bold flex items-center justify-center gap-3 hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all">
                        <div class="text-right">
                            <span class="block text-[10px] uppercase tracking-widest text-indigo-200">Selanjutnya</span>
                            <span class="block text-sm line-clamp-1"><?= esc($nextChapter['title']) ?></span>
                        </div>
                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                <?php else: ?>
                    <div class="text-center sm:text-right">
                        <p class="text-sm font-bold text-slate-400 italic">Anda telah mencapai bab terakhir.</p>
                        <a href="<?= base_url('works/' . $work['id']) ?>" class="text-indigo-600 text-sm font-bold hover:underline mt-2 inline-block">Beri dukungan kepada kreator</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="py-12 bg-white border-t border-slate-100 mt-20">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <p class="text-slate-400 text-xs tracking-widest uppercase font-medium mb-4">Membaca di NusaShare</p>
            <div class="flex justify-center gap-6 text-slate-300">
                <span class="material-symbols-outlined text-3xl">menu_book</span>
            </div>
        </div>
    </footer>

    <script>
        // Reading Modes
        function setMode(mode) {
            document.body.classList.remove('mode-dark', 'mode-sepia', 'mode-light');
            if (mode === 'dark') document.body.classList.add('mode-dark');
            if (mode === 'sepia') document.body.classList.add('mode-sepia');
            
            // Save preference
            localStorage.setItem('reading-mode', mode);
        }

        // Restore preference
        const savedMode = localStorage.getItem('reading-mode');
        if (savedMode) setMode(savedMode);

        // Progress Bar
        window.onscroll = function() {
            let winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            let height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            let scrolled = (winScroll / height) * 100;
            document.getElementById("progress-bar").style.width = scrolled + "%";
        };

        // Unlock logic
        const unlockBtn = document.getElementById('unlockBtn');
        if (unlockBtn) {
            unlockBtn.addEventListener('click', async () => {
                if (!confirm(`Buka bab ini seharga ${unlockBtn.dataset.price} CC?`)) return;

                unlockBtn.disabled = true;
                unlockBtn.innerHTML = '<span class="material-symbols-outlined animate-spin">sync</span> Memproses...';

                try {
                    const response = await fetch(`<?= base_url('chapters') ?>/${unlockBtn.dataset.chapterId}/unlock`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                        }
                    });

                    const data = await response.json();

                    if (data.status === 'success') {
                        // Refresh to show content
                        location.reload();
                    } else {
                        alert(data.message || 'Gagal membuka bab.');
                        unlockBtn.disabled = false;
                        unlockBtn.textContent = 'Buka Sekarang';
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan sistem.');
                    unlockBtn.disabled = false;
                    unlockBtn.textContent = 'Buka Sekarang';
                }
            });
        }

        // Reader Enhancements: Anti-Copy & Reading Marker
        document.addEventListener('DOMContentLoaded', () => {
            const article = document.getElementById('reader-article');
            const chapterId = '<?= $chapter['id'] ?>';
            
            // 1. Anti-Copy logic
            article.addEventListener('contextmenu', e => e.preventDefault());
            document.addEventListener('keydown', e => {
                if ((e.ctrlKey || e.metaKey) && (e.key === 'c' || e.key === 'u' || e.key === 's' || e.key === 'p')) {
                    e.preventDefault();
                    return false;
                }
            });
            document.addEventListener('copy', e => {
                e.preventDefault();
                alert('Penyalinan teks dilarang untuk melindungi hak cipta penulis.');
            });

            // 2. Reading Marker Logic
            const paragraphs = document.querySelectorAll('.reading-para');
            const storageKey = `nusa-read-pos-${chapterId}`;
            
            // Function to set marker
            const setMarker = (paraId, isInitial = false) => {
                paragraphs.forEach(p => p.classList.remove('active-marker'));
                const activePara = document.querySelector(`.reading-para[data-para-id="${paraId}"]`);
                if (activePara) {
                    activePara.classList.add('active-marker');
                    localStorage.setItem(storageKey, paraId);
                    
                    if (isInitial) {
                        activePara.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            };

            // Click listener for paragraphs
            paragraphs.forEach(para => {
                para.addEventListener('click', () => {
                    setMarker(para.dataset.paraId);
                });
            });

            // Initial restore
            const lastPos = localStorage.getItem(storageKey);
            if (lastPos !== null) {
                setTimeout(() => setMarker(lastPos, true), 500);
            }
        });
    </script>

    <!-- 2-Menit View Threshold System -->
    <script>
    (function () {
        const WORK_ID      = <?= (int)$work['id'] ?>;
        const THRESHOLD_MS = 2 * 60 * 1000; // 2 menit
        const BASE_URL     = '<?= rtrim(base_url(), '/') ?>';
        const LS_KEY       = 'nusa_view_' + WORK_ID;
        const VIEW_URL     = BASE_URL + '/works/' + WORK_ID + '/view';

        // Cek apakah view sudah dihitung di sesi ini
        if (sessionStorage.getItem(LS_KEY)) {
            return;
        }

        const timer = setTimeout(function () {
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
            .catch(function (err) {
                console.warn('[ViewThreshold] Gagal mencatat view:', err);
                sessionStorage.removeItem(LS_KEY);
            });
        }, THRESHOLD_MS);

        // Batalkan jika user pindah halaman sebelum 2 menit
        window.addEventListener('beforeunload', function () {
            clearTimeout(timer);
        });
    })();
    </script>

    <!-- AJAX Comment System -->
    <script>
    (function () {
        const BASE_URL    = '<?= base_url() ?>';
        const CSRF_NAME   = '<?= csrf_token() ?>';
        const IS_LOGGED   = <?= session()->get('isLoggedIn') ? 'true' : 'false' ?>;
        const CURRENT_UID = <?= (int)(session()->get('userId') ?? 0) ?>;
        const USERNAME    = '<?= esc(session()->get('username') ?? '') ?>';
        const USER_ROLE   = '<?= esc(session()->get('role') ?? 'user') ?>';

        // ─── Helper: get fresh CSRF token from meta or form ───────────────
        function getCsrf() {
            const inp = document.querySelector('input[name="' + CSRF_NAME + '"]');
            return inp ? inp.value : '';
        }

        // ─── Helper: format date ──────────────────────────────────────────
        function fmtDate(dateStr) {
            const d = dateStr ? new Date(dateStr) : new Date();
            return d.toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' })
                 + ', ' + d.toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit' });
        }

        // ─── Helper: escape HTML ──────────────────────────────────────────
        function escHtml(s) {
            return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
                    .replace(/"/g,'&quot;').replace(/'/g,'&#039;');
        }

        // ─── Helper: nl2br ───────────────────────────────────────────────
        function nl2br(s) { return escHtml(s).replace(/\n/g, '<br>'); }

        // ─── Update counter badge ─────────────────────────────────────────
        function updateBadge(delta) {
            const badge = document.getElementById('comment-count-badge');
            if (!badge) return;
            const match = badge.textContent.match(/(\d+)/);
            const cur   = match ? parseInt(match[1]) : 0;
            const next  = Math.max(0, cur + delta);
            badge.textContent = next + ' Komentar';
        }

        // ─── Build comment HTML ───────────────────────────────────────────
        function buildCommentEl(c) {
            const isOwn = (c.user_id === CURRENT_UID);
            const initial = (c.username || 'U').charAt(0).toUpperCase();
            const creatorBadge = (c.role === 'creator')
                ? '<span class="px-1.5 py-0.5 rounded bg-indigo-600 text-[8px] text-white font-black uppercase">Creator</span>'
                : '';
            const deleteBtn = isOwn
                ? `<button class="delete-comment-btn mt-1.5 text-[10px] text-slate-400 font-bold hover:text-red-500 uppercase tracking-tighter opacity-0 group-hover:opacity-100 transition-all flex items-center gap-1" data-comment-id="${c.id}"><span class="material-symbols-outlined text-xs">delete</span> Hapus</button>`
                : '';

            const div = document.createElement('div');
            div.id        = 'comment-' + c.id;
            div.className = 'comment-item comment-enter flex gap-3 group';
            div.innerHTML = `
                <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-sm shrink-0 mt-1">${initial}</div>
                <div class="flex-1">
                    <div class="bg-slate-50 rounded-2xl rounded-tl-none p-4 border border-slate-100">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-sm font-bold text-slate-900">${escHtml(c.username)}</span>
                            ${creatorBadge}
                            <span class="text-[10px] text-slate-400 font-medium ml-auto">${fmtDate(c.created_at)}</span>
                        </div>
                        <div class="text-sm text-slate-700 leading-relaxed">${nl2br(c.content)}</div>
                    </div>
                    ${deleteBtn}
                </div>`;
            return div;
        }

        // ─── Submit comment ───────────────────────────────────────────────
        const form = document.getElementById('comment-form');
        if (form) {
            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                const textarea  = document.getElementById('comment-input');
                const submitBtn = document.getElementById('comment-submit-btn');
                const errEl     = document.getElementById('comment-error');
                const content   = textarea.value.trim();

                if (!content) {
                    errEl.textContent = 'Komentar tidak boleh kosong.';
                    errEl.classList.remove('hidden');
                    return;
                }
                errEl.classList.add('hidden');

                const workId    = form.dataset.workId;
                const chapterId = form.dataset.chapterId;

                // Loading state
                submitBtn.disabled = true;
                document.getElementById('submit-text').textContent = 'Mengirim...';

                try {
                    const fd = new FormData();
                    fd.append('content',    content);
                    fd.append('chapter_id', chapterId);
                    fd.append(CSRF_NAME,    getCsrf());

                    const res  = await fetch(BASE_URL + 'works/' + workId + '/comment', {
                        method : 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        body   : fd
                    });
                    const data = await res.json();

                    if (data.status === 'success') {
                        // Remove empty state if exists
                        const empty = document.getElementById('empty-state');
                        if (empty) empty.remove();

                        // Prepend new comment
                        const list = document.getElementById('comments-list');
                        const el   = buildCommentEl(data.comment);
                        list.insertBefore(el, list.firstChild);

                        // Update badge
                        updateBadge(+1);

                        // Clear textarea
                        textarea.value = '';
                        textarea.style.height = '';

                        // Scroll to new comment
                        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    } else {
                        errEl.textContent = data.message || 'Gagal mengirim komentar.';
                        errEl.classList.remove('hidden');
                    }
                } catch (err) {
                    errEl.textContent = 'Terjadi kesalahan. Coba lagi.';
                    errEl.classList.remove('hidden');
                } finally {
                    submitBtn.disabled = false;
                    document.getElementById('submit-text').textContent = 'Kirim';
                }
            });

            // Auto-resize textarea
            const textarea = document.getElementById('comment-input');
            textarea.addEventListener('input', function () {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 200) + 'px';
            });
        }

        // ─── Delete comment (event delegation) ───────────────────────────
        document.addEventListener('click', async function (e) {
            const btn = e.target.closest('.delete-comment-btn');
            if (!btn) return;

            if (!confirm('Hapus komentar ini?')) return;

            const commentId = btn.dataset.commentId;
            const commentEl = document.getElementById('comment-' + commentId);

            btn.disabled = true;

            try {
                const fd = new FormData();
                fd.append(CSRF_NAME, getCsrf());

                const res  = await fetch(BASE_URL + 'comments/' + commentId + '/delete', {
                    method : 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body   : fd
                });
                const data = await res.json();

                if (data.status === 'success') {
                    // Animate out then remove
                    commentEl.classList.add('comment-exit');
                    setTimeout(function () {
                        commentEl.remove();
                        updateBadge(-1);

                        // Show empty state if no more comments
                        const list = document.getElementById('comments-list');
                        if (!list.querySelector('.comment-item')) {
                            list.innerHTML = `<div id="empty-state" class="text-center py-12 bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200">
                                <span class="material-symbols-outlined text-slate-300 text-4xl mb-3">forum</span>
                                <p class="text-slate-400 text-sm italic">Belum ada komentar untuk bab ini. Jadilah yang pertama!</p>
                            </div>`;
                        }
                    }, 300);
                } else {
                    alert(data.message || 'Gagal menghapus komentar.');
                    btn.disabled = false;
                }
            } catch (err) {
                alert('Terjadi kesalahan. Coba lagi.');
                btn.disabled = false;
            }
        });
    })();
    </script>
    <!-- Chapters Slide-over Drawer -->
    <div id="chaptersDrawer" class="fixed inset-0 z-[100] invisible transition-all duration-300">
        <!-- Overlay Backdrop -->
        <div id="drawerOverlay" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm opacity-0 transition-opacity duration-300"></div>
        
        <!-- Drawer Content Container -->
        <div id="drawerPanel" class="absolute right-0 top-0 bottom-0 w-full max-w-md bg-white shadow-2xl flex flex-col translate-x-full transition-transform duration-300 ease-out border-l border-slate-100">
            <!-- Drawer Header -->
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-black text-slate-900 text-lg leading-tight line-clamp-1"><?= esc($work['title']) ?></h3>
                    <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mt-0.5"><?= count($allChapters) ?> Bab Tersedia</p>
                </div>
                <button onclick="toggleChaptersDrawer(false)" class="p-2 rounded-full hover:bg-slate-100/50 text-slate-500 transition-colors flex items-center justify-center">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <!-- Search Bar -->
            <div class="p-4 border-b border-slate-100">
                <div class="relative flex items-center">
                    <span class="material-symbols-outlined absolute left-3 text-slate-400 text-xl pointer-events-none">search</span>
                    <input 
                        type="text" 
                        id="chapterSearch" 
                        placeholder="Cari bab..." 
                        class="w-full pl-10 pr-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all outline-none"
                    >
                </div>
            </div>
            
            <!-- Chapters List -->
            <div id="drawerChaptersList" class="flex-1 overflow-y-auto p-4 space-y-2 custom-scrollbar">
                <?php foreach ($allChapters as $c): 
                    $isActive = ((int)$c['id'] === (int)$chapter['id']);
                    $isLocked = ((int)$c['is_locked'] === 1);
                ?>
                    <a 
                        href="<?= base_url("works/{$work['id']}/read/{$c['id']}") ?>" 
                        class="chapter-item block p-4 rounded-2xl border transition-all flex items-center justify-between gap-3 group/item <?= $isActive 
                            ? 'bg-gradient-to-r from-indigo-600 to-indigo-700 border-indigo-600 text-white shadow-lg shadow-indigo-100' 
                            : 'bg-slate-50 hover:bg-indigo-50/50 border-slate-100 hover:border-indigo-100 text-slate-700 hover:text-indigo-600' ?>"
                        data-title="<?= esc(strtolower($c['title'])) ?>"
                        data-num="<?= esc($c['order_num']) ?>"
                    >
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-bold uppercase tracking-wider <?= $isActive ? 'text-indigo-200' : 'text-slate-400 group-hover/item:text-indigo-500' ?>">
                                    Bab <?= esc($c['order_num']) ?>
                                </span>
                                <?php if ($isActive): ?>
                                    <span class="px-2 py-0.5 rounded-full bg-white/20 text-[9px] font-bold text-white uppercase tracking-wider">
                                        Sedang Dibaca
                                    </span>
                                <?php endif; ?>
                            </div>
                            <p class="font-bold text-sm mt-1 truncate <?= $isActive ? 'text-white' : 'text-slate-800 group-hover/item:text-indigo-900' ?>">
                                <?= esc($c['title']) ?>
                            </p>
                        </div>
                        
                        <div class="flex items-center gap-2 shrink-0">
                           <?php if ($isLocked): ?>
                               <span class="w-8 h-8 rounded-full flex items-center justify-center text-sm <?= $isActive 
                                   ? 'bg-white/20 text-white' 
                                   : 'bg-amber-50 text-amber-600 border border-amber-100' ?>">
                                   <span class="material-symbols-outlined text-base">lock</span>
                               </span>
                           <?php else: ?>
                               <span class="material-symbols-outlined text-lg opacity-0 -translate-x-1 group-hover/item:opacity-100 group-hover/item:translate-x-0 transition-all duration-200 <?= $isActive ? 'text-white' : 'text-indigo-600' ?>">
                                   arrow_forward
                               </span>
                           <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Slide-over Drawer Script -->
    <script>
        function toggleChaptersDrawer(show) {
            const drawer = document.getElementById('chaptersDrawer');
            const overlay = document.getElementById('drawerOverlay');
            const panel = document.getElementById('drawerPanel');
            
            if (show) {
                drawer.classList.remove('invisible');
                // Give it a tiny tick before transitioning
                setTimeout(() => {
                    overlay.classList.remove('opacity-0');
                    overlay.classList.add('opacity-100');
                    panel.classList.remove('translate-x-full');
                    panel.classList.add('translate-x-0');
                }, 10);
                
                // Focus on search input
                setTimeout(() => {
                    const searchInput = document.getElementById('chapterSearch');
                    if (searchInput) searchInput.focus();
                }, 300);

                // Scroll the active chapter into view
                setTimeout(() => {
                    const activeItem = document.querySelector('.chapter-item.bg-gradient-to-r');
                    if (activeItem) {
                        activeItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }, 350);
            } else {
                overlay.classList.remove('opacity-100');
                overlay.classList.add('opacity-0');
                panel.classList.remove('translate-x-0');
                panel.classList.add('translate-x-full');
                
                setTimeout(() => {
                    drawer.classList.add('invisible');
                }, 300);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Instant Search Filtering
            const searchInput = document.getElementById('chapterSearch');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.trim().toLowerCase();
                    const items = document.querySelectorAll('.chapter-item');
                    
                    items.forEach(item => {
                        const title = item.dataset.title || '';
                        const num = item.dataset.num || '';
                        if (title.includes(query) || num.includes(query) || `bab ${num}`.includes(query)) {
                            item.style.display = 'flex';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            }

            // Close on Escape Key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    toggleChaptersDrawer(false);
                }
            });

            // Close when clicking overlay backdrop
            const overlay = document.getElementById('drawerOverlay');
            if (overlay) {
                overlay.addEventListener('click', () => {
                    toggleChaptersDrawer(false);
                });
            }
        });
    </script>
</body>
</html>
