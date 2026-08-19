<?php
/**
 * Konfigurasi koneksi database.
 *
 * Nilai diambil dari environment variable (untuk deployment online/Docker),
 * dengan fallback ke kredensial default XAMPP untuk pengembangan lokal.
 * JANGAN hardcode kredensial produksi di file ini — set via environment
 * variable pada platform hosting (Render dashboard, dsb).
 */
define('APP_ENV', getenv('APP_ENV') ?: 'local'); // 'local' | 'production'

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'lokaltrust_db');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASSWORD') ?: '');
define('DB_CHARSET', 'utf8mb4');

// Base URL aplikasi (tanpa trailing slash). Kosongkan jika di root domain.
define('BASE_URL', getenv('BASE_URL') ?: '');
