<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= $title ?? 'Login Kreator - NusaShare' ?></title>
    
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
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        /* Gradient background */
        .kreator-gradient {
            background: linear-gradient(135deg, #4f46e5, #9333ea, #ec4899, #f97316);
            background-size: 400% 400%;
            animation: gradientMove 15s ease infinite;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Glassmorphism card */
        .kreator-card {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 1.5rem;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            width: 100%;
            max-width: 450px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        /* Input field */
        .kreator-input {
            width: 100%;
            padding: 0.875rem 1.25rem;
            border-radius: 0.75rem;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            outline: none;
            transition: all 0.3s ease;
        }
        .kreator-input:focus {
            border: 1px solid #ec4899;
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 0 4px rgba(236, 72, 153, 0.2);
        }

        /* Gradient button */
        .kreator-button {
            background: linear-gradient(to right, #4f46e5, #ec4899, #f97316);
            color: white;
            padding: 0.875rem;
            border-radius: 0.75rem;
            font-weight: 700;
            transition: all 0.3s ease;
            width: 100%;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .kreator-button:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(236, 72, 153, 0.5);
        }
        .kreator-button:active {
            transform: translateY(0);
        }

        .glass-label {
            display: block;
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .back-link {
            position: absolute;
            top: 2rem;
            left: 2rem;
            color: white;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            transition: opacity 0.2s;
            z-index: 10;
        }
        .back-link:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body class="kreator-gradient">

    <a href="<?= base_url('/') ?>" class="back-link">
        <span class="material-symbols-outlined">arrow_back</span>
        Kembali ke Beranda
    </a>

    <div class="kreator-card">
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-white/10 backdrop-blur border border-white/20 mb-4">
                <img src="<?= base_url('assets/icon/logonus.png') ?>" alt="Logo" class="w-10 h-10">
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Login Kreator</h1>
            <p class="text-white/60">Kelola karya dan penghasilan Anda secara profesional.</p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="mb-6 p-4 bg-red-500/20 border border-red-500/50 text-red-100 text-sm rounded-xl backdrop-blur-sm">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="mb-6 p-4 bg-emerald-500/20 border border-emerald-500/50 text-emerald-100 text-sm rounded-xl backdrop-blur-sm">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('creator/login') ?>" method="POST" class="space-y-6">
            <div>
                <label for="id" class="glass-label">ID Kreator</label>
                <input type="text" id="id" name="id" class="kreator-input" placeholder="ABC123XYZ" required autofocus>
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="password" class="glass-label mb-0">Kata Sandi</label>
                    <a href="<?= base_url('forgot-password') ?>" class="text-xs text-white/50 hover:text-white transition-colors">Lupa sandi?</a>
                </div>
                <div class="relative">
                    <input type="password" id="password" name="password" class="kreator-input" placeholder="••••••••" required>
                    <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 -translate-y-1/2 text-white/40 hover:text-white/70 transition-colors">
                        <span class="material-symbols-outlined text-xl" id="eye-icon">visibility</span>
                    </button>
                </div>
            </div>

            <button type="submit" class="kreator-button mt-4">
                Masuk
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-white/60">
            Belum terdaftar sebagai kreator? 
            <a href="<?= base_url('creator/register') ?>" class="font-bold text-white hover:underline underline-offset-4">Daftar sekarang</a>
        </p>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.textContent = 'visibility_off';
            } else {
                passwordInput.type = 'password';
                eyeIcon.textContent = 'visibility';
            }
        }
    </script>
</body>
</html>
