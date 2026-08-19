<?php
$pageTitle = $product['product_name'];
$role = Auth::role();
$isOwner = $role === 'producer' && (int) $product['producer_id'] === (int) Auth::user()['id'];
$isAdmin = $role === 'admin';
$isDistributor = $role === 'distributor';
$isRetailer = $role === 'retailer';
$canEdit = $isOwner && in_array($product['status'], ['draft', 'rejected'], true);
$canLogActivity = ($isDistributor || $isRetailer) && $product['status'] !== 'draft';
$activityLabel = $isDistributor ? 'Distribusi' : 'Penerimaan Retail';

$logs = (new TraceabilityLog())->allByProduct($product['id']);
$certificate = $product['status'] === 'certified' ? (new Certificate())->findByProductId($product['id']) : null;

if ($isAdmin) {
    $certController = new CertificationController();
    [$eligible, $checklist] = $certController->checkEligibility($product);
}

$activityIcon = [
    'Production'            => 'bi-box-seam-fill',
    'Distribution'          => 'bi-truck-front-fill',
    'Retail Receiving'      => 'bi-shop-window',
    'Consumer Verification' => 'bi-patch-check-fill',
];
$activityClass = [
    'Production'            => 'timeline-icon-production',
    'Distribution'          => 'timeline-icon-distribution',
    'Retail Receiving'      => 'timeline-icon-retail',
    'Consumer Verification' => 'timeline-icon-consumer',
];

require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/navbar.php';
?>
<main class="dashboard-page">
    <div class="container py-5">
        <a href="<?= BASE_URL ?>/products.php" class="back-link mb-3 d-inline-block">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar Produk
        </a>

        <?php if ($msg = getFlash('success')): ?>
            <div class="alert alert-success py-2 small"><?= e($msg) ?></div>
        <?php endif; ?>
        <?php if ($msg = getFlash('error')): ?>
            <div class="alert alert-danger py-2 small"><?= e($msg) ?></div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="plts-panel p-3 text-center">
                    <?php if ($product['photo']): ?>
                        <img src="<?= BASE_URL ?>/uploads/products/<?= e($product['photo']) ?>" class="product-detail-photo" alt="">
                    <?php else: ?>
                        <div class="product-detail-photo product-thumb-placeholder d-flex align-items-center justify-content-center">
                            <i class="bi bi-image fs-1"></i>
                        </div>
                    <?php endif; ?>
                    <div class="mt-3">
                        <span class="<?= statusBadgeClass($product['status']) ?> fs-6"><?= statusLabel($product['status']) ?></span>
                    </div>
                    <code class="d-block mt-2 text-muted-plts small"><?= e($product['product_code']) ?></code>
                </div>

                <?php if ($canEdit): ?>
                    <div class="d-grid gap-2 mt-3">
                        <a href="<?= BASE_URL ?>/products.php?action=edit&id=<?= $product['id'] ?>" class="btn btn-plts-outline">
                            <i class="bi bi-pencil me-1"></i>Edit Produk
                        </a>
                        <form method="POST" action="<?= BASE_URL ?>/products.php">
                            <input type="hidden" name="action" value="submit">
                            <input type="hidden" name="id" value="<?= $product['id'] ?>">
                            <button type="submit" class="btn btn-plts-primary w-100" <?= empty($product['photo']) ? 'disabled title="Unggah foto terlebih dahulu"' : '' ?>>
                                <i class="bi bi-send-check me-1"></i>Ajukan Sertifikasi
                            </button>
                        </form>
                        <form method="POST" action="<?= BASE_URL ?>/products.php" onsubmit="return confirm('Hapus produk ini?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $product['id'] ?>">
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-trash me-1"></i>Hapus Produk
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-8">
                <?php if ($product['status'] === 'rejected' && !empty($product['admin_notes'])): ?>
                    <div class="alert alert-danger small mb-4">
                        <i class="bi bi-x-circle me-1"></i>
                        <strong>Alasan Penolakan:</strong> <?= e($product['admin_notes']) ?>
                    </div>
                <?php endif; ?>

                <div class="plts-panel p-4">
                    <h3 class="fw-bold mb-1"><?= e($product['product_name']) ?></h3>
                    <p class="text-muted-plts mb-4"><?= e($product['category']) ?> &middot; <?= e($product['origin_location']) ?></p>

                    <p class="mb-4"><?= nl2br(e($product['description'])) ?></p>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="detail-item">
                                <span class="detail-label">Tanggal Produksi</span>
                                <span class="detail-value"><?= formatDateID($product['production_date']) ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-item">
                                <span class="detail-label">Lokasi Asal</span>
                                <span class="detail-value"><?= e($product['origin_location']) ?></span>
                            </div>
                        </div>
                        <?php if (!$isOwner): ?>
                        <div class="col-sm-6">
                            <div class="detail-item">
                                <span class="detail-label">Producer</span>
                                <span class="detail-value"><?= e($product['producer_company'] ?: $product['producer_name']) ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="col-sm-6">
                            <div class="detail-item">
                                <span class="detail-label">Terakhir Diperbarui</span>
                                <span class="detail-value"><?= formatDateID($product['updated_at']) ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="plts-panel p-4 mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-1"></i>Traceability Timeline</h6>
                        <span class="text-muted-plts small"><?= count($logs) ?> aktivitas tercatat</span>
                    </div>

                    <?php if ($isDistributor || $isRetailer): ?>
                        <?php if ($canLogActivity): ?>
                            <form method="POST" action="<?= BASE_URL ?>/traceability.php" class="log-activity-form mb-4">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <div class="row g-2">
                                    <div class="col-md-5">
                                        <input type="text" name="location" class="form-control form-control-plts form-control-sm"
                                               placeholder="Lokasi <?= e($activityLabel) ?>" required>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" name="notes" class="form-control form-control-plts form-control-sm"
                                               placeholder="Catatan (opsional)">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-plts-primary btn-sm w-100">
                                            <i class="bi bi-plus-lg me-1"></i>Catat <?= e($activityLabel) ?>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-warning small mb-4">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Produk masih berstatus <strong>Draft</strong>. Aktivitas <?= e($activityLabel) ?> baru dapat
                                dicatat setelah producer mengajukan produk ini.
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (empty($logs)): ?>
                        <p class="text-muted-plts small mb-0">Belum ada aktivitas tercatat untuk produk ini.</p>
                    <?php else: ?>
                        <div class="plts-timeline">
                            <?php foreach ($logs as $log): ?>
                                <div class="timeline-item">
                                    <div class="timeline-marker <?= $activityClass[$log['activity']] ?? '' ?>">
                                        <i class="bi <?= $activityIcon[$log['activity']] ?? 'bi-circle-fill' ?>"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                                            <div>
                                                <span class="fw-bold"><?= e($log['activity']) ?></span>
                                                <span class="text-muted-plts small d-block"><?= e($log['actor']) ?> &middot; <?= e($log['location']) ?></span>
                                            </div>
                                            <span class="text-muted-plts small text-nowrap"><?= date('d M Y, H:i', strtotime($log['timestamp'])) ?></span>
                                        </div>
                                        <?php if (!empty($log['notes'])): ?>
                                            <p class="small mb-2 mt-2"><?= e($log['notes']) ?></p>
                                        <?php endif; ?>
                                        <div class="hash-chip" title="<?= e($log['data_hash']) ?>">
                                            <i class="bi bi-link-45deg"></i> <?= e(substr($log['data_hash'], 0, 16)) ?>&hellip;
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($isAdmin && $product['status'] === 'submitted'): ?>
                    <div class="plts-panel p-4 mt-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-clipboard-check me-1"></i>Tinjauan Sertifikasi &mdash; Smart Contract Simulation</h6>
                        <p class="text-muted-plts small mb-3">Kondisi <code>IF</code> berikut dievaluasi otomatis oleh sistem sebelum admin dapat memverifikasi produk:</p>
                        <ul class="checklist mb-4">
                            <?php foreach ($checklist as $label => $passed): ?>
                                <li class="<?= $passed ? 'checklist-ok' : 'checklist-fail' ?>">
                                    <i class="bi <?= $passed ? 'bi-check-circle-fill' : 'bi-x-circle-fill' ?>"></i> <?= e($label) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="d-flex flex-wrap gap-3">
                            <form method="POST" action="<?= BASE_URL ?>/certification.php">
                                <input type="hidden" name="action" value="verify">
                                <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                <button type="submit" class="btn btn-plts-primary" <?= $eligible ? '' : 'disabled' ?>>
                                    <i class="bi bi-check-lg me-1"></i>Verifikasi Produk
                                </button>
                            </form>
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="collapse" data-bs-target="#rejectForm">
                                <i class="bi bi-x-lg me-1"></i>Tolak Produk
                            </button>
                        </div>
                        <div class="collapse mt-3" id="rejectForm">
                            <form method="POST" action="<?= BASE_URL ?>/certification.php" class="log-activity-form">
                                <input type="hidden" name="action" value="reject">
                                <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                <label class="form-label small">Alasan Penolakan</label>
                                <textarea name="reason" rows="2" class="form-control form-control-plts mb-2" required></textarea>
                                <button type="submit" class="btn btn-outline-danger btn-sm">Kirim Penolakan</button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($isAdmin && $product['status'] === 'verified'): ?>
                    <div class="plts-panel p-4 mt-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-file-earmark-lock2 me-1"></i>Smart Contract &mdash; Penerbitan Sertifikat</h6>
                        <div class="smart-contract-box mb-3">
                            <div class="small mb-1"><code>IF</code> produk_lengkap = true <code>AND</code> dokumen_valid = true <code>AND</code> diverifikasi_admin = true</div>
                            <div class="small text-teal"><code>THEN</code> generate_certificate() &amp; append_to_ledger()</div>
                        </div>
                        <form method="POST" action="<?= BASE_URL ?>/certification.php">
                            <input type="hidden" name="action" value="certify">
                            <input type="hidden" name="id" value="<?= $product['id'] ?>">
                            <button type="submit" class="btn btn-plts-primary">
                                <i class="bi bi-patch-check-fill me-1"></i>Terbitkan Sertifikat Digital
                            </button>
                        </form>
                    </div>
                <?php endif; ?>

                <?php if ($certificate): ?>
                    <?php $verifyUrl = absoluteUrl('verify.php?code=' . urlencode($certificate['certificate_number'])); ?>
                    <div class="plts-panel p-4 mt-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-patch-check-fill me-1"></i>Sertifikat Digital</h6>
                        <div class="row g-4 align-items-center">
                            <div class="col-md-8">
                                <div class="cert-row"><span>No. Sertifikat</span><strong><?= e($certificate['certificate_number']) ?></strong></div>
                                <div class="cert-row"><span>Penerbit</span><strong><?= e($certificate['issuer']) ?></strong></div>
                                <div class="cert-row"><span>Tanggal Terbit</span><strong><?= formatDateID($certificate['issued_date']) ?></strong></div>
                                <div class="cert-row"><span>Status</span><strong class="text-capitalize"><?= e($certificate['status']) ?></strong></div>
                                <div class="cert-row"><span>Transaction Hash</span><code class="small" title="<?= e($certificate['certificate_hash']) ?>"><?= shortHash($certificate['certificate_hash'], 24) ?></code></div>
                                <a href="<?= BASE_URL ?>/verify.php?code=<?= urlencode($certificate['certificate_number']) ?>" target="_blank" class="link-plts small d-inline-block mt-2">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>Buka Halaman Verifikasi Publik
                                </a>
                            </div>
                            <div class="col-md-4 text-center">
                                <canvas id="qrCanvas" class="qr-canvas"></canvas>
                                <div class="text-muted-plts small mt-2">Scan untuk verifikasi publik</div>
                            </div>
                        </div>
                    </div>
                    <script src="https://cdn.jsdelivr.net/npm/qrcode@1/build/qrcode.min.js"></script>
                    <script>
                        QRCode.toCanvas(document.getElementById('qrCanvas'), <?= json_encode($verifyUrl) ?>, { width: 160, margin: 1 }, function (err) {
                            if (err) console.error(err);
                        });
                    </script>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
