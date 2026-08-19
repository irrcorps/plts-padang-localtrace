<?php $pageTitle = 'Login'; require __DIR__ . '/../partials/header.php'; ?>
<?php require __DIR__ . '/../partials/navbar.php'; ?>

<main class="auth-page">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="auth-card">
                    <div class="text-center mb-4">
                        <span class="brand-mark brand-mark-lg mx-auto mb-3"><i class="bi bi-link-45deg"></i></span>
                        <h3 class="fw-bold mb-1">Masuk ke LokalTrust</h3>
                        <p class="text-muted-plts small mb-0">Blockchain Traceability Platform</p>
                    </div>

                    <?php if ($msg = getFlash('error')): ?>
                        <div class="alert alert-danger py-2 small"><?= e($msg) ?></div>
                    <?php endif; ?>
                    <?php if ($msg = getFlash('success')): ?>
                        <div class="alert alert-success py-2 small"><?= e($msg) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="<?= BASE_URL ?>/login.php" novalidate>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control form-control-plts" placeholder="nama@email.com" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control form-control-plts" placeholder="Password" required>
                        </div>
                        <button type="submit" class="btn btn-plts-primary w-100 py-2 mt-2">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Login
                        </button>
                    </form>

                    <p class="text-center small text-muted-plts mt-4 mb-0">
                        Belum punya akun? <a href="<?= BASE_URL ?>/register.php" class="link-plts">Register di sini</a>
                    </p>

                    <div class="demo-account-box mt-4">
                        <div class="small fw-semibold mb-2"><i class="bi bi-info-circle me-1"></i>Akun Demo (password: <code>password123</code>)</div>
                        <div class="small text-muted-plts">admin@lokaltrust.com &middot; producer@lokaltrust.com &middot; distributor@lokaltrust.com &middot; retailer@lokaltrust.com</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>
