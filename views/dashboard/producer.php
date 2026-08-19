<?php
$pageTitle = 'Dashboard Producer';
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/navbar.php';

$productModel = new Product();
$user = Auth::user();

$totalProducts = $productModel->countByProducer($user['id']);
$certifiedCount = $productModel->countByStatus('certified', $user['id']);
$submittedCount = $productModel->countByStatus('submitted', $user['id']);
$draftCount = $productModel->countByStatus('draft', $user['id']);
$rejectedCount = $productModel->countByStatus('rejected', $user['id']);

$recentProducts = array_slice($productModel->allByProducer($user['id']), 0, 5);
?>
<main class="dashboard-page">
    <div class="container py-5">
        <?php if ($msg = getFlash('success')): ?>
            <div class="alert alert-success py-2 small"><?= e($msg) ?></div>
        <?php endif; ?>

        <div class="dashboard-welcome mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <span class="badge-pill mb-2"><i class="bi bi-box-seam me-1"></i>Producer / UMKM</span>
                <h3 class="fw-bold mb-1">Halo, <?= e($user['name']) ?> 👋</h3>
                <p class="text-muted-plts mb-0">Kelola produk dan pantau status pengajuan sertifikasi Anda.</p>
            </div>
            <a href="<?= BASE_URL ?>/products.php?action=create" class="btn btn-plts-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Produk
            </a>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-card-icon"><i class="bi bi-boxes"></i></div>
                    <div class="stat-card-number"><?= $totalProducts ?></div>
                    <div class="stat-card-label">Total Produk</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-card-icon stat-card-icon-gold"><i class="bi bi-patch-check-fill"></i></div>
                    <div class="stat-card-number"><?= $certifiedCount ?></div>
                    <div class="stat-card-label">Produk Certified</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-card-icon stat-card-icon-blue"><i class="bi bi-send-check"></i></div>
                    <div class="stat-card-number"><?= $submittedCount ?></div>
                    <div class="stat-card-label">Menunggu Verifikasi</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-card-icon stat-card-icon-gray"><i class="bi bi-file-earmark-text"></i></div>
                    <div class="stat-card-number"><?= $draftCount ?></div>
                    <div class="stat-card-label">Draft</div>
                </div>
            </div>
        </div>

        <?php if ($rejectedCount > 0): ?>
            <div class="alert alert-warning small mb-4">
                <i class="bi bi-exclamation-triangle me-1"></i>
                Anda memiliki <strong><?= $rejectedCount ?></strong> produk dengan status <em>Rejected</em>. Silakan periksa dan ajukan ulang.
            </div>
        <?php endif; ?>

        <div class="plts-panel">
            <div class="d-flex justify-content-between align-items-center p-3 pb-0">
                <h6 class="fw-bold mb-0">Produk Terbaru</h6>
                <a href="<?= BASE_URL ?>/products.php" class="link-plts small">Lihat semua &rarr;</a>
            </div>
            <div class="table-responsive">
                <table class="table plts-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentProducts)): ?>
                            <tr><td colspan="4" class="text-center text-muted-plts py-4">Belum ada produk. Silakan tambah produk baru.</td></tr>
                        <?php else: ?>
                            <?php foreach ($recentProducts as $product): ?>
                                <tr>
                                    <td class="fw-semibold"><?= e($product['product_name']) ?></td>
                                    <td><?= e($product['category']) ?></td>
                                    <td><span class="<?= statusBadgeClass($product['status']) ?>"><?= statusLabel($product['status']) ?></span></td>
                                    <td class="text-end">
                                        <a href="<?= BASE_URL ?>/products.php?action=show&id=<?= $product['id'] ?>" class="btn btn-sm btn-plts-outline">
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
