<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 – Halaman Tidak Ditemukan | NusaShare</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --primary:   #4F46E5;
            --primary-light: #6366F1;
            --accent:    #22D3EE;
            --bg:        #EEF2FF;
            --surface:   #FFFFFF;
            --text-main: #0F172A;
            --text-muted:#475569;
            --border:    rgba(79, 70, 229, 0.15);
        }

        html, body {
            height: 100%;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text-main);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            overflow: hidden;
            position: relative;
        }

        /* ── Animated background blobs ── */
        .blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.35;
            animation: float 8s ease-in-out infinite alternate;
            pointer-events: none;
            z-index: 0;
        }
        .blob-1 {
            width: 520px; height: 520px;
            background: radial-gradient(circle, #4F46E5, #818CF8);
            top: -160px; left: -160px;
            animation-delay: 0s;
        }
        .blob-2 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, #22D3EE, #67E8F9);
            bottom: -120px; right: -120px;
            animation-delay: 3s;
        }
        .blob-3 {
            width: 260px; height: 260px;
            background: radial-gradient(circle, #A78BFA, #C4B5FD);
            top: 50%; left: 60%;
            animation-delay: 1.5s;
        }
        @keyframes float {
            from { transform: translate(0, 0) scale(1); }
            to   { transform: translate(30px, -30px) scale(1.05); }
        }

        /* ── Main card ── */
        .card {
            position: relative;
            z-index: 1;
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.9);
            border-radius: 28px;
            padding: 56px 60px 48px;
            max-width: 560px;
            width: 90%;
            text-align: center;
            box-shadow:
                0 4px 6px -1px rgba(79,70,229,0.06),
                0 20px 60px rgba(79,70,229,0.12),
                0 1px 0 rgba(255,255,255,0.8) inset;
        }

        /* ── 404 number ── */
        .error-number {
            font-size: clamp(96px, 18vw, 140px);
            font-weight: 900;
            line-height: 1;
            letter-spacing: -6px;
            background: linear-gradient(135deg, #4F46E5 0%, #22D3EE 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            user-select: none;
            animation: pulse-glow 3s ease-in-out infinite;
        }
        @keyframes pulse-glow {
            0%, 100% { filter: drop-shadow(0 0 12px rgba(79,70,229,0.35)); }
            50%       { filter: drop-shadow(0 0 28px rgba(34,211,238,0.5)); }
        }

        /* ── Brand badge ── */
        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #4F46E5, #22D3EE);
            color: white;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.04em;
            padding: 6px 16px;
            border-radius: 999px;
            margin-bottom: 20px;
            box-shadow: 0 4px 14px rgba(79,70,229,0.3);
        }
        .brand-badge svg {
            width: 16px; height: 16px;
        }

        /* ── Divider ── */
        .divider {
            width: 48px;
            height: 4px;
            background: linear-gradient(90deg, #4F46E5, #22D3EE);
            border-radius: 2px;
            margin: 20px auto 24px;
        }

        /* ── Texts ── */
        h1 {
            font-size: 22px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 10px;
        }
        .subtitle {
            font-size: 15px;
            color: var(--text-muted);
            line-height: 1.7;
        }

        /* ── Error message (dev only) ── */
        .dev-message {
            margin-top: 20px;
            background: rgba(79,70,229,0.06);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 13px;
            color: var(--text-muted);
            text-align: left;
            font-family: 'Inter', monospace;
            word-break: break-word;
        }

        /* ── Buttons ── */
        .btn-group {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 32px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.25s ease;
            border: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, #4F46E5, #22D3EE);
            color: #fff;
            box-shadow: 0 8px 24px rgba(79,70,229,0.3);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 32px rgba(79,70,229,0.4);
        }
        .btn-ghost {
            background: rgba(79,70,229,0.07);
            color: var(--primary);
            border: 1px solid var(--border);
        }
        .btn-ghost:hover {
            background: rgba(79,70,229,0.12);
            transform: translateY(-2px);
        }
        .btn svg {
            width: 16px; height: 16px;
        }

        /* ── Footer ── */
        .footer {
            position: fixed;
            bottom: 24px;
            font-size: 13px;
            color: var(--text-muted);
            z-index: 1;
        }
        .footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        .footer a:hover { text-decoration: underline; }

        /* ── Floating particles ── */
        .particles {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }
        .particle {
            position: absolute;
            border-radius: 50%;
            animation: rise linear infinite;
            opacity: 0;
        }
        @keyframes rise {
            0%   { transform: translateY(100vh) scale(0); opacity: 0; }
            10%  { opacity: 0.6; }
            90%  { opacity: 0.3; }
            100% { transform: translateY(-10vh) scale(1); opacity: 0; }
        }
    </style>
</head>
<body>

    <!-- Blobs -->
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <!-- Particles (JS-generated) -->
    <div class="particles" id="particles"></div>

    <!-- Card -->
    <div class="card" role="main">

        <!-- Brand badge -->
        <div class="brand-badge">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2L2 7L12 12L22 7L12 2Z" fill="currentColor" opacity="0.8"/>
                <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            NusaShare
        </div>

        <!-- 404 number -->
        <div class="error-number" aria-hidden="true">404</div>

        <!-- Divider -->
        <div class="divider"></div>

        <!-- Heading & description -->
        <h1>Halaman Tidak Ditemukan</h1>
        <p class="subtitle">
            Sepertinya halaman yang kamu cari sudah berpindah tempat,<br>
            dihapus, atau tidak pernah ada sebelumnya.
        </p>

        <?php if (ENVIRONMENT !== 'production') : ?>
        <div class="dev-message">
            <strong>Debug:</strong> <?= nl2br(esc($message)) ?>
        </div>
        <?php endif; ?>

        <!-- Action buttons -->
        <div class="btn-group">
            <a href="/" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 12L12 3L21 12V21H15V15H9V21H3V12Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                </svg>
                Kembali ke Beranda
            </a>
            <button onclick="history.back()" class="btn btn-ghost">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Kembali
            </button>
        </div>

    </div>

    <!-- Footer -->
    <footer class="footer">
        &copy; <?= date('Y') ?> <a href="/">NusaShare</a> &mdash; Platform Kreator Indonesia
    </footer>

    <script>
        // Generate floating particles
        const container = document.getElementById('particles');
        const colors = ['#4F46E5', '#22D3EE', '#A78BFA', '#818CF8'];
        const count = 18;

        for (let i = 0; i < count; i++) {
            const el = document.createElement('div');
            el.className = 'particle';
            const size = Math.random() * 10 + 4;
            el.style.cssText = `
                width: ${size}px;
                height: ${size}px;
                left: ${Math.random() * 100}%;
                background: ${colors[Math.floor(Math.random() * colors.length)]};
                animation-duration: ${Math.random() * 12 + 10}s;
                animation-delay: ${Math.random() * 10}s;
            `;
            container.appendChild(el);
        }
    </script>
</body>
</html>
