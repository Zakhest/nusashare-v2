<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= $title ?? 'Atur Ulang Kata Sandi - NusaShare' ?></title>
    
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
    </style>
</head>
<body class="h-screen w-full flex items-center justify-center bg-slate-50 px-6">

    <div class="w-full max-w-md">
        <div class="bg-white p-8 rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-100">
            <!-- Header -->
            <div class="mb-8 text-center">
                <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-[#4F46E5] mx-auto mb-4">
                    <span class="material-symbols-outlined text-2xl">password</span>
                </div>
                <h1 class="text-2xl font-bold text-slate-900 mb-2">Atur Ulang Sandi</h1>
                <p class="text-slate-500 text-sm leading-relaxed">Silakan masukkan kata sandi baru Anda di bawah ini.</p>
            </div>

            <!-- Notifications -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded-r-xl">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded-r-xl">
                    <ul class="list-disc ml-4">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form action="<?= base_url('reset-password') ?>" method="POST" class="space-y-6">
                <!-- Hidden Token -->
                <input type="hidden" name="token" value="<?= $token ?>">

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Kata Sandi Baru</label>
                    <input type="password" id="password" name="password" class="input-field w-full px-4 py-3 rounded-xl border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none" placeholder="••••••••" required>
                </div>

                <div>
                    <label for="password_confirm" class="block text-sm font-medium text-slate-700 mb-1.5">Konfirmasi Kata Sandi</label>
                    <input type="password" id="password_confirm" name="password_confirm" class="input-field w-full px-4 py-3 rounded-xl border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn-gradient w-full py-3.5 rounded-xl text-white font-bold text-lg shadow-lg shadow-indigo-500/30">
                    Perbarui Kata Sandi
                </button>
            </form>
        </div>
    </div>

</body>
</html>
