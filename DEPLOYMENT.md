# Panduan Deployment
## LokalTrust → Render.com (Docker) + MySQL Online (Clever Cloud)

Dokumen ini menjelaskan cara membawa prototipe LokalTrust ke `https://lokaltrust.onrender.com` menggunakan layanan gratis.

## Kenapa Docker?

Render.com tidak menyediakan runtime PHP native (hanya Node/Python/Ruby/Go/Rust/Elixir + Docker), dan database managed-nya hanya PostgreSQL — **tidak ada MySQL**. Solusinya:

- Aplikasi PHP dijalankan sebagai **Web Service berbasis Docker** (`Dockerfile` sudah disediakan di root proyek, berbasis `php:8.2-apache`) — tidak ada satu baris pun kode aplikasi yang diubah untuk ini.
- Database MySQL di-host terpisah di provider gratis khusus MySQL: **Clever Cloud** (plan DEV, gratis tanpa kartu kredit).

Struktur database, model, controller, dan seluruh fitur **tidak diubah** — hanya cara koneksinya yang sekarang dibaca dari environment variable, bukan hardcoded.

---

## Bagian 1 — Siapkan Database Online (Clever Cloud)

1. Daftar/login di **https://console.clever-cloud.com**.
2. Klik **Create** → **an add-on** → pilih **MySQL**.
3. Pilih plan **DEV** (gratis, ~5–10MB storage — cukup untuk data demo penelitian ini).
4. Beri nama add-on, mis. `lokaltrust-mysql`, lalu buat.
5. Setelah aktif, buka tab **Information** pada add-on tersebut. Catat 5 nilai berikut (nama field persis seperti ini di Clever Cloud):
   - `MYSQL_ADDON_HOST`
   - `MYSQL_ADDON_PORT` (biasanya `3306`)
   - `MYSQL_ADDON_DB`
   - `MYSQL_ADDON_USER`
   - `MYSQL_ADDON_PASSWORD`
6. Import skema + data demo. Cara termudah: buka tab **phpMyAdmin** (tersedia langsung dari console Clever Cloud untuk add-on MySQL) atau connect via CLI:
   ```bash
   mysql -h <MYSQL_ADDON_HOST> -P <MYSQL_ADDON_PORT> -u <MYSQL_ADDON_USER> -p <MYSQL_ADDON_DB> < database.sql
   ```
   > Catatan: `database.sql` diawali `CREATE DATABASE IF NOT EXISTS lokaltrust_db ... ; USE lokaltrust_db;`. Karena Clever Cloud sudah menyediakan nama database sendiri (`MYSQL_ADDON_DB`, biasanya bukan `lokaltrust_db`), jalankan dulu isi file **tanpa** dua baris pertama itu, atau import langsung lewat phpMyAdmin yang otomatis mengarah ke database add-on Anda (lebih aman, tidak perlu edit file).

---

## Bagian 2 — Deploy ke Render.com

### Opsi A — Blueprint (tercepat, pakai `render.yaml` yang sudah disediakan)
1. Push repo ini ke GitHub (jika belum).
2. Di dashboard Render → **New** → **Blueprint**.
3. Pilih repo GitHub proyek ini. Render otomatis membaca `render.yaml`.
4. Saat diminta mengisi environment variable (yang ditandai `sync: false`), isi dengan nilai dari Clever Cloud (Bagian 1):
   | Env Var Render | Isi dengan |
   |---|---|
   | `DB_HOST` | `MYSQL_ADDON_HOST` |
   | `DB_USER` | `MYSQL_ADDON_USER` |
   | `DB_PASSWORD` | `MYSQL_ADDON_PASSWORD` |
5. Klik **Apply** / **Deploy**.

### Opsi B — Manual (New Web Service)
1. Dashboard Render → **New** → **Web Service** → connect ke repo GitHub ini.
2. **Runtime**: pilih **Docker** (Render otomatis mendeteksi `Dockerfile` di root).
3. **Region**: bebas (Singapore paling dekat untuk demo di Indonesia).
4. **Instance Type**: **Free**.
5. Tambahkan Environment Variables (tab **Environment**):

   | Key | Value |
   |---|---|
   | `APP_ENV` | `production` |
   | `DB_HOST` | *(dari Clever Cloud `MYSQL_ADDON_HOST`)* |
   | `DB_PORT` | *(dari Clever Cloud `MYSQL_ADDON_PORT`, biasanya `3306`)* |
   | `DB_NAME` | *(dari Clever Cloud `MYSQL_ADDON_DB`)* |
   | `DB_USER` | *(dari Clever Cloud `MYSQL_ADDON_USER`)* |
   | `DB_PASSWORD` | *(dari Clever Cloud `MYSQL_ADDON_PASSWORD`)* |
   | `BASE_URL` | *(kosongkan)* |

6. Klik **Create Web Service**. Render akan build image dari `Dockerfile` (butuh beberapa menit di build pertama) lalu deploy otomatis.
7. Setelah status **Live**, buka URL yang diberikan Render (mis. `https://lokaltrust.onrender.com` jika nama service disamakan menjadi `lokaltrust`).

---

## Bagian 3 — Verifikasi Setelah Deploy

Cek route-route berikut sudah bisa dibuka:

| Route | Fungsi |
|---|---|
| `/` | Landing page |
| `/login` | Halaman login |
| `/dashboard` | Dashboard (setelah login) |
| `/products` | Manajemen produk |
| `/verify/CERT-LOKALTRUST-2026-0001` | Verifikasi publik sertifikat demo |

Login dengan salah satu akun demo (lihat README) untuk memastikan koneksi database online berhasil dan data tampil (Total Users, daftar produk, dsb).

### Known limitation — foto upload di free tier
Render **Free Web Service tidak punya persistent disk** — setiap kali service di-restart/redeploy, seluruh isi filesystem container (termasuk foto produk yang di-upload user *setelah* deploy) akan hilang dan kembali ke kondisi image Docker semula. Foto seed (`seed_rendang.jpg`, dll.) aman karena ikut ter-build ke dalam image, tapi **foto baru yang di-upload lewat form Tambah/Edit Produk tidak permanen** di plan gratis ini. Ini adalah batasan platform gratis, bukan bug aplikasi — cukup didokumentasikan untuk keperluan demo penelitian (bukan untuk skala produksi).

---

## Troubleshooting

| Gejala | Penyebab | Solusi |
|---|---|---|
| "Layanan sedang tidak tersedia" saat buka situs | Kredensial DB salah / add-on Clever Cloud belum aktif | Cek ulang 5 env var di Render sesuai tab Information Clever Cloud |
| Build gagal di Render | Error di `Dockerfile` | Cek tab **Logs** di Render saat build; error umumnya soal ekstensi PHP yang gagal diinstall |
| Situs lambat merespons pertama kali dibuka | Render Free tier "sleep" setelah 15 menit tanpa traffic | Normal untuk free tier — request pertama butuh ~30–60 detik untuk "membangunkan" service |
| CSS/gambar tidak muncul | `BASE_URL` diisi padahal service ada di root domain | Kosongkan `BASE_URL` di environment variable Render |
| Foto produk hilang setelah beberapa waktu | Ephemeral disk Render Free (lihat catatan di atas) | Batasan platform gratis — untuk demo, gunakan foto seed atau upload ulang sebelum sesi demo |
