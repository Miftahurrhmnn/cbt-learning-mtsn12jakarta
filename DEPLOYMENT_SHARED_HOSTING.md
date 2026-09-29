# Panduan Lengkap Deployment CBT Learning ke Shared Hosting (cPanel / DirectAdmin)

Panduan ini disusun untuk memandu Anda meng-hosting project **CBT Simpel (Laravel 12 + Breeze)** ke server **Shared Hosting** (seperti Niagahoster, Hostinger, DomaiNesia, RumahWeb, Idwebhost, dll.) dengan aman, cepat, dan terkonfigurasi secara benar.

---

## 1. Persiapan Sebelum Upload dari Komputer Lokal

Sebelum meng-compress project ke ZIP, pastikan file frontend dan dependensi sudah dibangun:

1. **Build Aset Frontend (Vite):**
   ```bash
   npm run build
   ```
   *Pastikan folder `public/build` sudah terbuat dan berisi file CSS & JS yang terkompilasi.*

2. **Compress Project Menjadi File ZIP:**
   Compress seluruh folder project `cbt-simpel` menjadi `cbt-simpel.zip`.
   > **TIPS PENTING:** Anda dapat mengecualikan folder `node_modules` agar ukuran file ZIP jauh lebih kecil (hanya berkisar 20-30 MB). Folder `vendor` sebaiknya tetap diikutkan jika server shared hosting Anda tidak memiliki akses SSH/Composer.

---

## 2. Upload ke File Manager cPanel

Terdapat **2 Metode Deployment** di Shared Hosting:

### Metode 1: Menggunakan Root `.htaccess` (Paling Mudah & Direkomendasikan)
Project ini telah dilengkapi file `.htaccess` di root directory yang secara otomatis mengarahkan pengunjung ke folder `/public/` sekaligus memproteksi file sensitif seperti `.env`.

1. Login ke **cPanel** akun hosting Anda.
2. Buka **File Manager** &rarr; masuk ke folder `public_html` (atau subdomain Anda).
3. Upload file `cbt-simpel.zip` ke dalam `public_html`.
4. Klik kanan file ZIP &rarr; pilih **Extract**.
5. Pastikan seluruh isi project berada di dalam `public_html/` (termasuk file `.htaccess` di root).
6. File `.env` dan folder sistem secara otomatis terlindungi dari akses publik oleh rule keamanan `.htaccess`.

---

### Metode 2: Memisahkan Folder Inti di Luar `public_html` (Standar Industri)
Jika Anda ingin isolasi 100% fisik:
1. Ekstrak project ke folder di luar `public_html`, misalnya `/home/username/cbt-core/`.
2. Pindahkan seluruh isi folder `cbt-core/public/` ke dalam `public_html/`.
3. Buka file `public_html/index.php`, sesuaikan path bootstrap:
   ```php
   require __DIR__.'/../cbt-core/vendor/autoload.php';
   $app = require_once __DIR__.'/../cbt-core/bootstrap/app.php';
   ```

---

## 3. Konfigurasi Database MySQL di cPanel

1. Di cPanel, buka menu **MySQL Databases** (atau **MySQL Database Wizard**).
2. Buat database baru, contoh: `u1234567_cbt`.
3. Buat user database baru, contoh: `u1234567_cbtuser` beserta password yang kuat.
4. Hubungkan User ke Database dengan mencentang **ALL PRIVILEGES** (Semua Hak Akses).
5. Buat file `.env` di folder project (bisa menyalin dari template `.env.production.example`):
   ```env
   APP_NAME="CBT Simpel"
   APP_ENV=production
   APP_KEY=base64:f9jzW9x9rFdpE4tbd2J6b7SHPQLdlOwxkHfzOp46bew=
   APP_DEBUG=false
   APP_URL=https://domain-anda.com

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=u1234567_cbt
   DB_USERNAME=u1234567_cbtuser
   DB_PASSWORD=PasswordDatabaseAnda

   SESSION_DRIVER=database
   CACHE_STORE=file
   FILESYSTEM_DISK=public
   ```

---

## 4. Menjalankan Migrasi & Seeder Database

### Cara A: Melalui SSH Terminal cPanel (Jika Tersedia)
Masuk ke Terminal cPanel dan jalankan:
```bash
php artisan migrate --force
php artisan db:seed --force
```

### Cara B: Melalui phpMyAdmin (Jika Tidak Ada Terminal SSH)
1. Di komputer lokal Anda, buka HeidiSQL / phpMyAdmin Laragon atau jalankan mysqldump.
2. Export database `cbt_simpel` dari Laragon menjadi file `.sql`.
3. Buka **phpMyAdmin** di cPanel hosting &rarr; pilih database `u1234567_cbt` &rarr; klik tab **Import** &rarr; unggah file `.sql` tersebut.

---

## 5. Menghubungkan Storage (Symlink Gambar Soal)

Agar gambar soal yang diupload guru dapat dibuka oleh siswa di shared hosting:

### Jika ada Terminal SSH:
```bash
php artisan storage:link
```

### Jika TIDAK ada Terminal SSH:
Buat file baru di dalam folder `public/storage_link.php`:
```php
<?php
$target = __DIR__ . '/../storage/app/public';
$link = __DIR__ . '/storage';
if (symlink($target, $link)) {
    echo "Symlink storage berhasil dibuat!";
} else {
    echo "Gagal membuat symlink.";
}
```
Buka URL `https://domain-anda.com/storage_link.php` di browser satu kali. Setelah muncul pesan berhasil, **segera hapus** file `storage_link.php` tersebut demi keamanan.

---

## 6. Optimasi Performa & Cache di Server

Untuk mempercepat respons sistem saat puluhan/ratusan siswa mengerjakan ujian secara bersamaan, jalankan perintah optimasi (via Terminal SSH):
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Jika tidak ada SSH, fitur caching ini bersifat opsional karena project sudah dilengkapi kompresi GZIP dan browser cache di file `.htaccess`.

---

## 7. Verifikasi Keamanan Pasca Deployment
1. Buka browser dan coba akses langsung file: `https://domain-anda.com/.env`
   - **Hasil yang benar:** Harus muncul pesan **403 Forbidden** (ditolak).
2. Buka `https://domain-anda.com` dan pastikan login Guru dan Siswa berjalan normal.
3. Pastikan SSL/HTTPS telah aktif (aktifkan *Force HTTPS Redirection* di cPanel).
