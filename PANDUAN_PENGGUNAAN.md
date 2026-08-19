# Panduan Penggunaan Sistem
## Padang LocalTrace System (PLTS)

Dokumen ini menjelaskan cara menggunakan seluruh fitur sistem untuk masing-masing peran pengguna (role).

---

## 1. Gambaran Umum Alur Sistem

```
PRODUCER  →  DISTRIBUTOR  →  RETAILER  →  CONSUMER (Publik)
(Buat &      (Catat            (Catat            (Verifikasi
 ajukan       distribusi)       penerimaan)        via QR/kode)
 produk)

                    ↑
              ADMIN mengawasi seluruh proses:
              verifikasi → terbitkan sertifikat digital
```

Setiap aktivitas di atas otomatis tercatat sebagai **traceability log** yang saling terhubung lewat hash (konsep *hash-linked ledger*), dan direkam ulang ke **Blockchain Ledger Simulation** yang bisa dilihat admin di dashboard.

### Status Produk
```
Draft → Submitted → Verified → Certified
                 ↘ Rejected  ↗ (bisa diedit & diajukan ulang)
```

| Status | Arti | Siapa yang mengubah |
|---|---|---|
| **Draft** | Produk baru dibuat, belum diajukan | Producer |
| **Submitted** | Diajukan untuk sertifikasi, menunggu admin | Producer → Admin |
| **Verified** | Lolos pengecekan kelengkapan oleh admin | Admin |
| **Certified** | Sertifikat digital + QR resmi diterbitkan | Admin (otomatis via smart contract) |
| **Rejected** | Ditolak admin, disertai alasan | Admin → kembali ke Producer |

---

## 2. Akun Demo

| Role | Email | Password |
|---|---|---|
| Admin | admin@plts.test | password123 |
| Producer / UMKM | producer@plts.test | password123 |
| Distributor | distributor@plts.test | password123 |
| Retailer | retailer@plts.test | password123 |

Atau daftar akun baru sendiri lewat halaman **Register** (pilih role: Producer, Distributor, atau Retailer — role Admin tidak bisa didaftar sendiri demi keamanan).

---

## 3. Panduan untuk Pengunjung Publik (Tanpa Login)

### a. Melihat Landing Page
Buka halaman utama untuk membaca:
- Latar belakang masalah traceability produk lokal
- Konsep blockchain & smart contract yang diadopsi
- Alur sistem Producer → Distributor → Retail → Consumer
- Informasi penelitian PDP 2026

### b. Verifikasi Produk (`Verify Product`)
1. Klik menu **Verify Product** di navbar (tidak perlu login).
2. Masukkan **nomor sertifikat** (contoh: `CERT-PLTS-2026-0001`) atau **kode produk** (contoh: `PROD-001`) — bisa diketik manual atau hasil scan QR code pada kemasan.
3. Sistem menampilkan:
   - Informasi produk & producer
   - Status sertifikat (Aktif/Dicabut)
   - **Blockchain Verification Simulation** — daftar block ledger terkait produk tersebut
   - **Supply Chain History** — timeline lengkap dari Production sampai Consumer Verification
   - **Transaction Hash** sertifikat
4. Setiap kali sertifikat berhasil diverifikasi, sistem otomatis mencatat aktivitas **Consumer Verification** pada riwayat produk (maksimal 1 catatan per 5 menit agar tidak menumpuk).

---

## 4. Panduan untuk Producer / UMKM

### a. Registrasi & Login
1. Klik **Register**, isi Nama, Email, Password, pilih role **Producer / UMKM**, lengkapi Nama Usaha, Telepon, dan Alamat.
2. Setelah daftar, sistem otomatis login dan mengarahkan ke **Dashboard Producer**.

### b. Dashboard Producer
Menampilkan ringkasan: Total Produk, Produk Certified, Menunggu Verifikasi, Draft, serta 5 produk terbaru.

### c. Menambah Produk Baru
1. Klik **Tambah Produk** (dari dashboard atau menu **Produk**).
2. Isi: Nama Produk, Kategori, Deskripsi, Tanggal Produksi, Lokasi Asal, dan **unggah foto produk** (JPG/PNG/WEBP, maks 2MB).
3. Klik **Simpan sebagai Draft**.
4. Sistem otomatis mencatat aktivitas **Production** pertama pada traceability log produk tersebut.

### d. Mengedit / Menghapus Produk
- Hanya bisa dilakukan selama status produk **Draft** atau **Rejected**.
- Buka detail produk → **Edit Produk** atau **Hapus Produk**.

### e. Mengajukan Sertifikasi
1. Buka detail produk berstatus **Draft**.
2. Pastikan foto produk sudah diunggah (tombol nonaktif jika belum ada foto).
3. Klik **Ajukan Sertifikasi** → status berubah menjadi **Submitted**, menunggu review admin.

### f. Jika Produk Ditolak (Rejected)
- Alasan penolakan dari admin akan tampil di halaman detail produk.
- Perbaiki data sesuai catatan, lalu **Edit Produk** dan **Ajukan Sertifikasi** kembali.

### g. Melihat Sertifikat & QR Code
Setelah status **Certified**, halaman detail produk menampilkan:
- Nomor sertifikat, penerbit, tanggal terbit, transaction hash
- **QR Code** yang bisa di-scan untuk membuka halaman verifikasi publik
- Link langsung ke halaman verifikasi publik

---

## 5. Panduan untuk Distributor

### a. Dashboard Distributor
Menampilkan Total Aktivitas Tercatat, Total Produk Sistem, Produk Certified, dan riwayat aktivitas distribusi terbaru milik akun Anda.

### b. Mencatat Aktivitas Distribusi
1. Klik menu **Produk** → pilih produk yang ingin dicatat (distributor bisa melihat **semua** produk lintas producer).
2. Buka detail produk (syarat: status produk **bukan Draft**).
3. Isi form **Catat Distribusi**: Lokasi, Catatan (opsional).
4. Klik **Catat Distribusi** → aktivitas baru langsung muncul di Traceability Timeline dengan hash unik yang terhubung ke aktivitas sebelumnya.

> Jika status produk masih **Draft**, form pencatatan disembunyikan dan muncul peringatan bahwa producer belum mengajukan produk tersebut.

---

## 6. Panduan untuk Retailer

Alurnya sama seperti Distributor, dengan perbedaan:
- Aktivitas yang dicatat berjenis **Retail Receiving** (penerimaan produk di toko).
- Dashboard menampilkan statistik & riwayat khusus aktivitas penerimaan retail milik akun Anda.

---

## 7. Panduan untuk Admin

### a. Dashboard Analitik
Menampilkan:
- **4 kartu ringkasan**: Total Users, Total Produk, Produk Certified, Transactions (jumlah block pada ledger)
- **Grafik Statistik Sertifikasi Produk** (doughnut chart per status)
- **Grafik Aktivitas Traceability** (bar chart per jenis aktivitas)
- **Tabel Produk Terbaru** dari seluruh producer
- **Tabel Blockchain Ledger Simulation**: Block ID, Hash, Produk, Aktor, Timestamp, Status

### b. Meninjau & Memverifikasi Produk (Submitted → Verified)
1. Buka menu **Produk**, cari produk berstatus **Submitted**, klik ikon mata untuk membuka detail.
2. Panel **Tinjauan Sertifikasi — Smart Contract Simulation** menampilkan checklist otomatis (nama produk, kategori, deskripsi, tanggal produksi, lokasi, dokumen foto).
3. Jika seluruh syarat terpenuhi (centang hijau semua), klik **Verifikasi Produk** → status menjadi **Verified**.
4. Jika ada data tidak lengkap/tidak valid, klik **Tolak Produk**, isi alasan penolakan, kirim → status menjadi **Rejected** dan producer akan melihat alasannya.

### c. Menerbitkan Sertifikat Digital (Verified → Certified)
1. Pada produk berstatus **Verified**, panel **Smart Contract — Penerbitan Sertifikat** menampilkan logika:
   ```
   IF produk_lengkap = true AND dokumen_valid = true AND diverifikasi_admin = true
   THEN generate_certificate() & append_to_ledger()
   ```
2. Klik **Terbitkan Sertifikat Digital**.
3. Sistem otomatis:
   - Membuat nomor sertifikat unik (`CERT-PLTS-{tahun}-{urutan}`)
   - Menghitung *certificate hash* (transaction hash)
   - Menambahkan block baru ke Blockchain Ledger Simulation
   - Mengubah status produk menjadi **Certified**
   - Menghasilkan **QR Code** otomatis di halaman produk

### d. Memantau Seluruh Produk
Menu **Produk** (sebagai admin) menampilkan seluruh produk dari semua producer beserta kolom nama producer — bersifat **read-only** (admin tidak bisa mengedit/menghapus produk producer, hanya menjalankan aksi sertifikasi).

---

## 8. Ringkasan Hak Akses per Role

| Fitur | Producer | Distributor | Retailer | Admin | Publik |
|---|:---:|:---:|:---:|:---:|:---:|
| Lihat landing page | ✅ | ✅ | ✅ | ✅ | ✅ |
| Verifikasi produk publik | ✅ | ✅ | ✅ | ✅ | ✅ |
| CRUD produk sendiri | ✅ | ❌ | ❌ | ❌ | ❌ |
| Lihat semua produk | ❌ (hanya milik sendiri) | ✅ (read-only) | ✅ (read-only) | ✅ (read-only) | ❌ |
| Ajukan sertifikasi | ✅ | ❌ | ❌ | ❌ | ❌ |
| Catat aktivitas Distribution | ❌ | ✅ | ❌ | ❌ | ❌ |
| Catat aktivitas Retail Receiving | ❌ | ❌ | ✅ | ❌ | ❌ |
| Verifikasi & terbitkan sertifikat | ❌ | ❌ | ❌ | ✅ | ❌ |
| Dashboard analitik & ledger | ❌ | ❌ | ❌ | ✅ | ❌ |

---

## 9. Pertanyaan Umum (FAQ)

**Q: Kenapa tombol "Ajukan Sertifikasi" tidak bisa diklik?**
A: Produk belum memiliki foto. Edit produk dan unggah foto terlebih dahulu.

**Q: Kenapa Distributor/Retailer tidak bisa mencatat aktivitas pada suatu produk?**
A: Produk masih berstatus **Draft** — producer belum mengajukannya untuk sertifikasi.

**Q: Bagaimana cara mengetahui produk itu asli/terverifikasi sebagai konsumen?**
A: Scan QR code pada kemasan atau buka menu **Verify Product**, masukkan nomor sertifikat yang tertera.

**Q: Apakah hash pada traceability log dan blockchain ledger benar-benar blockchain?**
A: Ini adalah **simulasi berbasis konsep blockchain** (hash-linked ledger + smart contract sederhana) untuk keperluan pembuktian konsep (proof of concept) penelitian TKT 3 — bukan implementasi jaringan blockchain terdesentralisasi sungguhan.

**Q: Kenapa QR Code atau grafik dashboard tidak muncul?**
A: Fitur tersebut memuat pustaka dari CDN (qrcode.js, Chart.js) sehingga membutuhkan koneksi internet aktif saat sistem dijalankan.
