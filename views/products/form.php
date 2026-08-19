<?php
$isEdit = $product !== null;
$pageTitle = $isEdit ? 'Edit Produk' : 'Tambah Produk';
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/navbar.php';
?>
<main class="dashboard-page">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <a href="<?= BASE_URL ?>/products.php" class="back-link mb-3 d-inline-block">
                    <i class="bi bi-arrow-left me-1"></i>Kembali ke Produk Saya
                </a>

                <div class="plts-panel p-4 p-md-5">
                    <div class="mb-4">
                        <span class="badge-pill mb-2"><i class="bi bi-box-seam me-1"></i>Product Management</span>
                        <h3 class="fw-bold mb-0"><?= $isEdit ? 'Edit Produk' : 'Tambah Produk Baru' ?></h3>
                    </div>

                    <?php if ($msg = getFlash('error')): ?>
                        <div class="alert alert-danger py-2 small"><?= e($msg) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="<?= BASE_URL ?>/products.php" enctype="multipart/form-data" novalidate>
                        <input type="hidden" name="action" value="<?= $isEdit ? 'update' : 'store' ?>">
                        <?php if ($isEdit): ?>
                            <input type="hidden" name="id" value="<?= $product['id'] ?>">
                        <?php endif; ?>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Produk</label>
                                <input type="text" name="product_name" class="form-control form-control-plts"
                                       value="<?= e($product['product_name'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kategori</label>
                                <select name="category" class="form-select form-control-plts" required>
                                    <option value="" disabled <?= empty($product) ? 'selected' : '' ?>>Pilih kategori</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= e($cat) ?>" <?= (($product['category'] ?? '') === $cat) ? 'selected' : '' ?>><?= e($cat) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="description" rows="3" class="form-control form-control-plts" required><?= e($product['description'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Produksi</label>
                                <input type="date" name="production_date" class="form-control form-control-plts"
                                       value="<?= e($product['production_date'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Lokasi Asal</label>
                                <input type="text" name="origin_location" class="form-control form-control-plts"
                                       placeholder="cth. Padang, Sumatera Barat"
                                       value="<?= e($product['origin_location'] ?? '') ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Foto Produk <?= $isEdit ? '(kosongkan jika tidak diganti)' : '' ?></label>
                                <input type="file" name="photo" class="form-control form-control-plts" accept=".jpg,.jpeg,.png,.webp">
                                <div class="form-text">Format JPG/PNG/WEBP, maksimal 2MB.</div>
                                <?php if ($isEdit && $product['photo']): ?>
                                    <img src="<?= BASE_URL ?>/uploads/products/<?= e($product['photo']) ?>" class="photo-preview mt-3" alt="">
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="d-flex gap-3 mt-4">
                            <button type="submit" class="btn btn-plts-primary px-4">
                                <i class="bi bi-check-lg me-1"></i><?= $isEdit ? 'Simpan Perubahan' : 'Simpan sebagai Draft' ?>
                            </button>
                            <a href="<?= BASE_URL ?>/products.php" class="btn btn-plts-outline px-4">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
