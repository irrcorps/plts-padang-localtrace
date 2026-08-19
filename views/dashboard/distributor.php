<?php
$pageTitle = 'Dashboard Distributor';
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/navbar.php';

$productModel = new Product();
$logModel = new TraceabilityLog();
$user = Auth::user();

$totalActivities = $logModel->countByActor($user['id']);
$totalProducts = $productModel->countAll();
$certifiedCount = $productModel->countByStatus('certified');
$recentActivities = $logModel->recentByActor($user['id'], 5);
?>
<main class="dashboard-page">
    <div class="container py-5">
        <?php if ($msg = getFlash('success')): ?>
            <div class="alert alert-success py-2 small"><?= e($msg) ?></div>
        <?php endif; ?>
        <?php if ($msg = getFlash('error')): ?>
            <div class="alert alert-danger py-2 small"><?= e($msg) ?></div>
        <?php endif; ?>

        <div class="dashboard-welcome mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <span class="badge-pill mb-2"><i class="bi bi-truck me-1"></i>Distributor</span>
                <h3 class="fw-bold mb-1">Halo, <?= e($user['name']) ?> 👋</h3>
                <p class="text-muted-plts mb-0">Catat aktivitas distribusi produk pada traceability log.</p>
            </div>
            <a href="<?= BASE_URL ?>/products.php" class="btn btn-plts-primary">
                <i class="bi bi-box-seam me-1"></i>Lihat Produk
            </a>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-card-icon stat-card-icon-blue"><i class="bi bi-truck-front-fill"></i></div>
                    <div class="stat-card-number"><?= $totalActivities ?></div>
                    <div class="stat-card-label">Aktivitas Tercatat</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-card-icon"><i class="bi bi-boxes"></i></div>
                    <div class="stat-card-number"><?= $totalProducts ?></div>
                    <div class="stat-card-label">Total Produk Sistem</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-card-icon stat-card-icon-gold"><i class="bi bi-patch-check-fill"></i></div>
                    <div class="stat-card-number"><?= $certifiedCount ?></div>
                    <div class="stat-card-label">Produk Certified</div>
                </div>
            </div>
        </div>

        <div class="plts-panel">
            <div class="d-flex justify-content-between align-items-center p-3 pb-0">
                <h6 class="fw-bold mb-0">Aktivitas Distribusi Terbaru</h6>
                <a href="<?= BASE_URL ?>/products.php" class="link-plts small">Lihat semua produk &rarr;</a>
            </div>
            <div class="table-responsive">
                <table class="table plts-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Lokasi</th>
                            <th>Waktu</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentActivities)): ?>
                            <tr><td colspan="4" class="text-center text-muted-plts py-4">Belum ada aktivitas distribusi yang dicatat.</td></tr>
                        <?php else: ?>
                            <?php foreach ($recentActivities as $log): ?>
                                <tr>
                                    <td class="fw-semibold"><?= e($log['product_name']) ?></td>
                                    <td class="small"><?= e($log['location']) ?></td>
                                    <td class="small"><?= date('d M Y, H:i', strtotime($log['timestamp'])) ?></td>
                                    <td class="text-end">
                                        <a href="<?= BASE_URL ?>/products.php?action=show&id=<?= $log['product_id'] ?>" class="btn btn-sm btn-plts-outline">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
