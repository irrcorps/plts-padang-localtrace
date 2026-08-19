# Panduan Instalasi
## Padang LocalTrace System (PLTS)

Prototipe penelitian PDP 2026 — *Perancangan Sistem Traceability dan Sertifikasi Produk Ritel Lokal Berbasis Blockchain dan Smart Contract untuk Penguatan Hilirisasi di Kota Padang*.

---

## 1. Persyaratan Sistem

| Komponen | Minimal | Catatan |
|---|---|---|
| PHP | 8.0+ (kompatibel juga di 7.4+) | Ekstensi wajib: `pdo_mysql`, `gd`, `fileinfo`, `session` |
| MySQL / MariaDB | 5.7+ / 10.3+ | Untuk `ENUM`, `FOREIGN KEY`, `TIMESTAMP ON UPDATE` |
| Web Server | Apache (disarankan) | Mendukung `.htaccess` (`mod_rewrite`/`mod_authz_core`) |
| Browser | Chrome/Edge/Firefox versi terbaru | Untuk render QR Code (JS) dan Chart.js |
| Koneksi Internet | Diperlukan saat sistem dijalankan | CDN untuk Bootstrap 5, Bootstrap Icons, Google Fonts, Chart.js, qrcode.js |

Tidak diperlukan Composer, Node.js, atau proses build apa pun — seluruh sistem adalah PHP native + MySQL murni.

---

## 2. Struktur Folder

```
PLB-Padang/
├── config/database.php       # kredensial koneksi database (PDO)
├── core/                     # Database.php, Auth.php, helpers.php, bootstrap.php
├── models/                   # User, Product, TraceabilityLog, Certificate, BlockchainBlock
├── controllers/               # Auth, Product, Traceability, Certification, Verify
├── views/                     # landing, auth, dashboard, products, verify (+ partials)
├── assets/                    # css/js
├── uploads/products/          # foto produk hasil upload
├── database.sql               # skema + data contoh
└── index.php, login.php, register.php, dashboard.php, products.php,
    traceability.php, certification.php, verify.php   # entry point/front controller
```

`config/`, `core/`, `models/`, `controllers/`, `views/` masing-masing memiliki `.htaccess` (`Require all denied`) sehingga tidak bisa diakses langsung lewat browser — hanya bisa dipanggil lewat file entry point di root.

---

## 3. Instalasi di Localhost (XAMPP / Laragon)

### Langkah 1 — Salin proyek
Salin seluruh folder `PLB-Padang` ke direktori web server, misalnya:
```
C:\xampp\htdocs\plts
```

### Langkah 2 — Buat & Import Database
Buka **phpMyAdmin** atau terminal MySQL, lalu jalankan:

```bash
mysql -u root < database.sql
```

Atau melalui phpMyAdmin: buat database baru dengan nama `plts_db`, lalu import file `database.sql` melalui tab **Import**.

File `database.sql` sudah berisi:
- Seluruh `CREATE TABLE` (users, products, traceability_logs, certificates, blockchain_blocks)
- Data contoh: 4 akun demo lintas role, 3 produk lokal Padang (Rendang Kemasan, Keripik Balado, Kopi Minang) beserta foto, riwayat traceability, dan 1 sertifikat aktif

### Langkah 3 — Konfigurasi Koneksi Database
Buka [config/database.php](config/database.php) dan sesuaikan bila perlu:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'plts_db');
define('DB_USER', 'root');
define('DB_PASS', '');        // isi sesuai password MySQL Anda
define('BASE_URL', '');       // isi '/plts' jika project ada di subfolder, kosongkan jika di root domain
```

> **Penting:** Jika project diakses lewat subfolder (misal `http://localhost/plts`), set `BASE_URL` menjadi `/plts` agar seluruh link, CSS, dan gambar termuat dengan benar.

### Langkah 4 — Jalankan
- **Via XAMPP/Apache:** pastikan Apache & MySQL aktif di XAMPP Control Panel, lalu buka `http://localhost/plts/index.php`
- **Via PHP built-in server** (tanpa Apache):
```bash
php -S localhost:8000
```
lalu buka `http://localhost:8000/index.php`

### Langkah 5 — Login dengan Akun Demo

| Role | Email | Password |
|---|---|---|
| Admin | admin@plts.test | password123 |
| Producer / UMKM | producer@plts.test | password123 |
| Distributor | distributor@plts.test | password123 |
| Retailer | retailer@plts.test | password123 |

---

## 4. Instalasi di Hosting (cPanel / Shared Hosting)

1. **Upload file**: unggah seluruh isi folder proyek ke `public_html` (atau subfolder domain) via File Manager / FTP.
2. **Buat database MySQL** melalui menu *MySQL Databases* di cPanel, catat nama database, user, dan password yang dibuat.
3. **Import `database.sql`** lewat phpMyAdmin di cPanel (tab Import).
4. **Edit `config/database.php`** dengan kredensial database hosting:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'namauser_plts');
   define('DB_USER', 'namauser_plts');
   define('DB_PASS', 'password_dari_cpanel');
   define('BASE_URL', '');
   ```
5. **Set permission folder upload** agar dapat ditulis oleh web server:
   ```bash
   chmod 755 uploads/products
   ```
6. **Pastikan PHP versi 8.x** diaktifkan lewat *Select PHP Version* di cPanel, dan ekstensi `pdo_mysql`, `gd`, `fileinfo` sudah aktif (biasanya aktif secara default).
7. Akses domain Anda — sistem otomatis berjalan tanpa perlu build/compile tambahan.

---

## 5. Troubleshooting

| Masalah | Penyebab Umum | Solusi |
|---|---|---|
| `Koneksi database gagal` | Kredensial di `config/database.php` salah, atau MySQL belum aktif | Cek ulang `DB_HOST/DB_NAME/DB_USER/DB_PASS`, pastikan service MySQL berjalan |
| Halaman blank / error 500 | Ekstensi PHP `pdo_mysql`/`gd`/`fileinfo` belum aktif | Aktifkan lewat `php.ini` atau *Select PHP Extensions* di hosting |
| CSS/gambar tidak muncul di subfolder | `BASE_URL` belum disesuaikan | Set `BASE_URL` sesuai path subfolder di `config/database.php` |
| Upload foto produk gagal | Folder `uploads/products/` tidak writable | `chmod 755 uploads/products` (Linux/hosting) |
| QR Code / grafik dashboard tidak tampil | Tidak ada koneksi internet (CDN diblokir) | Sistem butuh internet untuk memuat Bootstrap, Chart.js, qrcode.js dari CDN |
| Error `404` file `.htaccess` diabaikan | `mod_rewrite`/`AllowOverride` tidak aktif di Apache | Aktifkan `AllowOverride All` pada konfigurasi virtual host |

---

## 6. Catatan Keamanan Prototipe

- Password disimpan dengan `password_hash()` (bcrypt) — bukan plaintext.
- Query database seluruhnya menggunakan **PDO Prepared Statement** (anti SQL Injection).
- Output ke halaman di-*escape* dengan `htmlspecialchars()` (anti XSS).
- Folder internal (`config`, `core`, `models`, `controllers`, `views`) diblokir dari akses langsung via `.htaccess`.
- Upload foto divalidasi berdasarkan **MIME type asli** (`finfo`), bukan hanya ekstensi file.

Sistem ini adalah **prototipe riset (TKT 3)** untuk membuktikan konsep — bukan sistem produksi/komersial. Untuk penggunaan produksi, disarankan menambah CSRF token, rate limiting, dan audit keamanan lanjutan.
