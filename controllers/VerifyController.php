<?php

class VerifyController
{
    private Certificate $certificateModel;
    private Product $productModel;
    private TraceabilityLog $logModel;
    private BlockchainBlock $blockModel;

    public function __construct()
    {
        $this->certificateModel = new Certificate();
        $this->productModel = new Product();
        $this->logModel = new TraceabilityLog();
        $this->blockModel = new BlockchainBlock();
    }

    public function show(): void
    {
        $code = trim($_GET['code'] ?? '');
        $result = null;
        $notFound = false;

        if ($code !== '') {
            $result = $this->resolve($code);
            $notFound = $result === null;
        }

        require __DIR__ . '/../views/verify.php';
    }

    private function resolve(string $code): ?array
    {
        $certificate = $this->certificateModel->findByCertificateNumber($code);

        if ($certificate) {
            $productId = (int) $certificate['product_id'];
            $this->recordConsumerVerification($productId);
            $logs = $this->logModel->allByProduct($productId);
            $blocks = $this->blockModel->byProduct($productId);

            return [
                'type'        => 'certificate',
                'certificate' => $certificate,
                'logs'        => $logs,
                'blocks'      => $blocks,
            ];
        }

        $product = $this->productModel->findByCode($code);
        if ($product) {
            $logs = $this->logModel->allByProduct((int) $product['id']);

            return [
                'type'    => 'product',
                'product' => $product,
                'logs'    => $logs,
            ];
        }

        return null;
    }

    private function recordConsumerVerification(int $productId): void
    {
        $last = $this->logModel->getLastLog($productId);

        if ($last && $last['activity'] === 'Consumer Verification' && strtotime($last['timestamp']) > time() - 300) {
            return;
        }

        $this->logModel->create([
            'product_id' => $productId,
            'actor_id'   => null,
            'actor'      => 'Konsumen (Publik)',
            'activity'   => 'Consumer Verification',
            'location'   => 'Online Verification',
            'notes'      => 'Produk diverifikasi publik melalui QR Code / pencarian sertifikat.',
            'timestamp'  => date('Y-m-d H:i:s'),
        ]);
    }
}
