-- =====================================================================
-- Padang LocalTrace System (PLTS)
-- Blockchain-Based Traceability and Digital Certification Platform
-- Database Schema + Sample Data
-- Penelitian PDP 2026 - Prototipe TKT 3
-- =====================================================================

CREATE DATABASE IF NOT EXISTS plts_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE plts_db;

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
    issuer VARCHAR(150) NOT NULL DEFAULT 'PLTS Certification Authority',
    issued_date DATE NOT NULL,
    certificate_hash VARCHAR(64) NOT NULL,
    qr_path VARCHAR(255) DEFAULT NULL,
    status ENUM('active','revoked') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_certificates_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table: blockchain_blocks (simulated ledger for admin dashboard)
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
-- SAMPLE DATA
-- =====================================================================

-- Users (password untuk semua akun demo: password123)
INSERT INTO users (name, email, password, role, company_name, phone, address) VALUES
('Admin PLTS', 'admin@plts.test', '$2y$10$EQ6pNlA30Ge8rbJG9exyFOnwMxmzMrfSbhIpEuwJxOzQyzrxf03Em', 'admin', 'PLTS Certification Authority', '0751-000000', 'Kota Padang, Sumatera Barat'),
('Siti Rahmah', 'producer@plts.test', '$2y$10$EQ6pNlA30Ge8rbJG9exyFOnwMxmzMrfSbhIpEuwJxOzQyzrxf03Em', 'producer', 'UMKM Rendang Uni Siti', '0812-1111-2222', 'Jl. Gajah Mada, Padang'),
('Budi Santoso', 'distributor@plts.test', '$2y$10$EQ6pNlA30Ge8rbJG9exyFOnwMxmzMrfSbhIpEuwJxOzQyzrxf03Em', 'distributor', 'CV Distribusi Minang Jaya', '0813-2222-3333', 'Jl. By Pass, Padang'),
('Rani Permata', 'retailer@plts.test', '$2y$10$EQ6pNlA30Ge8rbJG9exyFOnwMxmzMrfSbhIpEuwJxOzQyzrxf03Em', 'retailer', 'Toko Oleh-Oleh Minang Asli', '0814-3333-4444', 'Jl. Khatib Sulaiman, Padang');

-- Products (produk lokal Padang)
-- Catatan: file seed_rendang.jpg, seed_keripik.jpg, seed_kopi.jpg disertakan di folder uploads/products/
INSERT INTO products (producer_id, product_code, product_name, category, description, production_date, origin_location, photo, status) VALUES
(2, 'PROD-001', 'Rendang Kemasan', 'Makanan Olahan', 'Rendang daging sapi asli Minang, dikemas vakum tahan 6 bulan tanpa pengawet.', '2026-01-10', 'Padang, Sumatera Barat', 'seed_rendang.jpg', 'certified'),
(2, 'PROD-002', 'Keripik Balado', 'Makanan Ringan', 'Keripik singkong pedas khas Padang dengan bumbu balado autentik.', '2026-02-01', 'Padang, Sumatera Barat', 'seed_keripik.jpg', 'verified'),
(2, 'PROD-003', 'Kopi Minang', 'Minuman', 'Kopi robusta pilihan dari dataran tinggi Sumatera Barat, sangrai medium.', '2026-01-25', 'Solok, Sumatera Barat', 'seed_kopi.jpg', 'submitted');

-- Traceability logs
INSERT INTO traceability_logs (product_id, actor_id, actor, activity, location, notes, timestamp, data_hash, prev_hash, status) VALUES
(1, 2, 'UMKM Rendang Uni Siti', 'Production', 'Padang, Sumatera Barat', 'Produksi batch pertama rendang kemasan.', '2026-01-10 08:00:00', 'acac394a3c07b4f03bf2a6d4b3fd8e8ba6e0ef3ab66cff2811a28ba70e72e894', NULL, 'recorded'),
(1, 3, 'CV Distribusi Minang Jaya', 'Distribution', 'Gudang Distribusi, By Pass Padang', 'Produk dikirim ke jaringan retail mitra.', '2026-01-15 10:30:00', 'f14b8b2c244b444098a5ed5ebb22c0820b73deea4c06d2aa56e8aa1c1c31c736', 'acac394a3c07b4f03bf2a6d4b3fd8e8ba6e0ef3ab66cff2811a28ba70e72e894', 'recorded'),
(1, 4, 'Toko Oleh-Oleh Minang Asli', 'Retail Receiving', 'Jl. Khatib Sulaiman, Padang', 'Produk diterima dan siap dijual di toko.', '2026-01-20 09:15:00', 'a15f29fdfca3f880c3db31763af261f2bb37283d2fbf73322166750211e95fec', 'f14b8b2c244b444098a5ed5ebb22c0820b73deea4c06d2aa56e8aa1c1c31c736', 'recorded'),
(2, 2, 'UMKM Rendang Uni Siti', 'Production', 'Padang, Sumatera Barat', 'Produksi keripik balado batch Februari.', '2026-02-01 08:00:00', 'e692997053e3e93c68baa7dba62a67e488223a72c331508984cf71ca5495cc7a', NULL, 'recorded'),
(2, 3, 'CV Distribusi Minang Jaya', 'Distribution', 'Gudang Distribusi, By Pass Padang', 'Distribusi ke retailer mitra kota Padang.', '2026-02-05 13:00:00', '75189d800d5ff193a7053403641bf7fc89ad8a7a68c0e15029650a2f3fe1fc3b', 'e692997053e3e93c68baa7dba62a67e488223a72c331508984cf71ca5495cc7a', 'recorded'),
(3, 2, 'UMKM Rendang Uni Siti', 'Production', 'Solok, Sumatera Barat', 'Produksi kopi Minang siap ajukan sertifikasi.', '2026-01-25 07:30:00', '34eb20aefe9ed533da2498cb803fa522ae1678e33c98fec38b457226f483a5f3', NULL, 'recorded');

-- Certificates
INSERT INTO certificates (product_id, certificate_number, issuer, issued_date, certificate_hash, status) VALUES
(1, 'CERT-PLTS-2026-0001', 'PLTS Certification Authority', '2026-01-22', 'fe877e6ed9daa9857c08d228c0f1e4b0a62ea757c9298def3450cfaa10cf97ce', 'active');

-- Blockchain ledger simulation
INSERT INTO blockchain_blocks (block_index, product_id, actor, reference_type, reference_id, block_hash, prev_hash, status, created_at) VALUES
(1, 1, 'UMKM Rendang Uni Siti', 'traceability_log', 1, '39b8c091cf59a3d66844ddbb341299e52f6f6ec28909cc21f3e8ba478873079a', NULL, 'valid', '2026-01-10 08:00:05'),
(2, 1, 'CV Distribusi Minang Jaya', 'traceability_log', 2, '54502aadfabdf4c72d631b1c23d3c0e23f764a2605a6274d005c413e6aa45fda', '39b8c091cf59a3d66844ddbb341299e52f6f6ec28909cc21f3e8ba478873079a', 'valid', '2026-01-15 10:30:05'),
(3, 1, 'Toko Oleh-Oleh Minang Asli', 'traceability_log', 3, '294816d70d12e7276e832f6dd925c7dcf64713edafd613d53de8c23b8da75285', '54502aadfabdf4c72d631b1c23d3c0e23f764a2605a6274d005c413e6aa45fda', 'valid', '2026-01-20 09:15:05'),
(4, 1, 'PLTS Certification Authority', 'certificate', 1, 'a5813365aa85ea0365c5c3058a9aeb1adc2a0fed328780eab493910ace8c69c9', '294816d70d12e7276e832f6dd925c7dcf64713edafd613d53de8c23b8da75285', 'valid', '2026-01-22 11:00:00'),
(5, 2, 'UMKM Rendang Uni Siti', 'traceability_log', 4, '5c1d650fa72d7bc18ebd7e18ad50e965323d76c39db9d8be951eed4d4ebcc16e', 'a5813365aa85ea0365c5c3058a9aeb1adc2a0fed328780eab493910ace8c69c9', 'valid', '2026-02-01 08:00:05'),
(6, 2, 'CV Distribusi Minang Jaya', 'traceability_log', 5, '3be0b42f6df176043a170e19f3a05a8ce063e1040ef28cd570f4852299484f7a', '5c1d650fa72d7bc18ebd7e18ad50e965323d76c39db9d8be951eed4d4ebcc16e', 'valid', '2026-02-05 13:00:05');
