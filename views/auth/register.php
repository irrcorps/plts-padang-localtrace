<?php $pageTitle = 'Register'; require __DIR__ . '/../partials/header.php'; ?>
<?php require __DIR__ . '/../partials/navbar.php'; ?>

<main class="auth-page">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9">
                <div class="auth-card">
                    <div class="text-center mb-4">
                        <span class="brand-mark brand-mark-lg mx-auto mb-3"><i class="bi bi-link-45deg"></i></span>
                        <h3 class="fw-bold mb-1">Daftar Akun Baru</h3>
                        <p class="text-muted-plts small mb-0">Bergabung sebagai pelaku rantai pasok produk lokal Padang</p>
                    </div>

                    <?php if ($msg = getFlash('error')): ?>
                        <div class="alert alert-danger py-2 small"><?= e($msg) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="<?= BASE_URL ?>/register.php" novalidate>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control form-control-plts" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control form-control-plts" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control form-control-plts" minlength="6" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Konfirmasi Password</label>
                                <input type="password" name="confirm_password" class="form-control form-control-plts" minlength="6" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Daftar Sebagai</label>
                                <select name="role" class="form-select form-control-plts" required>
                                    <option value="" selected disabled>Pilih peran</option>
                                    <option value="producer">Producer / UMKM</option>
                                    <option value="distributor">Distributor</option>
                                    <option value="retailer">Retailer</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Usaha / Perusahaan</label>
                                <input type="text" name="company_name" class="form-control form-control-plts">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No. Telepon</label>
                                <input type="text" name="phone" class="form-control form-control-plts">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Alamat</label>
                                <input type="text" name="address" class="form-control form-control-plts">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-plts-primary w-100 py-2 mt-4">
                            <i class="bi bi-person-plus me-1"></i> Daftar
                        </button>
                    </form>

                    <p class="text-center small text-muted-plts mt-4 mb-0">
                        Sudah punya akun? <a href="<?= BASE_URL ?>/login.php" class="link-plts">Login di sini</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>
