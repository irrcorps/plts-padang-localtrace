# Panduan Instalasi
## LokalTrust

Prototipe penelitian PDP 2026 — *Perancangan Sistem Traceability dan Sertifikasi Produk Ritel Lokal Berbasis Blockchain dan Smart Contract untuk Penguatan Hilirisasi di Kota Padang*.

> Untuk deploy online ke Render.com + database MySQL online, lihat **[DEPLOYMENT.md](DEPLOYMENT.md)**. Dokumen ini fokus ke instalasi **lokal** (localhost / shared hosting cPanel).

---

## 1. Persyaratan Sistem

| Komponen | Minimal | Catatan |
|---|---|---|
| PHP | 8.0+ (kompatibel juga di 7.4+) | Ekstensi wajib: `pdo_mysql`, `fileinfo`, `session` |
| MySQL / MariaDB | 5.7+ / 10.3+ | Untuk `ENUM`, `FOREIGN KEY`, `TIMESTAMP ON UPDATE`, `VIEW` |
| Web Server | Apache (disarankan) | Mendukung `.htaccess` (`mod_rewrite`/`mod_authz_core`) |
| Browser | Chrome/Edge/Firefox versi terbaru | Untuk render QR Code (JS) dan Chart.js |
| Koneksi Internet | Diperlukan saat sistem dijalankan | CDN untuk Bootstrap 5, Bootstrap Icons, Google Fonts, Chart.js, qrcode.js |

Tidak diperlukan Composer, Node.js, atau proses build apa pun — seluruh sistem adalah PHP native + MySQL murni. (Docker hanya dipakai untuk jalur deploy online, lihat DEPLOYMENT.md.)

---

## 2. Struktur Folder

```
LokalTrust/
├── config/database.php       # koneksi database (baca environment variable, fallback lokal)
├── core/                     # Database.php, Auth.php, helpers.php, bootstrap.php
├── models/                   # User, Product, TraceabilityLog, Certificate, BlockchainBlock
├── controllers/               # Auth, Product, Traceability, Certification, Verify
├── views/                     # landing, auth, dashboard, products, verify (+ partials)
├── assets/                    # css/js
├── uploads/products/          # foto produk hasil upload
├── database.sql               # skema + data contoh (database: lokaltrust_db)
├── Dockerfile, docker-entrypoint.sh, render.yaml   # deployment online (lihat DEPLOYMENT.md)
└── index.php, login.php, register.php, dashboard.php, products.php,
    traceability.php, certification.php, verify.php   # entry point/front controller
```

`config/`, `core/`, `models/`, `controllers/`, `views/` masing-masing memiliki `.htaccess` (`Require all denied`) sehingga tidak bisa diakses langsung lewat browser — hanya bisa dipanggil lewat file entry point di root.

---

## 3. Instalasi di Localhost (XAMPP / Laragon)

### Langkah 1 — Salin proyek
Salin seluruh folder proyek ke direktori web server, misalnya:
```
C:\xampp\htdocs\lokaltrust
```

### Langkah 2 — Buat & Import Database
Buka **phpMyAdmin** atau terminal MySQL, lalu jalankan:

```bash
mysql -u root < database.sql
```

File `database.sql` akan otomatis membuat database **`lokaltrust_db`** berisi:
- Seluruh `CREATE TABLE` (users, products, traceability_logs, certificates, blockchain_blocks) + `VIEW transactions`
- Data contoh: 7 akun demo lintas role (1 admin, 3 producer, 1 distributor, 2 retailer), 3 produk lokal Padang (Rendang Minang Premium, Keripik Balado Padang, Kopi Lokal Minang) beserta foto, riwayat traceability, dan 2 sertifikat aktif

### Langkah 3 — Konfigurasi Koneksi Database
[config/database.php](config/database.php) membaca kredensial dari **environment variable**, dengan fallback default untuk localhost — biasanya **tidak perlu diedit** untuk instalasi lokal standar (XAMPP: user `root`, tanpa password). Jika kredensial MySQL Anda berbeda, override lewat environment variable (atau edit langsung nilai fallback di file tersebut untuk dev lokal):

| Environment Variable | Default lokal |
|---|---|
| `DB_HOST` | `localhost` |
| `DB_PORT` | `3306` |
| `DB_NAME` | `lokaltrust_db` |
| `DB_USER` | `root` |
| `DB_PASSWORD` | *(kosong)* |
| `BASE_URL` | *(kosong — isi `/lokaltrust` jika project ada di subfolder)* |

> **Penting:** Jika project diakses lewat subfolder (misal `http://localhost/lokaltrust`), set environment variable `BASE_URL` menjadi `/lokaltrust` agar seluruh link, CSS, dan gambar termuat dengan benar.

### Langkah 4 — Jalankan
- **Via XAMPP/Apache:** pastikan Apache & MySQL aktif di XAMPP Control Panel, lalu buka `http://localhost/lokaltrust/index.php`
- **Via PHP built-in server** (tanpa Apache):
```bash
php -S localhost:8000
```
lalu buka `http://localhost:8000/index.php`

### Langkah 5 — Login dengan Akun Demo

| Role | Email | Password |
|---|---|---|
| Admin | admin@lokaltrust.com | password123 |
| Producer / UMKM | producer@lokaltrust.com | password123 |
| Distributor | distributor@lokaltrust.com | password123 |
| Retailer | retailer@lokaltrust.com | password123 |

Akun demo tambahan (producer2/producer3/retailer2) ada di [PANDUAN_PENGGUNAAN.md](PANDUAN_PENGGUNAAN.md).

---

## 4. Instalasi di Hosting (cPanel / Shared Hosting)

1. **Upload file**: unggah seluruh isi folder proyek ke `public_html` (atau subfolder domain) via File Manager / FTP.
2. **Buat database MySQL** melalui menu *MySQL Databases* di cPanel, catat nama database, user, dan password yang dibuat.
3. **Import `database.sql`** lewat phpMyAdmin di cPanel (tab Import). Jika nama database di cPanel berbeda dari `lokaltrust_db` (biasanya diberi prefix akun, mis. `namauser_lokaltrust`), hapus/lewati baris `CREATE DATABASE` & `USE` di awal file sebelum import, atau import langsung lewat phpMyAdmin yang sudah otomatis mengarah ke database Anda.
4. **Set environment variable** kredensial database hosting (lewat panel "Setup Node.js/PHP App" jika tersedia) — atau, jika hosting Anda tidak mendukung environment variable, edit langsung nilai fallback di `config/database.php`:
   ```php
   define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
   define('DB_NAME', getenv('DB_NAME') ?: 'namauser_lokaltrust');
   define('DB_USER', getenv('DB_USER') ?: 'namauser_lokaltrust');
   define('DB_PASS', getenv('DB_PASSWORD') ?: 'password_dari_cpanel');
   ```
5. **Set permission folder upload** agar dapat ditulis oleh web server:
   ```bash
   chmod 755 uploads/products
   ```
6. **Pastikan PHP versi 8.x** diaktifkan lewat *Select PHP Version* di cPanel, dan ekstensi `pdo_mysql`, `fileinfo` sudah aktif (biasanya aktif secara default).
7. Akses domain Anda — sistem otomatis berjalan tanpa perlu build/compile tambahan.

---

## 5. Troubleshooting

| Masalah | Penyebab Umum | Solusi |
|---|---|---|
| `Koneksi database gagal` / "Layanan sedang tidak tersedia" | Kredensial salah, atau MySQL belum aktif | Cek ulang `DB_HOST/DB_PORT/DB_NAME/DB_USER/DB_PASSWORD`, pastikan service MySQL berjalan |
| Halaman blank / error 500 | Ekstensi PHP `pdo_mysql`/`fileinfo` belum aktif | Aktifkan lewat `php.ini` atau *Select PHP Extensions* di hosting |
| CSS/gambar tidak muncul di subfolder | `BASE_URL` belum disesuaikan | Set environment variable `BASE_URL` sesuai path subfolder |
| Upload foto produk gagal | Folder `uploads/products/` tidak writable | `chmod 755 uploads/products` (Linux/hosting) |
| QR Code / grafik dashboard tidak tampil | Tidak ada koneksi internet (CDN diblokir) | Sistem butuh internet untuk memuat Bootstrap, Chart.js, qrcode.js dari CDN |
| Error `404` pada `/login`, `/verify/...` | `mod_rewrite`/`AllowOverride` tidak aktif di Apache | Aktifkan `AllowOverride All` pada konfigurasi virtual host; URL asli `login.php` dsb tetap selalu berfungsi tanpa mod_rewrite |

---

## 6. Catatan Keamanan Prototipe

- Password disimpan dengan `password_hash()` (bcrypt) — bukan plaintext.
- Query database seluruhnya menggunakan **PDO Prepared Statement** (anti SQL Injection).
- Output ke halaman di-*escape* dengan `htmlspecialchars()` (anti XSS).
- Folder internal (`config`, `core`, `models`, `controllers`, `views`) diblokir dari akses langsung via `.htaccess`.
- Upload foto divalidasi berdasarkan **MIME type asli** (`finfo`), bukan hanya ekstensi file.
- Kredensial database **tidak di-hardcode** — dibaca dari environment variable (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`), dengan pesan error database yang disamarkan otomatis saat `APP_ENV=production`.
- Session cookie diberi flag `Secure`+`HttpOnly`+`SameSite=Lax` otomatis saat `APP_ENV=production` (HTTPS).

Sistem ini adalah **prototipe riset (TKT 3)** untuk membuktikan konsep — bukan sistem produksi/komersial. Untuk penggunaan produksi, disarankan menambah CSRF token, rate limiting, dan audit keamanan lanjutan.
