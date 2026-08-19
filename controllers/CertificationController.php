<?php

class CertificationController
{
    private Product $productModel;
    private Certificate $certificateModel;
    private TraceabilityLog $logModel;
    private BlockchainBlock $blockModel;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->certificateModel = new Certificate();
        $this->logModel = new TraceabilityLog();
        $this->blockModel = new BlockchainBlock();
    }

    /**
     * Simulasi kondisi IF pada smart contract sertifikasi.
     */
    public function checkEligibility(array $product): array
    {
        $checklist = [
            'Nama produk terisi'      => $product['product_name'] !== '',
            'Kategori valid'          => $product['category'] !== '',
            'Deskripsi lengkap'       => $product['description'] !== '',
            'Tanggal produksi valid'  => !empty($product['production_date']),
            'Lokasi asal terisi'      => $product['origin_location'] !== '',
            'Dokumen foto produk ada' => !empty($product['photo']),
        ];

        $eligible = !in_array(false, $checklist, true);

        return [$eligible, $checklist];
    }

    public function verify(int $id): void
    {
        Auth::requireRole(['admin']);
        $product = $this->findSubmittedProduct($id);

        [$eligible] = $this->checkEligibility($product);
        if (!$eligible) {
            flash('error', 'Produk belum memenuhi seluruh syarat kelengkapan untuk diverifikasi.');
            redirect('products.php?action=show&id=' . $id);
        }

        $this->productModel->updateStatusWithNotes($id, 'verified', null);
        flash('success', 'Produk berhasil diverifikasi. Siap untuk penerbitan sertifikat.');
        redirect('products.php?action=show&id=' . $id);
    }

    public function reject(int $id): void
    {
        Auth::requireRole(['admin']);
        $product = $this->productModel->findById($id);

        if (!$product || !in_array($product['status'], ['submitted', 'verified'], true)) {
            flash('error', 'Produk tidak dapat ditolak pada status saat ini.');
            redirect('products.php');
        }

        $reason = trim($_POST['reason'] ?? '');
        if ($reason === '') {
            flash('error', 'Alasan penolakan wajib diisi.');
            redirect('products.php?action=show&id=' . $id);
        }

        $this->productModel->updateStatusWithNotes($id, 'rejected', $reason);
        flash('success', 'Produk ditolak. Producer dapat memperbaiki dan mengajukan ulang.');
        redirect('products.php?action=show&id=' . $id);
    }

    public function certify(int $id): void
    {
        Auth::requireRole(['admin']);
        $product = $this->productModel->findById($id);

        if (!$product || $product['status'] !== 'verified') {
            flash('error', 'Produk harus berstatus Verified sebelum sertifikat diterbitkan.');
            redirect('products.php?action=show&id=' . $id);
        }

        [$eligible] = $this->checkEligibility($product);
        if (!$eligible) {
            flash('error', 'Smart contract: syarat kelengkapan produk tidak terpenuhi.');
            redirect('products.php?action=show&id=' . $id);
        }

        $lastLog = $this->logModel->getLastLog($id);
        $chainRef = $lastLog['data_hash'] ?? hash('sha256', 'genesis-' . $id);

        $certificateNumber = $this->certificateModel->generateCertificateNumber();
        $issuedDate = date('Y-m-d');
        $certificateHash = hash('sha256', implode('|', [
            $product['id'], $certificateNumber, $issuedDate, $chainRef, microtime(),
        ]));

        $certificateId = $this->certificateModel->create([
            'product_id'         => $id,
            'certificate_number' => $certificateNumber,
            'issuer'             => 'LokalTrust Certification Authority',
            'issued_date'        => $issuedDate,
            'certificate_hash'   => $certificateHash,
            'status'             => 'active',
        ]);

        $this->productModel->updateStatusWithNotes($id, 'certified', null);

        $this->blockModel->append(
            $id,
            'LokalTrust Certification Authority',
            'certificate',
            $certificateId,
            $certificateHash
        );

        flash('success', 'Sertifikat digital ' . $certificateNumber . ' berhasil diterbitkan (smart contract simulation).');
        redirect('products.php?action=show&id=' . $id);
    }

    private function findSubmittedProduct(int $id): array
    {
        $product = $this->productModel->findById($id);

        if (!$product || $product['status'] !== 'submitted') {
            flash('error', 'Produk harus berstatus Submitted untuk diverifikasi.');
            redirect('products.php?action=show&id=' . $id);
        }

        return $product;
    }
}
