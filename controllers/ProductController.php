<?php

class ProductController
{
    private Product $productModel;
    private TraceabilityLog $logModel;

    private const ALLOWED_CATEGORIES = [
        'Makanan Olahan', 'Makanan Ringan', 'Minuman', 'Kerajinan Tangan', 'Fashion & Tekstil', 'Lainnya',
    ];

    private const ALLOWED_MIME = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    private const MAX_PHOTO_BYTES = 2 * 1024 * 1024; // 2MB

    public function __construct()
    {
        $this->productModel = new Product();
        $this->logModel = new TraceabilityLog();
    }

    public function index(): void
    {
        Auth::requireRole(['producer', 'admin', 'distributor', 'retailer']);

        $user = Auth::user();
        $products = $user['role'] === 'producer'
            ? $this->productModel->allByProducer($user['id'])
            : $this->productModel->all();

        require __DIR__ . '/../views/products/index.php';
    }

    public function showCreateForm(): void
    {
        Auth::requireRole(['producer']);
        $categories = self::ALLOWED_CATEGORIES;
        $product = null;
        require __DIR__ . '/../views/products/form.php';
    }

    public function store(): void
    {
        Auth::requireRole(['producer']);
        $user = Auth::user();

        [$data, $errors] = $this->validate($_POST);

        if (!empty($errors)) {
            flash('error', implode(' ', $errors));
            redirect('products.php?action=create');
        }

        $photo = null;
        [$photo, $uploadError] = $this->handleUpload();
        if ($uploadError) {
            flash('error', $uploadError);
            redirect('products.php?action=create');
        }

        $productId = $this->productModel->create([
            'producer_id'     => $user['id'],
            'product_code'    => $this->productModel->generateProductCode(),
            'product_name'    => $data['product_name'],
            'category'        => $data['category'],
            'description'     => $data['description'],
            'production_date' => $data['production_date'],
            'origin_location' => $data['origin_location'],
            'photo'           => $photo,
            'status'          => 'draft',
        ]);

        $this->logModel->create([
            'product_id' => $productId,
            'actor_id'   => $user['id'],
            'actor'      => $user['company_name'] ?: $user['name'],
            'activity'   => 'Production',
            'location'   => $data['origin_location'],
            'notes'      => 'Produk baru didaftarkan oleh producer ke dalam sistem.',
            'timestamp'  => $data['production_date'] . ' 08:00:00',
        ]);

        flash('success', 'Produk berhasil ditambahkan sebagai draft.');
        redirect('products.php?action=show&id=' . $productId);
    }

    public function showEditForm(int $id): void
    {
        Auth::requireRole(['producer']);
        $product = $this->findOwnedProduct($id);

        if (!in_array($product['status'], ['draft', 'rejected'], true)) {
            flash('error', 'Produk yang sudah diajukan/tersertifikasi tidak dapat diedit.');
            redirect('products.php?action=show&id=' . $id);
        }

        $categories = self::ALLOWED_CATEGORIES;
        require __DIR__ . '/../views/products/form.php';
    }

    public function update(int $id): void
    {
        Auth::requireRole(['producer']);
        $product = $this->findOwnedProduct($id);

        if (!in_array($product['status'], ['draft', 'rejected'], true)) {
            flash('error', 'Produk yang sudah diajukan/tersertifikasi tidak dapat diedit.');
            redirect('products.php?action=show&id=' . $id);
        }

        [$data, $errors] = $this->validate($_POST);

        if (!empty($errors)) {
            flash('error', implode(' ', $errors));
            redirect('products.php?action=edit&id=' . $id);
        }

        [$photo, $uploadError] = $this->handleUpload();
        if ($uploadError) {
            flash('error', $uploadError);
            redirect('products.php?action=edit&id=' . $id);
        }

        if ($photo && $product['photo']) {
            $this->deletePhotoFile($product['photo']);
        }

        $this->productModel->update($id, [
            'product_name'     => $data['product_name'],
            'category'         => $data['category'],
            'description'      => $data['description'],
            'production_date'  => $data['production_date'],
            'origin_location'  => $data['origin_location'],
            'photo'            => $photo,
        ]);

        flash('success', 'Produk berhasil diperbarui.');
        redirect('products.php?action=show&id=' . $id);
    }

    public function show(int $id): void
    {
        Auth::requireRole(['producer', 'admin', 'distributor', 'retailer']);
        $user = Auth::user();
        $product = $this->productModel->findById($id);

        if (!$product) {
            flash('error', 'Produk tidak ditemukan.');
            redirect('products.php');
        }

        if ($user['role'] === 'producer' && (int) $product['producer_id'] !== (int) $user['id']) {
            flash('error', 'Anda tidak memiliki akses ke produk tersebut.');
            redirect('products.php');
        }

        require __DIR__ . '/../views/products/show.php';
    }

    public function delete(int $id): void
    {
        Auth::requireRole(['producer']);
        $product = $this->findOwnedProduct($id);

        if (!in_array($product['status'], ['draft', 'rejected'], true)) {
            flash('error', 'Produk yang sudah diajukan/tersertifikasi tidak dapat dihapus.');
            redirect('products.php');
        }

        if ($product['photo']) {
            $this->deletePhotoFile($product['photo']);
        }

        $this->productModel->delete($id);
        flash('success', 'Produk berhasil dihapus.');
        redirect('products.php');
    }

    public function submit(int $id): void
    {
        Auth::requireRole(['producer']);
        $product = $this->findOwnedProduct($id);

        if (!in_array($product['status'], ['draft', 'rejected'], true)) {
            flash('error', 'Produk ini sudah dalam proses/selesai sertifikasi.');
            redirect('products.php?action=show&id=' . $id);
        }

        if (empty($product['photo'])) {
            flash('error', 'Unggah foto produk terlebih dahulu sebelum mengajukan sertifikasi.');
            redirect('products.php?action=show&id=' . $id);
        }

        $this->productModel->updateStatus($id, 'submitted');
        flash('success', 'Produk berhasil diajukan untuk sertifikasi.');
        redirect('products.php?action=show&id=' . $id);
    }

    private function findOwnedProduct(int $id): array
    {
        $user = Auth::user();
        $product = $this->productModel->findById($id);

        if (!$product || (int) $product['producer_id'] !== (int) $user['id']) {
            flash('error', 'Produk tidak ditemukan.');
            redirect('products.php');
        }

        return $product;
    }

    private function validate(array $input): array
    {
        $data = [
            'product_name'     => trim($input['product_name'] ?? ''),
            'category'         => trim($input['category'] ?? ''),
            'description'      => trim($input['description'] ?? ''),
            'production_date'  => trim($input['production_date'] ?? ''),
            'origin_location'  => trim($input['origin_location'] ?? ''),
        ];

        $errors = [];
        if ($data['product_name'] === '') $errors[] = 'Nama produk wajib diisi.';
        if (!in_array($data['category'], self::ALLOWED_CATEGORIES, true)) $errors[] = 'Kategori tidak valid.';
        if ($data['description'] === '') $errors[] = 'Deskripsi wajib diisi.';
        if ($data['production_date'] === '' || !strtotime($data['production_date'])) $errors[] = 'Tanggal produksi tidak valid.';
        if ($data['origin_location'] === '') $errors[] = 'Lokasi asal wajib diisi.';

        return [$data, $errors];
    }

    private function handleUpload(): array
    {
        if (empty($_FILES['photo']) || $_FILES['photo']['error'] === UPLOAD_ERR_NO_FILE) {
            return [null, null];
        }

        $file = $_FILES['photo'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return [null, 'Gagal mengunggah foto produk.'];
        }

        if ($file['size'] > self::MAX_PHOTO_BYTES) {
            return [null, 'Ukuran foto maksimal 2MB.'];
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!isset(self::ALLOWED_MIME[$mime])) {
            return [null, 'Format foto harus JPG, PNG, atau WEBP.'];
        }

        $extension = self::ALLOWED_MIME[$mime];
        $filename = 'product_' . uniqid() . '.' . $extension;
        $destination = __DIR__ . '/../uploads/products/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return [null, 'Gagal menyimpan foto produk.'];
        }

        return [$filename, null];
    }

    private function deletePhotoFile(string $filename): void
    {
        $path = __DIR__ . '/../uploads/products/' . $filename;
        if (is_file($path)) {
            @unlink($path);
        }
    }
}
