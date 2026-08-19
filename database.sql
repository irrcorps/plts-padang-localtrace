-- =====================================================================
-- LokalTrust
-- Blockchain-Based Traceability and Digital Certification Platform
-- for Local Retail Products
-- Database Schema + Sample Data
-- Penelitian PDP 2026 - Prototipe TKT 3
-- =====================================================================
--
-- Catatan migrasi (deployment online):
-- Struktur tabel TIDAK diubah dari versi lokal (prinsip: jangan ubah
-- struktur database & fitur utama). Satu-satunya penambahan adalah VIEW
-- `transactions` di akhir file sebagai alias baca-saja ke tabel
-- `blockchain_blocks`, agar tabel bernama literal "transactions" tersedia
-- untuk keperluan reviewer/dokumentasi tanpa mengubah kode aplikasi yang
-- sudah menggunakan nama tabel blockchain_blocks di seluruh model/controller.
-- =====================================================================

CREATE DATABASE IF NOT EXISTS lokaltrust_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE lokaltrust_db;

-- ---------------------------------------------------------------------
-- Table: users
-- ---------------------------------------------------------------------
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','producer','distributor','retailer') NOT NULL,
    company_name VARCHAR(150) DEFAULT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table: products
-- ---------------------------------------------------------------------
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producer_id INT NOT NULL,
    product_code VARCHAR(30) NOT NULL UNIQUE,
    product_name VARCHAR(150) NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    production_date DATE NOT NULL,
    origin_location VARCHAR(150) NOT NULL,
    photo VARCHAR(255) DEFAULT NULL,
    status ENUM('draft','submitted','verified','certified','rejected') NOT NULL DEFAULT 'draft',
    admin_notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_products_producer FOREIGN KEY (producer_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table: traceability_logs
-- ---------------------------------------------------------------------
CREATE TABLE traceability_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    actor_id INT DEFAULT NULL,
    actor VARCHAR(150) NOT NULL,
    activity ENUM('Production','Distribution','Retail Receiving','Consumer Verification') NOT NULL,
    location VARCHAR(150) NOT NULL,
    notes TEXT DEFAULT NULL,
    timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    data_hash VARCHAR(64) NOT NULL,
    prev_hash VARCHAR(64) DEFAULT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'recorded',
    CONSTRAINT fk_logs_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    CONSTRAINT fk_logs_actor FOREIGN KEY (actor_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table: certificates
-- ---------------------------------------------------------------------
CREATE TABLE certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    certificate_number VARCHAR(60) NOT NULL UNIQUE,
    issuer VARCHAR(150) NOT NULL DEFAULT 'LokalTrust Certification Authority',
    issued_date DATE NOT NULL,
    certificate_hash VARCHAR(64) NOT NULL,
    qr_path VARCHAR(255) DEFAULT NULL,
    status ENUM('active','revoked') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_certificates_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table: blockchain_blocks (simulated ledger / transaction record)
-- ---------------------------------------------------------------------
CREATE TABLE blockchain_blocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    block_index INT NOT NULL,
    product_id INT DEFAULT NULL,
    actor VARCHAR(150) NOT NULL,
    reference_type VARCHAR(50) NOT NULL,
    reference_id INT NOT NULL,
    block_hash VARCHAR(64) NOT NULL,
    prev_hash VARCHAR(64) DEFAULT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'valid',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_blocks_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =====================================================================
-- SAMPLE DATA — Demo Penelitian LokalTrust
-- =====================================================================

-- Users (password untuk semua akun demo: password123)
-- 1 admin, 3 producer, 1 distributor, 2 retailer
INSERT INTO users (name, email, password, role, company_name, phone, address) VALUES
('Admin LokalTrust', 'admin@lokaltrust.com', '$2y$10$EQ6pNlA30Ge8rbJG9exyFOnwMxmzMrfSbhIpEuwJxOzQyzrxf03Em', 'admin', 'LokalTrust Certification Authority', '0751-000000', 'Kota Padang, Sumatera Barat'),
('Yusra Amelia', 'producer@lokaltrust.com', '$2y$10$EQ6pNlA30Ge8rbJG9exyFOnwMxmzMrfSbhIpEuwJxOzQyzrxf03Em', 'producer', 'UMKM Rendang Minang Asli', '0812-1111-2222', 'Jl. Gajah Mada, Padang'),
('Fajri Ramadhan', 'producer2@lokaltrust.com', '$2y$10$EQ6pNlA30Ge8rbJG9exyFOnwMxmzMrfSbhIpEuwJxOzQyzrxf03Em', 'producer', 'UMKM Keripik Balado Bundo', '0812-4444-5555', 'Jl. Adinegoro, Padang'),
('Nadia Putri', 'producer3@lokaltrust.com', '$2y$10$EQ6pNlA30Ge8rbJG9exyFOnwMxmzMrfSbhIpEuwJxOzQyzrxf03Em', 'producer', 'Kelompok Tani Kopi Solok', '0812-6666-7777', 'Alahan Panjang, Solok'),
('Budi Santoso', 'distributor@lokaltrust.com', '$2y$10$EQ6pNlA30Ge8rbJG9exyFOnwMxmzMrfSbhIpEuwJxOzQyzrxf03Em', 'distributor', 'CV Distribusi Minang Jaya', '0813-2222-3333', 'Jl. By Pass, Padang'),
('Rani Permata', 'retailer@lokaltrust.com', '$2y$10$EQ6pNlA30Ge8rbJG9exyFOnwMxmzMrfSbhIpEuwJxOzQyzrxf03Em', 'retailer', 'Toko Oleh-Oleh Minang Asli', '0814-3333-4444', 'Jl. Khatib Sulaiman, Padang'),
('Dewi Lestari', 'retailer2@lokaltrust.com', '$2y$10$EQ6pNlA30Ge8rbJG9exyFOnwMxmzMrfSbhIpEuwJxOzQyzrxf03Em', 'retailer', 'Minang Mart Modern', '0814-8888-9999', 'Jl. Veteran, Padang');

-- Products (produk lokal Padang) — masing-masing milik producer berbeda
-- Foto: seed_rendang.jpg, seed_keripik.jpg, seed_kopi.jpg (folder uploads/products/)
INSERT INTO products (producer_id, product_code, product_name, category, description, production_date, origin_location, photo, status) VALUES
(2, 'PROD-001', 'Rendang Minang Premium', 'Makanan Olahan', 'Rendang daging sapi asli Minang, dikemas vakum tahan 6 bulan tanpa pengawet.', '2026-01-10', 'Padang, Sumatera Barat', 'seed_rendang.jpg', 'certified'),
(3, 'PROD-002', 'Keripik Balado Padang', 'Makanan Ringan', 'Keripik singkong pedas khas Padang dengan bumbu balado autentik.', '2026-02-01', 'Padang, Sumatera Barat', 'seed_keripik.jpg', 'certified'),
(4, 'PROD-003', 'Kopi Lokal Minang', 'Minuman', 'Kopi robusta pilihan dari dataran tinggi Sumatera Barat, sangrai medium.', '2026-02-15', 'Solok, Sumatera Barat', 'seed_kopi.jpg', 'verified');

-- Traceability logs (transaksi rantai pasok)
INSERT INTO traceability_logs (product_id, actor_id, actor, activity, location, notes, timestamp, data_hash, prev_hash, status) VALUES
(1, 2, 'UMKM Rendang Minang Asli', 'Production', 'Padang, Sumatera Barat', 'Produksi batch pertama Rendang Minang Premium.', '2026-01-10 08:00:00', '3ce49c995137855532c964f74b067132fb0098163a8b7986a0b0704f5acc0164', NULL, 'recorded'),
(1, 5, 'CV Distribusi Minang Jaya', 'Distribution', 'Gudang Distribusi, By Pass Padang', 'Produk dikirim ke jaringan retail mitra.', '2026-01-15 10:30:00', '6c53b91f1440542c673d10c72e5c4d6ae4ecbe362e46d6c2617f9a6f9d7ca444', '3ce49c995137855532c964f74b067132fb0098163a8b7986a0b0704f5acc0164', 'recorded'),
(1, 6, 'Toko Oleh-Oleh Minang Asli', 'Retail Receiving', 'Jl. Khatib Sulaiman, Padang', 'Produk diterima dan siap dijual di toko.', '2026-01-20 09:15:00', 'f9e70c28d1a2ef87451c15d7b93f6d97ac67c47891eb603ac46417266c3accd5', '6c53b91f1440542c673d10c72e5c4d6ae4ecbe362e46d6c2617f9a6f9d7ca444', 'recorded'),
(2, 3, 'UMKM Keripik Balado Bundo', 'Production', 'Padang, Sumatera Barat', 'Produksi Keripik Balado Padang batch Februari.', '2026-02-01 08:00:00', 'f70ebe5e8825988f50cac6e046dbacb5a4f9dc7a3de6736b3a6baebd8cc2d6bf', NULL, 'recorded'),
(2, 5, 'CV Distribusi Minang Jaya', 'Distribution', 'Gudang Distribusi, By Pass Padang', 'Distribusi ke retailer mitra kota Padang.', '2026-02-05 13:00:00', '71f0e9fe371930a84d373f04249e390488cf62f1046752a94a9e95e4517ea380', 'f70ebe5e8825988f50cac6e046dbacb5a4f9dc7a3de6736b3a6baebd8cc2d6bf', 'recorded'),
(2, 7, 'Minang Mart Modern', 'Retail Receiving', 'Jl. Veteran, Padang', 'Produk diterima dan dipajang di rak toko.', '2026-02-08 09:40:00', '0cf235d7a0794c4802ecf83c608d4193edd1651cc075becbfbcb295a409fab20', '71f0e9fe371930a84d373f04249e390488cf62f1046752a94a9e95e4517ea380', 'recorded'),
(3, 4, 'Kelompok Tani Kopi Solok', 'Production', 'Alahan Panjang, Solok', 'Produksi Kopi Lokal Minang, siap diajukan sertifikasi.', '2026-02-15 07:30:00', '3437f4a3b1420ac1c3d04747e3e0cbf89f1830a6b103753955d7309ebd5673a1', NULL, 'recorded');

-- Certificates (sertifikat digital)
INSERT INTO certificates (product_id, certificate_number, issuer, issued_date, certificate_hash, status) VALUES
(1, 'CERT-LOKALTRUST-2026-0001', 'LokalTrust Certification Authority', '2026-01-22', '7600cb06ea59f3c72eca16ae7bd527ba13f17602d209235354172a223d0558af', 'active'),
(2, 'CERT-LOKALTRUST-2026-0002', 'LokalTrust Certification Authority', '2026-02-10', '55f7d992189a99bfa0ce73fcef684fee759b724a048a25381a24988991676dc5', 'active');

-- Blockchain ledger simulation (urut kronologis lintas seluruh produk)
INSERT INTO blockchain_blocks (block_index, product_id, actor, reference_type, reference_id, block_hash, prev_hash, status, created_at) VALUES
(1, 1, 'UMKM Rendang Minang Asli', 'traceability_log', 1, 'c8341f5f157568da3aaa19756dd04413ec7ed0cbc48ccb92bc59aa99c8966611', NULL, 'valid', '2026-01-10 08:00:05'),
(2, 1, 'CV Distribusi Minang Jaya', 'traceability_log', 2, 'f90401503a6677915a8c92663aee7c7bfc6a0ed86d9432342b4b8d36aaa4d5ac', 'c8341f5f157568da3aaa19756dd04413ec7ed0cbc48ccb92bc59aa99c8966611', 'valid', '2026-01-15 10:30:05'),
(3, 1, 'Toko Oleh-Oleh Minang Asli', 'traceability_log', 3, '15f0cb9b2ae8170591374991f645f53607ed9648bf23f6e2764655837645064d', 'f90401503a6677915a8c92663aee7c7bfc6a0ed86d9432342b4b8d36aaa4d5ac', 'valid', '2026-01-20 09:15:05'),
(4, 1, 'LokalTrust Certification Authority', 'certificate', 1, '16e41badfaa62ec09518582f15d3251815b14d102929a01472d17f3f7ecb3d78', '15f0cb9b2ae8170591374991f645f53607ed9648bf23f6e2764655837645064d', 'valid', '2026-01-22 11:00:00'),
(5, 2, 'UMKM Keripik Balado Bundo', 'traceability_log', 4, '0316d4fa704db5bdc5f46d94b02b4a17492caff1b6abe3e90387f4a67affc49e', '16e41badfaa62ec09518582f15d3251815b14d102929a01472d17f3f7ecb3d78', 'valid', '2026-02-01 08:00:05'),
(6, 2, 'CV Distribusi Minang Jaya', 'traceability_log', 5, 'aab4395cbad32a2cd4746c3a9931b361cdab9a663149ea7db9e65b5d342f7f15', '0316d4fa704db5bdc5f46d94b02b4a17492caff1b6abe3e90387f4a67affc49e', 'valid', '2026-02-05 13:00:05'),
(7, 2, 'Minang Mart Modern', 'traceability_log', 6, '7f86640a5e816540c8138e649c2c509fbfdd90aedc817eb4eebaedd4014f1eff', 'aab4395cbad32a2cd4746c3a9931b361cdab9a663149ea7db9e65b5d342f7f15', 'valid', '2026-02-08 09:40:05'),
(8, 2, 'LokalTrust Certification Authority', 'certificate', 2, 'b1e1803f9b6e2d4d394dcd866c5e5783a1a47c01dc0505ec29ea74a463550cbc', '7f86640a5e816540c8138e649c2c509fbfdd90aedc817eb4eebaedd4014f1eff', 'valid', '2026-02-10 11:00:00'),
(9, 3, 'Kelompok Tani Kopi Solok', 'traceability_log', 7, '4ee659d096c4f69c01cf3e6a9a6778c437e2db966232df5b32935187a8539524', 'b1e1803f9b6e2d4d394dcd866c5e5783a1a47c01dc0505ec29ea74a463550cbc', 'valid', '2026-02-15 07:30:05');

-- =====================================================================
-- VIEW: transactions
-- Alias baca-saja untuk blockchain_blocks, disediakan agar tabel bernama
-- literal "transactions" tersedia untuk keperluan dokumentasi/reviewer.
-- Tidak dipakai langsung oleh kode aplikasi (aplikasi tetap memakai
-- blockchain_blocks) sehingga tidak mengubah struktur/fitur yang sudah ada.
-- =====================================================================
CREATE OR REPLACE VIEW transactions AS
SELECT
    id AS transaction_id,
    block_index,
    product_id,
    actor,
    reference_type,
    reference_id,
    block_hash AS transaction_hash,
    prev_hash,
    status,
    created_at
FROM blockchain_blocks;
