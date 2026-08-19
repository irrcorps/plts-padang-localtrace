    <footer class="plts-footer">
        <div class="container py-5">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="brand-mark"><i class="bi bi-link-45deg"></i></span>
                        <span class="fw-bold text-white fs-5">PLTS</span>
                    </div>
                    <p class="text-secondary-light small mb-0">
                        Padang LocalTrace System — Prototipe penelitian PDP 2026 untuk sistem
                        traceability dan sertifikasi digital produk ritel lokal berbasis konsep
                        blockchain dan smart contract, guna mendukung hilirisasi produk UMKM Kota Padang.
                    </p>
                </div>
                <div class="col-lg-2 col-6">
                    <h6 class="text-white mb-3">Navigasi</h6>
                    <ul class="list-unstyled small footer-links">
                        <li><a href="<?= BASE_URL ?>/index.php#alur">Alur Sistem</a></li>
                        <li><a href="<?= BASE_URL ?>/index.php#penelitian">Tentang Penelitian</a></li>
                        <li><a href="<?= BASE_URL ?>/verify.php">Verify Product</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-6">
                    <h6 class="text-white mb-3">Akun</h6>
                    <ul class="list-unstyled small footer-links">
                        <li><a href="<?= BASE_URL ?>/login.php">Login</a></li>
                        <li><a href="<?= BASE_URL ?>/register.php">Register</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h6 class="text-white mb-3">Tentang Penelitian</h6>
                    <p class="text-secondary-light small mb-0">
                        Penelitian Dosen Pemula (PDP) 2026 &mdash; "Perancangan Sistem Traceability
                        dan Sertifikasi Produk Ritel Lokal Berbasis Blockchain dan Smart Contract
                        untuk Penguatan Hilirisasi di Kota Padang". Status: Prototipe TKT 3.
                    </p>
                </div>
            </div>
            <hr class="border-secondary my-4">
            <p class="text-secondary-light small mb-0 text-center">
                &copy; <?= date('Y') ?> Padang LocalTrace System. Prototipe akademik &mdash; bukan produk komersial.
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>
