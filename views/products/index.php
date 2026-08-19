<?php
$pageTitle = 'Manajemen Produk';
$role = Auth::role();
$isProducer = $role === 'producer';
$showProducerColumn = !$isProducer;
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/navbar.php';
?>
<main class="dashboard-page">
    <div class="container py-5">
        <?php if ($msg = getFlash('success')): ?>
            <div class="alert alert-success py-2 small"><?= e($msg) ?></div>
        <?php endif; ?>
        <?php if ($msg = getFlash('error')): ?>
            <div class="alert alert-danger py-2 small"><?= e($msg) ?></div>
        <?php endif; ?>

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <span class="badge-pill mb-2"><i class="bi bi-box-seam me-1"></i>Product Management</span>
                <h3 class="fw-bold mb-0"><?= $isProducer ? 'Produk Saya' : 'Semua Produk Terdaftar' ?></h3>
            </div>
            <?php if ($isProducer): ?>
                <a href="<?= BASE_URL ?>/products.php?action=create" class="btn btn-plts-primary">
                    <i class="bi bi-plus-lg me-1"></i>Tambah Produk
                </a>
            <?php endif; ?>
        </div>

        <div class="plts-panel">
            <div class="table-responsive">
                <table class="table plts-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Kode</th>
                            <th>Kategori</th>
                            <?php if ($showProducerColumn): ?><th>Producer</th><?php endif; ?>
                            <th>Tgl Produksi</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="<?= $showProducerColumn ? 7 : 6 ?>" class="text-center text-muted-plts py-4">
                                    Belum ada produk<?= $isProducer ? '. Silakan tambah produk baru.' : ' terdaftar.' ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <?php if ($product['photo']): ?>
                                                <img src="<?= BASE_URL ?>/uploads/products/<?= e($product['photo']) ?>" class="product-thumb" alt="">
                                            <?php else: ?>
                                                <span class="product-thumb product-thumb-placeholder"><i class="bi bi-image"></i></span>
                                            <?php endif; ?>
                                            <span class="fw-semibold"><?= e($product['product_name']) ?></span>
                                        </div>
                                    </td>
                                    <td><code class="small"><?= e($product['product_code']) ?></code></td>
                                    <td><?= e($product['category']) ?></td>
                                    <?php if ($showProducerColumn): ?>
                                        <td class="small"><?= e($product['producer_company'] ?: $product['producer_name']) ?></td>
                                    <?php endif; ?>
                                    <td class="small"><?= formatDateID($product['production_date']) ?></td>
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
