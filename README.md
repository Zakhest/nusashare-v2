# NusaShare

NusaShare adalah aplikasi web berbagi karya untuk kreator dan pembaca. Platform ini menyediakan ruang publik untuk menemukan karya, area pengguna untuk membaca dan mendukung kreator, serta dashboard kreator untuk mengelola konten dan monetisasi.

Proyek ini dibangun dengan CodeIgniter 4 dan berjalan di atas PHP 8.1 atau lebih baru.

## Fitur Utama

- Landing page dan halaman legal.
- Registrasi, login, lupa password, dan reset password untuk pengguna.
- Area eksplorasi karya dengan halaman detail, baca, dan download.
- Dashboard pengguna untuk profil, bookmark, follow creator, cart, notifikasi, dan riwayat interaksi.
- Sistem Cooling Credit (CC) untuk top up, membuka karya premium, dan mendukung kreator.
- Area kreator untuk login/register, dashboard, manajemen konten, chapter, galeri gambar, publish/archive, statistik, monetisasi, dan pengaturan profil.
- Interaksi karya: like, comment, bookmark, unlock karya, dan unlock chapter.
- API v1 untuk integrasi mobile atau client eksternal.
- Proxy gambar untuk cover, galeri, profil, dan gambar chapter.

## Teknologi

- PHP 8.1+
- CodeIgniter 4
- MySQL atau MariaDB
- Composer
- Dompdf
- PHPUnit
- CSS/JavaScript native, Tailwind CDN pada beberapa halaman

## Struktur Folder

```text
app/
  Config/          Konfigurasi aplikasi, route, filter, database, session
  Controllers/     Controller web, creator area, dan API
  Database/        Migration dan seeder
  Filters/         Auth filter untuk web dan API
  Models/          Model database
  Views/           Tampilan halaman publik, user, creator, auth, works
assets/            Asset CSS, JavaScript, dan icon aplikasi
public/            Public document root dan upload publik
tests/             Test PHPUnit
tmp/               Script sementara untuk perubahan database
vendor/            Dependency Composer
writable/          Log, session, upload, cache, debugbar
```

## Modul Aplikasi

### Public Area

- `/` landing page
- `/explore` eksplorasi karya
- `/search` pencarian karya
- `/works/{id}` detail karya
- `/works/{id}/read/{chapterId}` baca chapter
- `/works/{id}/download` download karya
- `/user/{username}` profil publik pengguna atau kreator
- `/terms` dan `/privacy`

### User Area

Area ini memerlukan login sebagai user.

- `/dashboard`
- `/me/profile`
- `/me/bookmarks`
- `/me/cart`
- `/me/follows`
- `/topup`
- Like, comment, bookmark, follow, cart checkout, unlock karya, unlock chapter, dan notifikasi.

### Creator Area

Area ini memerlukan login sebagai creator.

- `/creator/login`
- `/creator/register`
- `/creator/dashboard`
- `/creator/content`
- `/creator/content/create`
- `/creator/content/{id}/chapters`
- `/creator/content/{id}/images`
- `/creator/stats`
- `/creator/monetization`
- `/creator/settings`

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

Jika file `.env` sudah ada, cukup periksa konfigurasi berikut:

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

Ada juga script manual database di root dan folder `tmp/`. Gunakan script tersebut dengan hati-hati, terutama jika database sudah berisi data produksi.

## Penyimpanan File

- Upload runtime tersimpan di `writable/uploads`.
- Cover publik tersimpan di `public/uploads/covers`.
- Log aplikasi tersimpan di `writable/logs`.
- Session dan debugbar tersimpan di `writable/session` dan `writable/debugbar`.

Pastikan folder `writable` dapat ditulis oleh web server.

## Konvensi Keamanan

- Auto route dimatikan di `app/Config/Routes.php`.
- Area user menggunakan filter `auth:user`.
- Area creator menggunakan filter `auth:creator`.
- API protected menggunakan filter `api_auth:user`.
- Jangan commit kredensial asli di `.env`.
- Untuk deployment publik, arahkan document root web server ke folder `public`.

## Status Proyek

Proyek ini sudah memiliki struktur fitur yang cukup lengkap untuk platform konten kreator, termasuk web app, creator dashboard, sistem kredit, dan API dasar. Dokumentasi teknis tambahan yang masih disarankan:

- Skema database lengkap.
- Daftar role dan permission.
- Alur transaksi Cooling Credit.
- Contoh request/response API.
- Panduan deployment produksi.

Ini masih PoC
# nusashare-v2
