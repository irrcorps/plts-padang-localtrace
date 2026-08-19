<?php $currentUser = Auth::user(); ?>
<nav class="navbar navbar-expand-lg plts-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= BASE_URL ?>/index.php">
            <span class="brand-mark"><i class="bi bi-link-45deg"></i></span>
            <span class="brand-text">
                <span class="brand-title">LokalTrust</span>
                <span class="brand-sub">Blockchain Traceability Platform</span>
            </span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/index.php#alur">Alur Sistem</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/index.php#penelitian">Penelitian</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/verify.php"><i class="bi bi-qr-code-scan me-1"></i>Verify Product</a></li>
                <?php if ($currentUser): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/dashboard.php"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/products.php"><i class="bi bi-box-seam me-1"></i>Produk</a></li>
                    <li class="nav-item">
                        <a class="btn btn-plts-outline btn-sm ms-lg-2" href="<?= BASE_URL ?>/logout.php">
                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/login.php">Login</a></li>
                    <li class="nav-item">
                        <a class="btn btn-plts-primary btn-sm ms-lg-2" href="<?= BASE_URL ?>/register.php">
                            Register <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
