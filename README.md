# NusaShare

NusaShare adalah aplikasi web berbagi karya untuk kreator dan pembaca. Platform ini menyediakan ruang publik untuk menemukan karya, area pengguna untuk membaca dan mendukung kreator, dashboard kreator untuk mengelola konten dan monetisasi, serta panel admin untuk memantau operasional platform.

Proyek ini dibangun dengan CodeIgniter 4 dan berjalan di atas PHP 8.1 atau lebih baru. Status proyek saat ini masih PoC/produk pengembangan aktif.

## Ringkasan Fitur Terbaru

- Landing page, halaman legal, login/register user, login/register kreator, forgot password, dan reset password.
- Explore karya dengan spotlight, filter genre/tipe, mode gallery, mode story, pencarian karya, dan pencarian user/kreator.
- Format konten: text, novel, light novel, comic, image, dan PDF pada skema konten.
- Detail karya, reader chapter, navigasi chapter, komentar per karya/chapter, hapus komentar sendiri, like, bookmark, follow/unfollow kreator, statistik follow, dan profil publik.
- View counter dengan threshold baca 2 menit agar statistik lebih bermakna.
- Reading history dan resume terakhir dibaca untuk pengguna login.
- Sistem Cooling Credit (CC) untuk top up, unlock karya, unlock chapter, checkout cart, download karya, dan pendapatan kreator.
- Konten berbayar mendukung akses full, akses per chapter, harga preview, harga beli permanen, timer preview, watermark gambar, blur/lock state, dan chapter terkunci.
- Cart karya downloadable dengan checkout CC, halaman auto-download setelah checkout, download ZIP untuk karya gambar, dan export PDF untuk karya teks/novel/light novel/comic.
- Dashboard pengguna untuk profil, bookmark/koleksi, cart, follows, top up, dan notifikasi realtime/AJAX.
- Dashboard kreator untuk statistik ringkas, manajemen karya, chapter, galeri gambar, publish/archive/delete, statistik karya, analitik per karya, monetisasi, penarikan dummy, riwayat transaksi, export Excel/PDF, dan receipt transaksi.
- Panel admin `alpha-admin` untuk dashboard statistik, user management, creator management, status Starsoul, penyesuaian saldo CC, transaksi, top up, ekonomi, konten, laporan, gallery, CMS landing/page/FAQ, audit, sistem, dan notifikasi.
- API v1 untuk login/register/logout, daftar karya, detail karya, profil user, like/comment/bookmark, saldo top up, dan checkout top up.
- Proxy gambar untuk cover, galeri, profil, gambar chapter, remote image, blur/lock state, dan watermark.
- Notification service terpusat untuk follow, like, comment, purchase, unlock, unlock chapter, top up, dan notifikasi sistem.

## Teknologi

- PHP 8.1+
- CodeIgniter 4
- MySQL atau MariaDB
- Composer
- Dompdf
- PHPUnit
- CSS/JavaScript native
- Tailwind CDN pada beberapa halaman
- Chart.js pada area admin/dashboard tertentu
- ZipArchive untuk paket download gambar

## Struktur Folder

```text
app/
  Commands/        Command lokal tambahan
  Config/          Konfigurasi aplikasi, route, filter, database, session
  Controllers/     Controller web, creator area, admin, dan API
  Database/        Migration dan seeder
  Filters/         Auth filter untuk web dan API
  Models/          Model database
  Services/        Service konten, file upload, dan notifikasi
  ThirdParty/      Library vendor lokal tambahan
  Views/           Tampilan publik, user, creator, admin, auth, works
assets/            Asset CSS, JavaScript, dan icon aplikasi
public/            Public document root dan asset publik
tests/             Test PHPUnit
tmp/               Script sementara untuk perubahan database
vendor/            Dependency Composer
writable/          Log, session, upload, download sementara, cache, debugbar
```

## Modul Aplikasi

### Public Area

- `/` landing page
- `/explore` eksplorasi semua karya
- `/explore/gallery` eksplorasi karya gambar
- `/explore/story` eksplorasi novel, light novel, dan comic
- `/search` pencarian karya dan pengguna
- `/works/{id}` detail karya
- `/works/{id}/read/{chapterId}` baca chapter
- `/works/{id}/download` download ZIP untuk karya gambar gratis
- `/content/{segment}` alias halaman konten
- `/user/{username}` profil publik pengguna/kreator
- `/creator/{username}` profil publik kreator
- `/terms` dan `/privacy`

### User Area

Area ini memerlukan login sebagai user.

- `/dashboard`
- `/me/profile`
- `/me/bookmarks`
- `/me/cart`
- `/me/follows`
- `/topup`
- `POST /cart/add/{id}`
- `POST /cart/remove/{id}`
- `POST /cart/checkout`
- `GET /cart/download-all`
- `GET /cart/download/{id}`
- `POST /works/{id}/like`
- `POST /works/{id}/comment`
- `POST /works/{id}/unlock`
- `POST /chapters/{chapterId}/unlock`
- `POST /comments/{id}/delete`
- `POST /bookmark/{id}`
- `POST /bookmark/{id}/remove`
- `POST /follow/{username}`
- `POST /follow/{username}/remove`
- `GET /follow/{username}/stats`
- `GET /notifications/fetch`
- `POST /notifications/read-all`
- `POST /notifications/{id}/read`

### Creator Area

Area ini memerlukan login sebagai creator.

- `/creator/login`
- `/creator/register`
- `/creator/dashboard`
- `/creator/content`
- `/creator/content/create`
- `/creator/content/{id}/edit`
- `/creator/content/{id}/chapters`
- `/creator/content/{id}/chapters/create`
- `/creator/content/{id}/images`
- `/creator/content/{id}/stats`
- `/creator/stats`
- `/creator/monetization`
- `/creator/monetization/history`
- `/creator/monetization/export/excel`
- `/creator/monetization/export/pdf`
- `/creator/monetization/receipt/{id}`
- `/creator/stats/works/{id}`
- `/creator/settings`

Fitur creator mencakup draft/publish/archive/delete, upload cover dan galeri, chapter editor, chapter lock, pengaturan harga CC, status ongoing/ended, statistik dashboard, statistik per karya, analitik engagement, analitik revenue/unlock, monetisasi, dan profil kreator.

### Admin Area

Area ini memerlukan login sebagai admin.

- `/alpha-admin`
- `/alpha-admin/api/users`
- `/alpha-admin/api/users/{id}`
- `/alpha-admin/api/users/{id}/status`
- `/alpha-admin/api/users/{id}/adjust-cc`
- `/alpha-admin/api/creators`
- `/alpha-admin/api/creators/{id}`
- `/alpha-admin/api/creators/{id}/status`
- `/alpha-admin/api/creators/{id}/starsoul-status`
- `/alpha-admin/api/transactions`
- `/alpha-admin/api/transactions/stats`

Panel admin menampilkan statistik user, kreator, karya, CC beredar, revenue/top up, transaksi terbaru, top creator, breakdown karya, grafik registrasi, dan grafik top up. Section admin view juga sudah disiapkan untuk user management, creator management, content works/reports/gallery, finance transactions/topup/economy, CMS pages/landing/FAQ, audit, system settings, dan notification settings.

### API v1

Base URL:

```text
/api/v1
```

Endpoint utama:

- `POST /api/v1/login`
- `POST /api/v1/register`
- `GET /api/v1/logout`
- `GET /api/v1/works`
- `GET /api/v1/works/{id}`
- `GET /api/v1/user/profile`
- `POST /api/v1/user/profile`
- `POST /api/v1/works/{id}/like`
- `POST /api/v1/works/{id}/comment`
- `POST /api/v1/works/{id}/bookmark`
- `GET /api/v1/topup/balance`
- `POST /api/v1/topup/checkout`

## Alur Fitur Penting

### Cooling Credit (CC)

- User melakukan top up dari paket CC yang tersedia.
- CC digunakan untuk unlock karya, unlock chapter, checkout cart, dan download konten berbayar yang mendukung pembelian permanen.
- Transaksi dicatat sebagai `in` atau `out` dengan kategori seperti `topup`, `unlock`, `chapter_unlock`, `purchase`, `work_purchase`, `download`, dan `withdraw`.
- Kreator menerima saldo dari unlock/download karya dan dapat melihat riwayat monetisasi.
- Admin dapat melihat statistik transaksi dan melakukan penyesuaian saldo CC user.

### Konten dan Monetisasi

- Karya dapat disimpan sebagai draft atau langsung published.
- Status publik yang tampil di explore adalah `published`, `curated`, dan `museum`.
- Karya chapter-based mencakup text, novel, light novel, dan comic.
- Karya image menggunakan galeri gambar, proxy image, watermark, blur lock, preview timer, dan download ZIP.
- Karya text/novel/light novel/comic dapat diexport sebagai PDF setelah checkout/download valid.
- Chapter dapat dikunci dengan harga CC masing-masing.
- Harga karya mendukung `price` untuk akses/preview dan `purchase_price` untuk pembelian permanen.

### Cart dan Download

- User dapat memasukkan karya downloadable ke cart.
- Checkout cart memotong saldo CC user dan mencatat transaksi keluar untuk pembeli.
- Pendapatan dari pembelian/download dicatat sebagai transaksi masuk untuk kreator.
- Setelah checkout, user diarahkan ke halaman download-all untuk mengunduh semua karya yang baru dibeli.
- Karya gambar dikemas sebagai ZIP, sedangkan karya berbasis chapter dikemas menjadi PDF menggunakan Dompdf.
- Download berbayar dicek ulang melalui riwayat transaksi agar file tidak bisa diambil tanpa checkout.

### Statistik Kreator

- Dashboard statistik kreator menampilkan total karya, karya published, follower, views, like, bookmark, comment, dan engagement.
- Statistik bulanan membandingkan performa bulan berjalan dengan bulan sebelumnya.
- Grafik pertumbuhan 30 hari mencakup like, bookmark, comment, revenue, dan unlock.
- Statistik per karya mencakup quality score, performa interaksi, komentar terbaru, revenue, riwayat unlock, top chapter, dan tren pendapatan.
- Breakdown tipe konten dan daftar karya teratas membantu kreator melihat format yang paling aktif.

### Notifikasi

Notifikasi dibuat melalui service terpusat untuk event:

- Follow kreator
- Like karya
- Komentar baru
- Purchase/download
- Unlock karya
- Unlock chapter
- Top up berhasil
- System notification

## Instalasi Lokal

Pastikan PHP, Composer, dan MySQL/MariaDB sudah tersedia. Jika memakai XAMPP, tempatkan proyek di folder `htdocs`.

1. Install dependency:

```bash
composer install
```

2. Buat atau sesuaikan file `.env`:

```bash
cp env .env
```

Jika file `.env` sudah ada, periksa konfigurasi berikut:

```dotenv
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost/nusa/'

database.default.hostname = localhost
database.default.database = nama_database
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
```

3. Buat database sesuai nilai `database.default.database`.

4. Jalankan migration:

```bash
php spark migrate
```

5. Jalankan aplikasi.

Dengan XAMPP/Apache:

```text
http://localhost/nusa/
```

Atau dengan development server CodeIgniter:

```bash
php spark serve
```

Lalu buka:

```text
http://localhost:8080
```

## Testing

Jalankan test dengan Composer:

```bash
composer test
```

Atau langsung dengan PHPUnit:

```bash
vendor/bin/phpunit
```

## Catatan Database

Migration yang tersedia mencakup fitur:

- Password reset
- Likes dan comments
- Starsoul metrics
- Paid content
- Access/status karya
- Reading history
- Locked chapters
- User profiles
- Notifications
- Cart items
- Transactions
- Purchase price karya
- Perbaikan tipe `notifications.user_id`

Ada juga script manual database di root dan folder `tmp/`. Gunakan script tersebut dengan hati-hati, terutama jika database sudah berisi data produksi.

## Penyimpanan File

- Upload runtime tersimpan di `writable/uploads`.
- Download sementara tersimpan di `writable/downloads` atau folder upload runtime sesuai proses.
- Cover publik tersimpan di `public/uploads/covers`.
- Asset publik tersimpan di `public/assets`.
- Log aplikasi tersimpan di `writable/logs`.
- Session dan debugbar tersimpan di `writable/session` dan `writable/debugbar`.
- Upload cover/arts juga dapat dikirim ke remote upload service melalui `App\Services\File\RemoteUploadService`.

Pastikan folder `writable` dapat ditulis oleh web server.

## Konvensi Keamanan

- Auto route dimatikan di `app/Config/Routes.php`.
- Area user menggunakan filter `auth:user`.
- Area creator menggunakan filter `auth:creator`.
- Area admin menggunakan filter `auth:admin`.
- API protected menggunakan filter `api_auth:user`.
- Jangan commit kredensial asli di `.env` atau service upload.
- Untuk deployment publik, arahkan document root web server ke folder `public`.
- Review kembali konfigurasi remote upload, token, dan SSL sebelum produksi.

## Status Proyek

NusaShare sudah memiliki struktur fitur yang cukup lengkap untuk platform konten kreator, termasuk web app, creator dashboard, admin panel, sistem kredit, notifikasi, monetisasi, dan API dasar. Dokumentasi teknis lanjutan yang masih disarankan:

- Skema database lengkap.
- Daftar role dan permission.
- Alur transaksi Cooling Credit secara detail.
- Contoh request/response API.
- Panduan deployment produksi.
- Panduan konfigurasi remote upload.
