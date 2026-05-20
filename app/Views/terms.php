<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= $title ?? 'Syarat dan Ketentuan - NusaShare' ?></title>
    
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
            background-color: #F8FAFC;
        }
        .prose h2 {
            margin-top: 2rem;
            margin-bottom: 1rem;
            font-weight: 700;
            font-size: 1.5rem;
            color: #1E293B;
        }
        .prose p {
            margin-bottom: 1rem;
            line-height: 1.75;
            color: #475569;
        }
    </style>
</head>
<body class="min-h-screen py-12 px-6">
    <div class="max-w-3xl mx-auto bg-white rounded-3xl shadow-sm border border-slate-200 p-8 md:p-12">
        <!-- Header -->
        <div class="flex items-center gap-3 mb-10">
            <a href="<?= base_url() ?>" class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center text-white transition-transform hover:scale-105">
                <img src="<?= base_url('assets/icon/logonus.png') ?>" alt="Logo N" class="w-7 h-7" />
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900">NusaShare</h1>
                <p class="text-slate-500 text-sm">Terakhir diperbarui: 20 Februari 2026</p>
            </div>
        </div>

        <h2 class="text-3xl font-extrabold text-slate-900 mb-8">Syarat dan Ketentuan</h2>

        <div class="prose">
            <p>Selamat datang di NusaShare. Dengan mengakses atau menggunakan platform kami, Anda setuju untuk terikat oleh Syarat dan Ketentuan berikut. Mohon baca dengan seksama.</p>

            <h2>1. Penggunaan Layanan</h2>
            <p>Anda setuju untuk menggunakan NusaShare hanya untuk tujuan yang sah dan sesuai dengan hukum yang berlaku di Republik Indonesia. Anda bertanggung jawab penuh atas aktivitas yang terjadi di bawah akun Anda.</p>

            <h2>2. Hak Kekayaan Intelektual</h2>
            <p>Seluruh karya yang diunggah ke platform ini tetap menjadi milik kreator asli. Namun, dengan mengunggah karya, Anda memberikan NusaShare lisensi non-eksklusif, bebas royalti, untuk menampilkan karya tersebut dalam layanan kami.</p>

            <h2>3. Akun Pengguna</h2>
            <p>Anda wajib memberikan informasi yang akurat saat mendaftar. Anda bertanggung jawab untuk menjaga kerahasiaan kata sandi Anda. Kami berhak menangguhkan akun yang melanggar ketentuan kami.</p>

            <h2>4. Batasan Tanggung Jawab</h2>
            <p>NusaShare tidak bertanggung jawab atas kerugian langsung atau tidak langsung yang timbul dari penggunaan atau ketidakmampuan menggunakan layanan kami.</p>

            <h2>5. Perubahan Ketentuan</h2>
            <p>Kami berhak untuk mengubah syarat dan ketentuan ini sewaktu-waktu. Perubahan akan berlaku segera setelah dipublikasikan di halaman ini.</p>
        </div>

        <!-- Footer -->
        <div class="mt-12 pt-8 border-t border-slate-200 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-slate-400 text-sm">© 2026 NusaShare. Hak cipta dilindungi undang-undang.</p>
            <div class="flex gap-6">
                <a href="<?= base_url('login') ?>" class="text-sm font-semibold text-[#4F46E5] hover:underline">Masuk</a>
                <a href="<?= base_url('privacy') ?>" class="text-sm font-semibold text-slate-600 hover:underline">Kebijakan Privasi</a>
            </div>
        </div>
    </div>
</body>
</html>
