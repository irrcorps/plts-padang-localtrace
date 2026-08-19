<?php
$pageTitle = 'Verify Product';
require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/navbar.php';

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
?>
<main class="dashboard-page">
    <div class="container py-5">
        <div class="text-center mb-4">
            <span class="badge-pill mb-2"><i class="bi bi-qr-code-scan me-1"></i>Public Verification</span>
            <h3 class="fw-bold mb-1">Verifikasi Produk &amp; Sertifikat</h3>
            <p class="text-muted-plts mb-0">Masukkan nomor sertifikat atau kode produk untuk melihat riwayat rantai pasok dan status sertifikasi.</p>
        </div>

        <div class="row justify-content-center mb-4">
            <div class="col-lg-7">
                <form method="GET" action="<?= BASE_URL ?>/verify.php" class="d-flex gap-2">
                    <input type="text" name="code" class="form-control form-control-plts" placeholder="cth. CERT-LOKALTRUST-2026-0001 atau PROD-001"
                           value="<?= e($code) ?>" required>
                    <button type="submit" class="btn btn-plts-primary px-4">
                        <i class="bi bi-search me-1"></i>Cek
                    </button>
                </form>
            </div>
        </div>

        <?php if ($notFound): ?>
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="alert alert-danger small">
                        <i class="bi bi-x-circle me-1"></i>
                        Kode <strong><?= e($code) ?></strong> tidak ditemukan. Periksa kembali nomor sertifikat atau kode produk.
                    </div>
                </div>
            </div>
        <?php elseif ($result === null): ?>
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="plts-panel p-4 text-center">
                        <i class="bi bi-qr-code fs-1 text-muted-plts mb-2 d-block"></i>
                        <p class="text-muted-plts small mb-0">
                            Pindai QR code pada kemasan produk, atau masukkan kode secara manual.
                            Contoh kode demo: <code>CERT-LOKALTRUST-2026-0001</code>
                        </p>
                    </div>
                </div>
            </div>
        <?php elseif ($result['type'] === 'certificate'): ?>
            <?php
                $certificate = $result['certificate'];
                $logs = $result['logs'];
                $blocks = $result['blocks'];
            ?>
            <div class="row g-4 justify-content-center">
                <div class="col-lg-9">
                    <div class="plts-panel p-4 mb-4">
                        <div class="row g-4">
                            <div class="col-md-4 text-center">
                                <?php if ($certificate['photo']): ?>
                                    <img src="<?= BASE_URL ?>/uploads/products/<?= e($certificate['photo']) ?>" class="product-detail-photo" alt="">
                                <?php else: ?>
                                    <div class="product-detail-photo product-thumb-placeholder d-flex align-items-center justify-content-center">
                                        <i class="bi bi-image fs-1"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="mt-3">
                                    <span class="badge-status badge-status-<?= $certificate['status'] === 'active' ? 'certified' : 'rejected' ?> fs-6">
                                        <?= $certificate['status'] === 'active' ? 'Sertifikat Aktif' : 'Sertifikat Dicabut' ?>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h3 class="fw-bold mb-1"><?= e($certificate['product_name']) ?></h3>
                                <p class="text-muted-plts mb-3"><?= e($certificate['category']) ?> &middot; <?= e($certificate['origin_location']) ?></p>
                                <div class="cert-row"><span>Producer</span><strong><?= e($certificate['producer_company'] ?: $certificate['producer_name']) ?></strong></div>
                                <div class="cert-row"><span>Tanggal Produksi</span><strong><?= formatDateID($certificate['production_date']) ?></strong></div>
                                <div class="cert-row"><span>No. Sertifikat</span><strong><?= e($certificate['certificate_number']) ?></strong></div>
                                <div class="cert-row"><span>Penerbit</span><strong><?= e($certificate['issuer']) ?></strong></div>
                                <div class="cert-row"><span>Tanggal Terbit</span><strong><?= formatDateID($certificate['issued_date']) ?></strong></div>
                                <div class="cert-row"><span>Transaction Hash</span><code class="small" title="<?= e($certificate['certificate_hash']) ?>"><?= shortHash($certificate['certificate_hash'], 28) ?></code></div>
                            </div>
                        </div>
                    </div>

                    <div class="plts-panel p-4 mb-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-diagram-3 me-1"></i>Blockchain Verification Simulation</h6>
                        <p class="text-muted-plts small mb-3">
                            Setiap aktivitas produk ini tercatat sebagai block pada ledger simulasi,
                            saling terhubung melalui hash block sebelumnya (hash-linked ledger).
                        </p>
                        <div class="table-responsive">
                            <table class="table plts-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Block</th>
                                        <th>Referensi</th>
                                        <th>Aktor</th>
                                        <th>Hash</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($blocks as $block): ?>
                                        <tr>
                                            <td>#<?= (int) $block['block_index'] ?></td>
                                            <td class="small text-capitalize"><?= e(str_replace('_', ' ', $block['reference_type'])) ?></td>
                                            <td class="small"><?= e($block['actor']) ?></td>
                                            <td><code class="small" title="<?= e($block['block_hash']) ?>"><?= shortHash($block['block_hash'], 14) ?></code></td>
                                            <td><span class="badge-status badge-status-certified"><i class="bi bi-check-circle-fill me-1"></i>Valid</span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="plts-panel p-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-1"></i>Riwayat Rantai Pasok (Supply Chain History)</h6>
                        <?php if (empty($logs)): ?>
                            <p class="text-muted-plts small mb-0">Belum ada aktivitas tercatat.</p>
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
                                            <div class="hash-chip mt-2" title="<?= e($log['data_hash']) ?>">
                                                <i class="bi bi-link-45deg"></i> <?= shortHash($log['data_hash'], 16) ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php $product = $result['product']; $logs = $result['logs']; ?>
            <div class="row g-4 justify-content-center">
                <div class="col-lg-8">
                    <div class="alert alert-warning small mb-4">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Produk ini <strong>belum memiliki sertifikat digital resmi</strong>. Status saat ini:
                        <span class="<?= statusBadgeClass($product['status']) ?>"><?= statusLabel($product['status']) ?></span>
                    </div>
                    <div class="plts-panel p-4">
                        <div class="row g-4">
                            <div class="col-md-4 text-center">
                                <?php if ($product['photo']): ?>
                                    <img src="<?= BASE_URL ?>/uploads/products/<?= e($product['photo']) ?>" class="product-detail-photo" alt="">
                                <?php else: ?>
                                    <div class="product-detail-photo product-thumb-placeholder d-flex align-items-center justify-content-center">
                                        <i class="bi bi-image fs-1"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-8">
                                <h3 class="fw-bold mb-1"><?= e($product['product_name']) ?></h3>
                                <p class="text-muted-plts mb-3"><?= e($product['category']) ?> &middot; <?= e($product['origin_location']) ?></p>
                                <div class="cert-row"><span>Producer</span><strong><?= e($product['producer_company'] ?: $product['producer_name']) ?></strong></div>
                                <div class="cert-row"><span>Tanggal Produksi</span><strong><?= formatDateID($product['production_date']) ?></strong></div>
                                <div class="cert-row"><span>Kode Produk</span><strong><?= e($product['product_code']) ?></strong></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
