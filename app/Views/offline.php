<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kamu Sedang Offline | NusaShare</title>
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <meta name="theme-color" content="#4F46E5">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary:  #4F46E5;
            --accent:   #22D3EE;
            --bg:       #EEF2FF;
            --surface:  #FFFFFF;
            --text:     #0F172A;
            --muted:    #475569;
        }

        html, body { height: 100%; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            overflow: hidden;
            position: relative;
        }

        /* Blobs */
        .blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.3;
            animation: float 8s ease-in-out infinite alternate;
            pointer-events: none;
            z-index: 0;
        }
        .blob-1 { width: 480px; height: 480px; background: radial-gradient(circle, #4F46E5, #818CF8); top: -140px; left: -140px; }
        .blob-2 { width: 360px; height: 360px; background: radial-gradient(circle, #22D3EE, #67E8F9); bottom: -100px; right: -100px; animation-delay: 3s; }
        @keyframes float {
            from { transform: translate(0,0) scale(1); }
            to   { transform: translate(25px,-25px) scale(1.05); }
        }

        /* Card */
        .card {
            position: relative;
            z-index: 1;
            background: rgba(255,255,255,0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.9);
            border-radius: 28px;
            padding: 52px 56px 44px;
            max-width: 520px;
            width: 90%;
            text-align: center;
            box-shadow: 0 4px 6px -1px rgba(79,70,229,0.06), 0 20px 60px rgba(79,70,229,0.12);
        }

        /* Signal animation */
        .signal-wrap {
            position: relative;
            width: 80px;
            height: 80px;
            margin: 0 auto 28px;
        }
        .signal-icon {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            background: linear-gradient(135deg, #4F46E5, #22D3EE);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 2;
            box-shadow: 0 8px 24px rgba(79,70,229,0.35);
        }
        .signal-icon svg { width: 38px; height: 38px; color: white; }
        .signal-ring {
            position: absolute;
            inset: -10px;
            border-radius: 30px;
            border: 2px solid rgba(79,70,229,0.3);
            animation: ring-pulse 2s ease-out infinite;
        }
        .signal-ring:nth-child(2) { inset: -20px; border-radius: 38px; animation-delay: 0.5s; border-color: rgba(79,70,229,0.15); }
        @keyframes ring-pulse {
            0%   { transform: scale(0.9); opacity: 0.8; }
            100% { transform: scale(1.15); opacity: 0; }
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(79,70,229,0.08);
            color: var(--primary);
            font-size: 12px;
            font-weight: 600;
            padding: 5px 14px;
            border-radius: 999px;
            border: 1px solid rgba(79,70,229,0.15);
            margin-bottom: 16px;
        }
        .badge-dot {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: #EF4444;
            animation: blink 1.5s ease-in-out infinite;
        }
        @keyframes blink {
            0%,100% { opacity: 1; }
            50%      { opacity: 0.2; }
        }

        h1 { font-size: 22px; font-weight: 700; margin-bottom: 10px; }
        .subtitle { font-size: 15px; color: var(--muted); line-height: 1.7; }

        /* Cached pages list */
        .cached-list {
            margin-top: 24px;
            background: rgba(79,70,229,0.04);
            border: 1px solid rgba(79,70,229,0.12);
            border-radius: 14px;
            padding: 14px 16px;
            text-align: left;
        }
        .cached-list p { font-size: 12px; color: var(--muted); font-weight: 600; margin-bottom: 8px; letter-spacing: 0.04em; text-transform: uppercase; }
        .cached-list a {
            display: flex; align-items: center; gap: 8px;
            padding: 7px 10px; border-radius: 8px;
            font-size: 13px; color: var(--primary); font-weight: 500;
            text-decoration: none; transition: background 0.2s;
        }
        .cached-list a:hover { background: rgba(79,70,229,0.08); }
        .cached-list svg { width: 14px; height: 14px; flex-shrink: 0; }

        /* Buttons */
        .btn-group { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; margin-top: 28px; }
        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 12px 24px; border-radius: 12px;
            font-family: 'Inter', sans-serif; font-size: 14px; font-weight: 600;
            cursor: pointer; text-decoration: none; transition: all 0.25s ease; border: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, #4F46E5, #22D3EE);
            color: #fff; box-shadow: 0 8px 24px rgba(79,70,229,0.3);
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 14px 32px rgba(79,70,229,0.4); }
        .btn-ghost {
            background: rgba(79,70,229,0.07); color: var(--primary);
            border: 1px solid rgba(79,70,229,0.15);
        }
        .btn-ghost:hover { background: rgba(79,70,229,0.12); transform: translateY(-2px); }
        .btn svg { width: 16px; height: 16px; }

        .footer { position: fixed; bottom: 24px; font-size: 13px; color: var(--muted); z-index: 1; }
        .footer a { color: var(--primary); text-decoration: none; font-weight: 500; }
    </style>
</head>
<body>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="card" role="main">

        <!-- Signal icon -->
        <div class="signal-wrap">
            <div class="signal-ring"></div>
            <div class="signal-ring"></div>
            <div class="signal-icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 1L23 23" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M16.72 11.06A10.94 10.94 0 0 1 19 12.55" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M5 12.55a10.94 10.94 0 0 1 5.17-2.39" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M10.71 5.05A16 16 0 0 1 22.56 9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M1.42 9a15.91 15.91 0 0 1 4.7-2.88" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M13.33 17.11a4 4 0 0 0-6.66 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <circle cx="12" cy="21" r="1" fill="currentColor"/>
                </svg>
            </div>
        </div>

        <!-- Badge -->
        <div class="badge">
            <span class="badge-dot"></span>
            Tidak ada koneksi
        </div>

        <h1>Kamu Sedang Offline</h1>
        <p class="subtitle">
            Sepertinya koneksi internet kamu terputus.<br>
            Halaman yang sudah dikunjungi tetap bisa dibuka.
        </p>

        <!-- Cached pages shortcut -->
        <div class="cached-list" id="cached-pages-list">
            <p>Halaman tersedia offline</p>
            <a href="/">
                <svg viewBox="0 0 24 24" fill="none"><path d="M3 12L12 3L21 12V21H15V15H9V21H3V12Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
                Beranda
            </a>
            <a href="/explore">
                <svg viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/><path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                Jelajahi Karya
            </a>
            <a href="/dashboard">
                <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/><rect x="14" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/><rect x="3" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/><rect x="14" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/></svg>
                Dashboard
            </a>
        </div>

        <div class="btn-group">
            <button onclick="tryReload()" class="btn btn-primary" id="retry-btn">
                <svg viewBox="0 0 24 24" fill="none"><path d="M1 4V10H7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M3.51 15A9 9 0 1 0 5.64 5.64L1 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Coba Lagi
            </button>
            <a href="/" class="btn btn-ghost">
                <svg viewBox="0 0 24 24" fill="none"><path d="M3 12L12 3L21 12V21H15V15H9V21H3V12Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
                Beranda
            </a>
        </div>
    </div>

    <footer class="footer">
        &copy; <?= date('Y') ?> <a href="/">NusaShare</a> &mdash; Platform Kreator Indonesia
    </footer>

    <script>
        function tryReload() {
            const btn = document.getElementById('retry-btn');
            btn.disabled = true;
            btn.innerHTML = `
                <svg style="animation:spin 1s linear infinite" viewBox="0 0 24 24" fill="none">
                    <path d="M1 4V10H7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M3.51 15A9 9 0 1 0 5.64 5.64L1 10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                Menghubungkan...
            `;
            // Coba reload setelah 300ms delay biar animasi kelihatan
            setTimeout(() => window.location.reload(), 400);
        }

        // Otomatis reload saat koneksi kembali
        window.addEventListener('online', () => {
            setTimeout(() => window.location.reload(), 500);
        });
    </script>

    <style>
        @keyframes spin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }
    </style>
</body>
</html>
