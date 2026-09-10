<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Ulang Kata Sandi - NusaShare</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f1f5f9;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #334155;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 580px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #4F46E5 0%, #22D3EE 100%);
            padding: 36px 30px;
            text-align: center;
        }
        .logo-box {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 10px 20px;
            border-radius: 9999px;
            margin-bottom: 12px;
        }
        .brand-name {
            color: #ffffff;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin: 0;
        }
        .header-title {
            color: #ffffff;
            font-size: 20px;
            font-weight: 600;
            margin: 10px 0 0 0;
        }
        .content {
            padding: 36px 32px;
            line-height: 1.6;
        }
        .greeting {
            font-size: 16px;
            font-weight: 600;
            color: #0f172a;
            margin-top: 0;
            margin-bottom: 16px;
        }
        .text {
            font-size: 15px;
            color: #475569;
            margin-bottom: 24px;
        }
        .btn-wrapper {
            text-align: center;
            margin: 32px 0;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #4F46E5 0%, #4338CA 100%);
            color: #ffffff !important;
            text-decoration: none;
            font-weight: 700;
            font-size: 15px;
            padding: 14px 34px;
            border-radius: 12px;
            box-shadow: 0 4px 14px 0 rgba(79, 70, 229, 0.35);
            letter-spacing: 0.2px;
        }
        .notice-box {
            background-color: #f8fafc;
            border-left: 4px solid #6366f1;
            padding: 14px 18px;
            border-radius: 0 10px 10px 0;
            margin-bottom: 24px;
        }
        .notice-text {
            font-size: 13px;
            color: #64748b;
            margin: 0;
        }
        .alt-link-box {
            background: #f1f5f9;
            padding: 12px 16px;
            border-radius: 8px;
            word-break: break-all;
            font-size: 12px;
            color: #4F46E5;
            margin-top: 8px;
        }
        .footer {
            background-color: #f8fafc;
            padding: 24px 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        .footer-text {
            font-size: 12px;
            color: #94a3b8;
            margin: 4px 0;
        }
        .footer-links a {
            color: #6366f1;
            text-decoration: none;
            margin: 0 8px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo-box">
                <span class="brand-name">NusaShare</span>
            </div>
            <h1 class="header-title">Permintaan Atur Ulang Kata Sandi</h1>
        </div>

        <!-- Body Content -->
        <div class="content">
            <p class="greeting">Halo, <?= esc($username ?? 'Pengguna NusaShare') ?>!</p>
            <p class="text">
                Kami menerima permintaan untuk mereset kata sandi akun NusaShare yang terhubung dengan alamat email ini (<strong><?= esc($email) ?></strong>).
            </p>
            <p class="text">
                Untuk melanjutkan dan membuat kata sandi baru, silakan klik tombol di bawah ini:
            </p>

            <div class="btn-wrapper">
                <a href="<?= esc($resetLink) ?>" class="btn" target="_blank">Atur Ulang Kata Sandi</a>
            </div>

            <div class="notice-box">
                <p class="notice-text">
                    ⚠️ <strong>Penting:</strong> Tautan ini hanya berlaku selama <strong>1 jam</strong>. Jika Anda tidak pernah meminta pengaturan ulang kata sandi ini, abaikan saja email ini. Akun Anda tetap aman.
                </p>
            </div>

            <p class="text" style="font-size: 13px; color: #64748b; margin-bottom: 6px;">
                Jika tombol di atas tidak berfungsi, salin dan tempel URL berikut ke browser Anda:
            </p>
            <div class="alt-link-box">
                <?= esc($resetLink) ?>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p class="footer-text">&copy; <?= date('Y') ?> NusaShare. Seluruh hak cipta dilindungi.</p>
            <p class="footer-text">Email ini dikirim secara otomatis, mohon untuk tidak membalas email ini.</p>
        </div>
    </div>
</body>
</html>
