<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= $title ?? 'Daftar Kreator - NusaShare' ?></title>
    
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
            overflow-x: hidden;
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
            padding: 3rem 1rem;
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
            max-width: 500px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            margin: auto;
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

        /* Checkbox styling */
        .kreator-checkbox {
            appearance: none;
            width: 1.25rem;
            height: 1.25rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 0.25rem;
            background: rgba(255, 255, 255, 0.1);
            cursor: pointer;
            position: relative;
            transition: all 0.2s;
        }
        .kreator-checkbox:checked {
            background: #ec4899;
            border-color: #ec4899;
        }
        .kreator-checkbox:checked::after {
            content: 'check';
            font-family: 'Material Symbols Outlined';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 1rem;
        }
    </style>
</head>
<body class="kreator-gradient">

    <a href="<?= base_url('/') ?>" class="back-link">
        <span class="material-symbols-outlined">arrow_back</span>
        Kembali
    </a>

    <div class="kreator-card">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Jadi Kreator</h1>
            <p class="text-white/60">Bergabunglah dengan komunitas penulis berbakat.</p>
        </div>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="mb-6 p-4 bg-red-500/20 border border-red-500/50 text-red-100 text-sm rounded-xl backdrop-blur-sm">
                <ul class="list-disc ml-4">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('creator/register') ?>" method="POST" class="space-y-5">
            <div>
                <label for="display_name" class="glass-label">Nama Display</label>
                <input type="text" id="display_name" name="display_name" class="kreator-input" placeholder="Budi Santoso" value="<?= old('display_name') ?>" required autofocus>
            </div>

            <div>
                <label for="bio" class="glass-label">Bio Kreator</label>
                <textarea id="bio" name="bio" class="kreator-input h-32 resize-none" placeholder="Ceritakan sedikit tentang karya Anda..." required><?= old('bio') ?></textarea>
            </div>

            <div class="flex items-start gap-3 py-2">
                <input type="checkbox" id="terms" class="kreator-checkbox mt-0.5" required>
                <label for="terms" class="text-xs text-white/70 leading-normal">
                    Saya menyetujui <a href="<?= base_url('terms') ?>" class="text-white font-semibold hover:underline">Syarat & Ketentuan</a> serta <a href="<?= base_url('privacy') ?>" class="text-white font-semibold hover:underline">Kebijakan Privasi</a> sebagai Kreator NusaShare.
                </label>
            </div>

            <button type="submit" class="kreator-button mt-2">
                Daftar Sebagai Kreator
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-white/60">
            Sudah punya akun kreator? 
            <a href="<?= base_url('creator/login') ?>" class="font-bold text-white hover:underline underline-offset-4">Masuk di sini</a>
        </p>
    </div>
</body>
</html>
