# NusaShare

NusaShare adalah aplikasi web berbagi karya untuk kreator dan pembaca. Platform ini menyediakan ruang publik untuk menemukan karya, area pengguna untuk membaca dan mendukung kreator, dashboard kreator untuk mengelola konten dan monetisasi, serta panel admin untuk memantau operasional platform.

Proyek ini dibangun dengan CodeIgniter 4 dan berjalan di atas PHP 8.1 atau lebih baru. Status proyek saat ini masih PoC/produk pengembangan aktif.

## Ringkasan Fitur Terbaru

- Landing page, halaman legal, login/register user, login/register kreator, forgot password, dan reset password.
- Explore karya dengan spotlight, filter genre/tipe, mode gallery, mode story, kategori artikel, pencarian karya, dan pencarian user/kreator.
- Format konten: text, novel, light novel, comic, image, PDF, dan artikel pada skema konten.
- Artikel publik dengan URL slug, parser body artikel, infobox, tabel, gambar inline, live preview editor, like, bookmark, komentar, share, dan view counter.
- Detail karya, reader chapter, navigasi chapter, komentar per karya/chapter, hapus komentar sendiri, like, bookmark, follow/unfollow kreator, statistik follow, dan profil publik.
- View counter dengan threshold baca 2 menit agar statistik lebih bermakna.
- Reading history dan resume terakhir dibaca untuk pengguna login.
- Sistem Cooling Credit (CC) untuk top up, unlock karya, unlock chapter, checkout cart, download karya, dan pendapatan kreator.
- Konten berbayar mendukung akses full, akses per chapter, harga preview, harga beli permanen, timer preview, watermark gambar, blur/lock state, dan chapter terkunci.
- Kreator dapat mengaktifkan/nonaktifkan izin unduhan per karya melalui opsi `allow_downloads`; artikel otomatis tidak masuk alur download/cart.
- Cart karya downloadable dengan checkout CC, halaman auto-download setelah checkout, download ZIP untuk karya gambar, dan export PDF untuk karya teks/novel/light novel/comic.
- File unduhan diberi proteksi berbasis akun pembeli: ZIP memakai password dan PDF memakai pembatasan/enkripsi bila dukungan library tersedia.
- Dashboard pengguna untuk profil, bookmark/koleksi, cart, follows, top up, dan notifikasi realtime/AJAX.
- Dashboard kreator untuk statistik ringkas, manajemen karya, chapter, galeri gambar, publish/archive/delete, statistik karya, analitik per karya, monetisasi, penarikan dummy, riwayat transaksi, export Excel/PDF, dan receipt transaksi.
- Panel admin `alpha-admin` untuk dashboard statistik, user management, creator management, status Starsoul, penyesuaian saldo CC, transaksi, top up, ekonomi, konten, laporan, gallery, CMS landing/page/FAQ, audit, sistem, dan notifikasi.
- API v1 untuk login/register/logout, daftar karya, detail karya, profil user, like/comment/bookmark, saldo top up, dan checkout top up.
- Proxy gambar untuk cover, galeri, profil, gambar chapter, remote image, blur/lock state, dan watermark.
- Notification service terpusat untuk follow, like, comment, purchase, unlock, unlock chapter, top up, dan notifikasi sistem.
- Progressive Web App (PWA): manifest aplikasi, icon multi-ukuran, install prompt, service worker, cache statis/dinamis, fallback `/offline`, indikator online/offline, dan background sync notifikasi.

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
- Service Worker, Web App Manifest, Cache API, Background Sync, dan IndexedDB untuk fitur PWA

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
public/            Public document root, manifest, service worker, dan asset publik
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
- `/artikel/{slug}` halaman artikel publik
- `/content/{segment}` alias halaman konten
- `/user/{username}` profil publik pengguna/kreator
- `/creator/{username}` profil publik kreator
- `/offline` halaman fallback saat PWA tidak memiliki koneksi
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
- `POST /creator/artikel/upload-image`
- `/creator/stats`
- `/creator/monetization`
- `/creator/monetization/history`
- `/creator/monetization/export/excel`
- `/creator/monetization/export/pdf`
- `/creator/monetization/receipt/{id}`
- `/creator/stats/works/{id}`
- `/creator/settings`

Fitur creator mencakup draft/publish/archive/delete, upload cover dan galeri, chapter editor, chapter lock, editor artikel dengan slug, body, infobox, tabel, gambar inline, live preview, pengaturan harga CC, kontrol izin unduhan per karya, status ongoing/ended, statistik dashboard, statistik per karya, analitik engagement, analitik revenue/unlock, monetisasi, dan profil kreator.

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
- Karya artikel menggunakan body tunggal dengan slug publik, parser infobox/tabel/gambar, halaman `/artikel/{slug}`, dan interaksi like/bookmark/comment.
- Karya text/novel/light novel/comic dapat diexport sebagai PDF setelah checkout/download valid.
- Chapter dapat dikunci dengan harga CC masing-masing.
- Harga karya mendukung `price` untuk akses/preview dan `purchase_price` untuk pembelian permanen.
- Izin unduhan dikontrol oleh `allow_downloads`; jika dimatikan, karya tidak bisa ditambahkan ke cart, checkout, halaman download-all, maupun endpoint unduhan langsung.

### Cart dan Download

- User dapat memasukkan karya downloadable ke cart.
- Karya hanya bisa masuk cart jika tipe kontennya mendukung download dan kreator mengaktifkan `allow_downloads`.
- Checkout cart memotong saldo CC user dan mencatat transaksi keluar untuk pembeli.
- Pendapatan dari pembelian/download dicatat sebagai transaksi masuk untuk kreator.
- Setelah checkout, user diarahkan ke halaman download-all untuk mengunduh semua karya yang baru dibeli.
- Karya gambar dikemas sebagai ZIP berpassword, sedangkan karya berbasis chapter dikemas menjadi PDF menggunakan Dompdf dengan metadata kepemilikan pembeli.
- Download berbayar dicek ulang melalui riwayat transaksi agar file tidak bisa diambil tanpa checkout.

### PWA dan Offline

- `manifest.json` menyediakan nama aplikasi, icon, warna tema, mode standalone, shortcut ke explore/dashboard, dan metadata install.
- `sw.js` melakukan precache halaman/aset penting, cache-first untuk asset statis, stale-while-revalidate untuk gambar proxy, dan network-first untuk halaman HTML.
- Route `/offline` menjadi fallback ketika halaman belum tersedia di cache dan koneksi terputus.
- `assets/js/pwa.js` mendaftarkan service worker, menampilkan prompt install, toast update aplikasi, dan indikator koneksi online/offline.
- Background Sync digunakan untuk mengambil notifikasi terbaru dan menyimpannya ke IndexedDB sebelum dibroadcast ke `notifications.js`.
- Endpoint API, notifikasi, cart, top up, dan logout sengaja dibuat network-only agar data transaksi tetap segar.

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

## Cara/Panduan Menjalankan Website

Panduan cepat untuk menjalankan NusaShare di lokal:

1. Nyalakan Apache dan MySQL/MariaDB dari XAMPP.
2. Pastikan folder proyek berada di `C:\xampp\htdocs\nusa`.
3. Buat database sesuai konfigurasi `.env`, misalnya `nusashare`.
4. Jalankan dependency dan migration:

```bash
composer install
php spark migrate
```

5. Buka website melalui browser:

```text
http://localhost/nusa/
```

Jika memakai development server CodeIgniter, jalankan:

```bash
php spark serve
```

Lalu buka:

```text
http://localhost:8080
```

Route penting untuk demo:

- Public/explore: `http://localhost/nusa/` dan `http://localhost/nusa/explore`
- Login user: `http://localhost/nusa/login`
- Register user: `http://localhost/nusa/register`
- Login kreator: `http://localhost/nusa/creator/login`
- Register kreator: `http://localhost/nusa/creator/register`
- Dashboard admin: `http://localhost/nusa/alpha-admin`
- Top up CC user: `http://localhost/nusa/topup`
- Cart user: `http://localhost/nusa/me/cart`

## Informasi Akun Demo

Jika fitur login diperlukan oleh juri, siapkan akun demo berikut di database lokal sebelum presentasi. Project ini belum menyertakan seeder akun demo default, jadi kredensial di bawah dapat disesuaikan dengan data yang dibuat saat demo.

| Role | URL Login | Email/Username | Password | Catatan |
| --- | --- | --- | --- | --- |
| User/Pembaca | `/login` | `demo_user` | `password-demo` | Untuk mencoba explore, like, bookmark, komentar, top up CC, cart, checkout, dan download. |
| Kreator | `/creator/login` | `demo_creator` | `password-demo` | Untuk mencoba dashboard kreator, upload karya, chapter, galeri, statistik, dan monetisasi. |
| Admin | `/alpha-admin` | `demo_admin` | `password-demo` | Untuk mencoba panel admin, user management, creator management, transaksi, top up, CMS, dan sistem. |

Catatan untuk juri:

- Akun demo sebaiknya dibuat dengan saldo CC yang cukup agar alur transaksi bisa dicoba tanpa setup tambahan.
- Untuk demo unduhan berbayar, pastikan minimal ada satu karya `is_paid` dengan `purchase_price` atau `price` lebih dari 0.
- Untuk demo kreator, pastikan akun kreator sudah memiliki minimal satu karya published dan beberapa chapter/gallery agar statistik lebih terlihat.

## Alur Transaksi & Validasi Unduhan

Alur pembelian/download menggunakan Cooling Credit (CC):

1. User login sebagai pembaca.
2. User melakukan top up di `/topup`.
3. Sistem menambahkan saldo CC sesuai paket dan mencatat transaksi `in` kategori `topup`.
4. User membuka detail karya dan menambahkan karya downloadable ke cart.
5. Saat checkout cart, sistem menghitung total harga karya berbayar dari `purchase_price` atau fallback ke `price`.
6. Jika saldo CC tidak cukup, user diarahkan ke halaman top up.
7. Jika saldo cukup, sistem menjalankan transaksi database:
   - saldo CC pembeli dikurangi;
   - saldo CC kreator ditambah;
   - transaksi pembeli dicatat sebagai `out` kategori `download`;
   - transaksi kreator dicatat sebagai `in` kategori `download`;
   - notifikasi pembelian dikirim ke kreator.
8. Cart dikosongkan dan daftar karya yang baru dibeli disimpan sementara di session `pending_downloads`.
9. User diarahkan ke `/cart/download-all` untuk melihat daftar file siap unduh.
10. Saat user membuka `/cart/download/{id}`, sistem memvalidasi akses:
    - karya harus ada;
    - tipe karya harus termasuk `image`, `text`, `novel`, `light_novel`, atau `comic`;
    - jika karya berbayar, harus ada riwayat transaksi pembeli dengan `user_id`, `reference_id` karya, kategori `download`, dan tipe `out`.
11. Jika validasi gagal, sistem menolak unduhan dengan status 403.
12. Jika validasi berhasil:
    - karya gambar diunduh sebagai ZIP;
    - karya berbasis chapter diunduh sebagai PDF menggunakan Dompdf.

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
- Allow downloads karya (`allow_downloads`)
- Perbaikan tipe `notifications.user_id`

Ada juga script manual database di root dan folder `tmp/`. Gunakan script tersebut dengan hati-hati, terutama jika database sudah berisi data produksi.

## Penyimpanan File

- Upload runtime tersimpan di `writable/uploads`.
- Download sementara tersimpan di `writable/downloads` atau folder upload runtime sesuai proses.
- Cover publik tersimpan di `public/uploads/covers`.
- Asset publik tersimpan di `public/assets`.
- Asset PWA tersimpan di `assets/pwa` dan `public/assets/pwa`, sedangkan manifest/service worker tersedia di root publik (`manifest.json`, `sw.js`, `public/manifest.json`, `public/sw.js`).
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
