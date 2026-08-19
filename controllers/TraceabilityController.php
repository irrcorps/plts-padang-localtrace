<?php

class TraceabilityController
{
    private TraceabilityLog $logModel;
    private Product $productModel;

    private const ACTIVITY_BY_ROLE = [
        'distributor' => 'Distribution',
        'retailer'    => 'Retail Receiving',
    ];

    public function __construct()
    {
        $this->logModel = new TraceabilityLog();
        $this->productModel = new Product();
    }

    public function store(): void
    {
        Auth::requireRole(['distributor', 'retailer']);

        $user = Auth::user();
        $productId = (int) ($_POST['product_id'] ?? 0);
        $location = trim($_POST['location'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

        $activity = self::ACTIVITY_BY_ROLE[$user['role']];

        $product = $this->productModel->findById($productId);
        if (!$product) {
            flash('error', 'Produk tidak ditemukan.');
            redirect('products.php');
        }

        if ($product['status'] === 'draft') {
            flash('error', 'Produk masih berstatus draft dan belum diajukan oleh producer.');
            redirect('products.php?action=show&id=' . $productId);
        }

        if ($location === '') {
            flash('error', 'Lokasi wajib diisi.');
            redirect('products.php?action=show&id=' . $productId);
        }

        $this->logModel->create([
            'product_id' => $productId,
            'actor_id'   => $user['id'],
            'actor'      => $user['company_name'] ?: $user['name'],
            'activity'   => $activity,
            'location'   => $location,
            'notes'      => $notes ?: null,
            'timestamp'  => date('Y-m-d H:i:s'),
        ]);

        flash('success', 'Aktivitas "' . $activity . '" berhasil dicatat pada traceability log.');
        redirect('products.php?action=show&id=' . $productId);
    }
}
