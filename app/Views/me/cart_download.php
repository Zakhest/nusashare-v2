<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mengunduh File... - NusaShare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">
    <link rel="icon" href="<?= base_url('assets/icon/logonus.png') ?>" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * { font-family: 'Inter', sans-serif; }

        body {
            background: linear-gradient(135deg, #0F172A 0%, #1E1B4B 50%, #0F172A 100%);
            min-height: 100vh;
        }

        /* Animated background stars */
        .stars {
            position: fixed; inset: 0; pointer-events: none; overflow: hidden; z-index: 0;
        }
        .star {
            position: absolute;
            width: 2px; height: 2px;
            background: white;
            border-radius: 50%;
            animation: twinkle var(--dur, 3s) ease-in-out infinite var(--delay, 0s);
            opacity: 0;
        }
        @keyframes twinkle {
            0%,100% { opacity: 0; transform: scale(1); }
            50%      { opacity: var(--op, 0.7); transform: scale(1.5); }
        }

        /* Success icon animation */
        @keyframes scaleIn {
            0%   { transform: scale(0) rotate(-180deg); opacity: 0; }
            60%  { transform: scale(1.15) rotate(10deg); }
            100% { transform: scale(1) rotate(0deg); opacity: 1; }
        }
        .success-icon { animation: scaleIn 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) 0.3s both; }

        /* Progress bar */
        @keyframes progressFill {
            from { width: 0%; }
        }
        .progress-bar {
            animation: progressFill linear forwards;
            animation-duration: var(--total-duration, 3s);
        }

        /* File item entrance */
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-20px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        .file-item { animation: slideInLeft 0.4s ease forwards; opacity: 0; }

        /* Download indicator */
        @keyframes downloadPulse {
            0%,100% { transform: translateY(0); opacity: 1; }
            50%      { transform: translateY(3px); opacity: 0.6; }
        }
        .dl-arrow { animation: downloadPulse 1s ease infinite; }

        /* Checkmark */
        @keyframes checkPop {
            0%   { transform: scale(0); }
            70%  { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        .check-pop { animation: checkPop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }

        /* Glow ring */
        @keyframes glowRing {
            0%,100% { box-shadow: 0 0 0 0 rgba(99,102,241,.4); }
            50%      { box-shadow: 0 0 0 20px rgba(99,102,241,0); }
        }
        .glow-ring { animation: glowRing 2s ease infinite; }

        /* Float animation for card */
        @keyframes floatUp {
            from { opacity: 0; transform: translateY(40px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .float-up { animation: floatUp 0.7s cubic-bezier(0.34, 1.2, 0.64, 1) both; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-6 relative">

    <!-- Animated stars background -->
    <div class="stars" id="stars-bg"></div>

    <!-- Main Card -->
    <div class="relative z-10 w-full max-w-lg float-up">

        <!-- Success header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-indigo-600 glow-ring success-icon mb-6">
                <span class="material-symbols-outlined text-white text-5xl" style="font-variation-settings:'FILL' 1">task_alt</span>
            </div>
            <h1 class="text-3xl font-black text-white mb-2">Pembayaran Berhasil!</h1>
            <p class="text-indigo-300 text-sm">File Anda sedang disiapkan dan akan terunduh otomatis</p>
        </div>

        <!-- Card -->
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl overflow-hidden shadow-2xl">

            <!-- Progress bar -->
            <div class="h-1.5 bg-white/10">
                <div id="global-progress"
                     class="progress-bar h-full bg-gradient-to-r from-indigo-500 to-violet-500 rounded-full"
                     style="width: 0%; --total-duration: <?= max(count($items) * 2, 3) ?>s">
                </div>
            </div>

            <div class="p-6">
                <!-- Status text -->
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-indigo-400 rounded-full animate-pulse"></div>
                        <p id="status-text" class="text-white/80 text-sm font-medium">Memulai unduhan...</p>
                    </div>
                    <span class="text-white/40 text-xs font-mono" id="counter-text">0 / <?= count($items) ?></span>
                </div>

                <!-- File list -->
                <div class="space-y-3" id="file-list">
                    <?php foreach ($items as $i => $item): ?>
                    <div class="file-item flex items-center gap-3 bg-white/5 rounded-2xl p-3.5 border border-white/10"
                         id="file-item-<?= $i ?>"
                         style="animation-delay: <?= $i * 0.1 ?>s">
                        <!-- Format icon -->
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                                    <?= $item['format'] === 'ZIP' ? 'bg-indigo-500/20 text-indigo-300' : 'bg-amber-500/20 text-amber-300' ?>">
                            <span class="material-symbols-outlined text-[20px]">
                                <?= $item['format'] === 'ZIP' ? 'photo_library' : 'picture_as_pdf' ?>
                            </span>
                        </div>

                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <p class="text-white font-semibold text-sm truncate"><?= htmlspecialchars($item['title']) ?></p>
                            <p class="text-white/40 text-xs"><?= $item['format'] ?> &bull; NusaShare</p>
                        </div>

                        <!-- Status indicator -->
                        <div id="status-<?= $i ?>" class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 bg-white/5">
                            <span class="material-symbols-outlined text-[18px] text-white/30 dl-arrow">arrow_downward</span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Done state (hidden initially) -->
                <div id="done-section" class="hidden mt-5">
                    <div class="bg-emerald-500/10 border border-emerald-500/30 rounded-2xl p-4 text-center">
                        <p class="text-emerald-400 font-bold text-sm">✓ Semua file telah terunduh!</p>
                        <p class="text-emerald-300/60 text-xs mt-1">Periksa folder unduhan Anda</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation links -->
        <div class="flex items-center justify-center gap-6 mt-6 text-sm">
            <a href="<?= base_url('me/bookmarks') ?>" class="text-indigo-300 hover:text-white transition-colors flex items-center gap-1.5 font-medium">
                <span class="material-symbols-outlined text-[16px]">bookmark</span>
                Wishlist
            </a>
            <span class="text-white/20">|</span>
            <a href="<?= base_url('explore') ?>" class="text-indigo-300 hover:text-white transition-colors flex items-center gap-1.5 font-medium">
                <span class="material-symbols-outlined text-[16px]">explore</span>
                Jelajah Karya Lain
            </a>
            <span class="text-white/20">|</span>
            <a href="<?= base_url('me/cart') ?>" class="text-indigo-300 hover:text-white transition-colors flex items-center gap-1.5 font-medium">
                <span class="material-symbols-outlined text-[16px]">shopping_cart</span>
                Keranjang
            </a>
        </div>

        <!-- Re-download hint -->
        <p class="text-center text-white/20 text-xs mt-4">
            File tidak terunduh?
            <button id="retry-btn" class="text-indigo-400 hover:text-indigo-300 underline" onclick="retryAll()">Coba lagi</button>
        </p>
    </div>

    <!-- Hidden iframes for download triggering -->
    <div id="download-frames" class="hidden" aria-hidden="true"></div>

    <script>
        /* ── Build star background ── */
        (function() {
            const container = document.getElementById('stars-bg');
            for (let i = 0; i < 80; i++) {
                const s = document.createElement('div');
                s.className = 'star';
                s.style.cssText = `
                    left:${Math.random()*100}%;
                    top:${Math.random()*100}%;
                    --dur:${2+Math.random()*4}s;
                    --delay:${Math.random()*4}s;
                    --op:${0.3+Math.random()*0.6};
                    width:${1+Math.random()*2}px;
                    height:${1+Math.random()*2}px;
                `;
                container.appendChild(s);
            }
        })();

        /* ── Download queue ── */
        const downloads = <?= json_encode(array_values($items)) ?>;
        let completed   = 0;
        const total     = downloads.length;

        const statusText  = document.getElementById('status-text');
        const counterText = document.getElementById('counter-text');
        const framesEl    = document.getElementById('download-frames');

        function markDownloading(index) {
            const statusEl = document.getElementById('status-' + index);
            if (!statusEl) return;
            statusEl.innerHTML = `
                <span class="material-symbols-outlined text-[18px] text-indigo-400 dl-arrow">downloading</span>
            `;
            // Highlight the card
            const card = document.getElementById('file-item-' + index);
            if (card) {
                card.classList.add('border-indigo-500/40','bg-indigo-500/10');
            }
        }

        function markDone(index) {
            const statusEl = document.getElementById('status-' + index);
            if (!statusEl) return;
            statusEl.innerHTML = `
                <span class="material-symbols-outlined text-[18px] text-emerald-400 check-pop"
                      style="font-variation-settings:'FILL' 1">check_circle</span>
            `;
            const card = document.getElementById('file-item-' + index);
            if (card) {
                card.classList.remove('border-indigo-500/40','bg-indigo-500/10');
                card.classList.add('border-emerald-500/30','bg-emerald-500/5');
            }
        }

        function triggerDownload(index) {
            if (index >= total) {
                // All done
                statusText.textContent = 'Semua file berhasil diunduh!';
                counterText.textContent = total + ' / ' + total;
                document.getElementById('done-section').classList.remove('hidden');
                return;
            }

            const item = downloads[index];
            statusText.textContent = 'Mengunduh: ' + item.title + ' (' + item.format + ')';
            counterText.textContent = (index + 1) + ' / ' + total;

            markDownloading(index);

            // Create hidden iframe to trigger download
            // (more reliable than window.location or <a>.click() for binary files)
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = item.download_url;
            framesEl.appendChild(iframe);

            // Wait for download to start (give server time to respond)
            // then move to next file after delay
            setTimeout(() => {
                markDone(index);
                completed++;
                // Queue next download
                setTimeout(() => triggerDownload(index + 1), 800);
            }, 2500);
        }

        /* ── Retry all ── */
        function retryAll() {
            // Reset UI
            downloads.forEach((_, i) => {
                const card = document.getElementById('file-item-' + i);
                const statusEl = document.getElementById('status-' + i);
                if (card) card.className = card.className
                    .replace('border-emerald-500/30','border-white/10')
                    .replace('bg-emerald-500/5','bg-white/5');
                if (statusEl) statusEl.innerHTML = `
                    <span class="material-symbols-outlined text-[18px] text-white/30 dl-arrow">arrow_downward</span>
                `;
            });
            document.getElementById('done-section').classList.add('hidden');
            completed = 0;
            // Clear old iframes
            framesEl.innerHTML = '';
            // Restart
            setTimeout(() => triggerDownload(0), 500);
        }

        /* ── Start downloads after page load ── */
        window.addEventListener('load', function() {
            setTimeout(() => triggerDownload(0), 1200);
        });
    </script>
</body>
</html>
