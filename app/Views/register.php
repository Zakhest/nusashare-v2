<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= $title ?? 'Daftar - NusaShare' ?></title>
    
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
        :root {
            --brand-primary: #4F46E5;
            --brand-secondary: #22D3EE;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #FFFFFF;
        }

        .btn-gradient {
            background: linear-gradient(135deg, #4F46E5, #22D3EE);
            transition: all 0.3s ease;
        }
        .btn-gradient:hover {
            opacity: 0.95;
            box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.4);
            transform: translateY(-1px);
        }

        .input-field {
            transition: all 0.2s ease;
        }
        .input-field:focus {
            border-color: #4F46E5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        .ken-burns {
            animation: kenBurns 20s infinite alternate;
        }
        @keyframes kenBurns {
            from { transform: scale(1); }
            to { transform: scale(1.1); }
        }

        .glass-credit {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="min-h-screen w-full flex">

    <!-- LEFT SIDE: Visual (desktop only) -->
    <div class="hidden lg:block w-1/2 h-screen sticky top-0 shrink-0 relative overflow-hidden bg-slate-900">
        <img src="https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?q=80&w=1964&auto=format&fit=crop" alt="Abstract Art" class="absolute inset-0 w-full h-full object-cover ken-burns opacity-70">
        <div class="absolute inset-0 bg-gradient-to-t from-[#0F172A]/90 via-transparent to-transparent"></div>

        <div class="absolute top-8 left-8 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-white/20 backdrop-blur flex items-center justify-center text-white font-bold border border-white/10">
                <img src="<?= base_url('assets/icon/logonus.png') ?>" alt="Logo N" class="w-6 h-6" />
            </div>
            <span class="text-white font-bold text-lg tracking-tight drop-shadow-md">NusaShare</span>
        </div>

        <div class="absolute bottom-12 left-12 right-12">
            <h2 class="text-4xl font-bold text-white mb-4 leading-tight">Wujudkan Ide Kreatifmu Bersama Komunitas.</h2>
            <p class="text-slate-300 text-lg max-w-md leading-relaxed">
                Bergabunglah dengan ribuan kreator lainnya dan mulai bagikan karyamu ke seluruh Nusantara.
            </p>
        </div>
    </div>

    <!-- RIGHT SIDE: Register Form -->
    <div class="w-full lg:w-1/2 min-h-screen flex flex-col bg-white">

        <!-- Mobile Header Bar (logo kiri + tombol batal kanan, tidak bertabrakan) -->
        <div class="lg:hidden flex items-center justify-between px-5 pt-6 pb-2 flex-shrink-0">
            <div class="flex items-center gap-2">
                <img src="<?= base_url('assets/icon/logonus.png') ?>" alt="Logo N" class="w-8 h-8" />
                <span class="font-bold text-base text-slate-900">NusaShare</span>
            </div>
            <a href="<?= base_url('explore') ?>" class="flex items-center gap-1 text-sm text-slate-500 hover:text-[#4F46E5] transition-colors group">
                <span class="material-symbols-outlined text-base group-hover:-translate-x-1 transition-transform">arrow_back</span>
                <span>Batal</span>
            </a>
        </div>

        <!-- Desktop Back Button -->
        <a href="<?= base_url('explore') ?>" class="hidden lg:flex absolute top-8 right-8 items-center gap-1 text-sm text-slate-500 hover:text-[#4F46E5] transition-colors group">
            <span class="material-symbols-outlined text-lg group-hover:-translate-x-1 transition-transform">arrow_back</span>
            Batal
        </a>

        <!-- Form Content -->
        <div class="flex-1 flex flex-col justify-center items-center px-5 sm:px-10 md:px-16 py-8">
            <div class="w-full max-w-md">

                <!-- Page Header -->
                <div class="mb-6 sm:mb-8 text-center lg:text-left">
                    <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-2">Buat Akun Baru.</h1>
                    <p class="text-sm sm:text-base text-slate-500">Mulai langkah pertamamu sebagai bagian dari NusaShare.</p>
                </div>

                <!-- Notifications -->
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded-r-xl">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded-r-xl">
                        <ul class="list-disc ml-4">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Register Form -->
                <form action="<?= base_url('register') ?>" method="POST" class="space-y-4">
                    <div>
                        <label for="username" class="block text-sm font-medium text-slate-700 mb-1.5">Username</label>
                        <input type="text" id="username" name="username"
                            class="input-field w-full px-4 py-3 rounded-xl border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none"
                            placeholder="budisantoso" value="<?= old('username') ?>" required>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                        <input type="email" id="email" name="email"
                            class="input-field w-full px-4 py-3 rounded-xl border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none"
                            placeholder="budi@example.com" value="<?= old('email') ?>" required>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Kata Sandi</label>
                        <input type="password" id="password" name="password"
                            class="input-field w-full px-4 py-3 rounded-xl border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none"
                            placeholder="••••••••" required>
                    </div>

                    <!-- Terms Checkbox -->
                    <div class="flex items-start gap-3 mt-2">
                        <input type="checkbox" id="terms"
                            class="mt-0.5 h-4 w-4 flex-shrink-0 rounded border-slate-300 text-[#4F46E5] focus:ring-[#4F46E5]" required>
                        <label for="terms" class="text-xs text-slate-500 leading-relaxed">
                            Saya menyetujui
                            <a href="<?= base_url('terms') ?>" class="text-[#4F46E5] font-semibold hover:underline">Syarat &amp; Ketentuan</a>
                            serta
                            <a href="<?= base_url('privacy') ?>" class="text-[#4F46E5] font-semibold hover:underline">Kebijakan Privasi</a>
                            NusaShare.
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="btn-gradient w-full py-3.5 rounded-xl text-white font-bold text-base sm:text-lg shadow-lg shadow-indigo-500/30 mt-4">
                        Daftar Sekarang
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-3 bg-white text-slate-400">atau daftar dengan</span>
                    </div>
                </div>

                <!-- Social Login Buttons -->
                <div class="flex gap-3">
                    <button type="button" class="flex-1 flex items-center justify-center gap-2 py-2.5 border border-slate-200 rounded-xl hover:bg-slate-50 hover:border-slate-300 transition-all">
                        <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5 flex-shrink-0" alt="Google">
                        <span class="text-sm font-medium text-slate-700">Google</span>
                    </button>
                    <button type="button" class="flex-1 flex items-center justify-center gap-2 py-2.5 border border-slate-200 rounded-xl hover:bg-slate-50 hover:border-slate-300 transition-all">
                        <img src="https://www.svgrepo.com/show/442938/apple-logo.svg" class="w-5 h-5 flex-shrink-0" alt="Apple">
                        <span class="text-sm font-medium text-slate-700">Apple</span>
                    </button>
                </div>

                <!-- Footer Login Link -->
                <p class="mt-6 text-center text-sm text-slate-600">
                    Sudah punya akun?
                    <a href="<?= base_url('login') ?>" class="font-bold text-[#4F46E5] hover:underline">Masuk di sini</a>
                </p>

            </div>
        </div>
    </div>

</body>
</html>
