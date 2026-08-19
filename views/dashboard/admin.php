<?php
$pageTitle = 'Dashboard Admin';
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/navbar.php';

$productModel = new Product();
$userModel = new User();
$logModel = new TraceabilityLog();
$blockModel = new BlockchainBlock();

$totalUsers = $userModel->countAll();
$totalProducts = $productModel->countAll();
$certifiedCount = $productModel->countByStatus('certified');
$submittedCount = $productModel->countByStatus('submitted');
$totalTransactions = $blockModel->countAll();

$statusCounts = $productModel->countGroupedByStatus();
$activityCounts = $logModel->countGroupedByActivity();

$recentProducts = array_slice($productModel->all(), 0, 6);
$ledgerBlocks = $blockModel->latest(20);
?>
<main class="dashboard-page">
    <div class="container py-5">
        <?php if ($msg = getFlash('success')): ?>
            <div class="alert alert-success py-2 small"><?= e($msg) ?></div>
        <?php endif; ?>

        <div class="dashboard-welcome mb-4">
            <span class="badge-pill mb-2"><i class="bi bi-shield-lock me-1"></i>Administrator</span>
            <h3 class="fw-bold mb-1">Halo, <?= e(Auth::user()['name']) ?> 👋</h3>
            <p class="text-muted-plts mb-0">Ringkasan sistem &amp; dashboard analitik Padang LocalTrace System (PLTS).</p>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-card-icon stat-card-icon-blue"><i class="bi bi-people-fill"></i></div>
                    <div class="stat-card-number"><?= $totalUsers ?></div>
                    <div class="stat-card-label">Total Users</div>
                </div>
            </div>
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
                    <div class="stat-card-icon stat-card-icon-gray"><i class="bi bi-link-45deg"></i></div>
                    <div class="stat-card-number"><?= $totalTransactions ?></div>
                    <div class="stat-card-label">Transactions (Ledger Blocks)</div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-lg-6">
                <div class="plts-panel p-4 h-100">
                    <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart me-1"></i>Statistik Sertifikasi Produk</h6>
                    <div class="chart-box">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="plts-panel p-4 h-100">
                    <h6 class="fw-bold mb-3"><i class="bi bi-bar-chart me-1"></i>Aktivitas Traceability</h6>
                    <div class="chart-box">
                        <canvas id="activityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="plts-panel mb-4">
            <div class="d-flex justify-content-between align-items-center p-3 pb-0">
                <h6 class="fw-bold mb-0">Produk Terbaru dari Seluruh Producer</h6>
                <a href="<?= BASE_URL ?>/products.php" class="link-plts small">Lihat semua &rarr;</a>
            </div>
            <div class="table-responsive">
                <table class="table plts-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Producer</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentProducts)): ?>
                            <tr><td colspan="5" class="text-center text-muted-plts py-4">Belum ada produk terdaftar.</td></tr>
                        <?php else: ?>
                            <?php foreach ($recentProducts as $product): ?>
                                <tr>
                                    <td class="fw-semibold"><?= e($product['product_name']) ?></td>
                                    <td class="small"><?= e($product['producer_company'] ?: $product['producer_name']) ?></td>
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

        <div class="plts-panel">
            <div class="d-flex justify-content-between align-items-center p-3 pb-0">
                <h6 class="fw-bold mb-0"><i class="bi bi-diagram-3 me-1"></i>Blockchain Ledger Simulation</h6>
                <span class="text-muted-plts small">Menampilkan <?= count($ledgerBlocks) ?> block terbaru dari <?= $totalTransactions ?> total block</span>
            </div>
            <div class="table-responsive">
                <table class="table plts-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Block ID</th>
                            <th>Hash</th>
                            <th>Produk</th>
                            <th>Aktor</th>
                            <th>Timestamp</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ledgerBlocks)): ?>
                            <tr><td colspan="6" class="text-center text-muted-plts py-4">Belum ada block tercatat.</td></tr>
                        <?php else: ?>
                            <?php foreach ($ledgerBlocks as $block): ?>
                                <tr>
                                    <td>#<?= (int) $block['block_index'] ?></td>
                                    <td><code class="small" title="<?= e($block['block_hash']) ?>"><?= shortHash($block['block_hash'], 14) ?></code></td>
                                    <td class="small"><?= e($block['product_name'] ?? '-') ?></td>
                                    <td class="small"><?= e($block['actor']) ?></td>
                                    <td class="small"><?= date('d M Y, H:i', strtotime($block['created_at'])) ?></td>
                                    <td><span class="badge-status badge-status-certified"><i class="bi bi-check-circle-fill me-1"></i>Valid</span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
    Chart.defaults.font.family = "'Plus Jakarta Sans', system-ui, sans-serif";
    Chart.defaults.color = '#64748b';

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Draft', 'Submitted', 'Verified', 'Certified', 'Rejected'],
            datasets: [{
                data: <?= json_encode(array_values($statusCounts)) ?>,
                backgroundColor: ['#94a3b8', '#3b82f6', '#8b5cf6', '#14b8a6', '#ef4444'],
                borderWidth: 0,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, padding: 14 } } },
            cutout: '65%',
        },
    });

    new Chart(document.getElementById('activityChart'), {
        type: 'bar',
        data: {
            labels: ['Production', 'Distribution', 'Retail Receiving', 'Consumer Verification'],
            datasets: [{
                label: 'Jumlah Aktivitas',
                data: <?= json_encode(array_values($activityCounts)) ?>,
                backgroundColor: ['#14b8a6', '#3b82f6', '#7c3aed', '#f6ad55'],
                borderRadius: 6,
                maxBarThickness: 48,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#e2e8f0' } },
                x: { grid: { display: false } },
            },
        },
    });
</script>

<?php require __DIR__ . '/../partials/footer.php'; ?>
