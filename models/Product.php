<?php

class Product
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, u.name AS producer_name, u.company_name AS producer_company
             FROM products p
             JOIN users u ON u.id = p.producer_id
             WHERE p.id = ? LIMIT 1'
        );
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        return $product ?: null;
    }

    public function allByProducer(int $producerId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM products WHERE producer_id = ? ORDER BY created_at DESC');
        $stmt->execute([$producerId]);
        return $stmt->fetchAll();
    }

    public function all(): array
    {
        $sql = 'SELECT p.*, u.name AS producer_name, u.company_name AS producer_company
                FROM products p
                JOIN users u ON u.id = p.producer_id
                ORDER BY p.created_at DESC';
        return $this->db->query($sql)->fetchAll();
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO products
                    (producer_id, product_code, product_name, category, description, production_date, origin_location, photo, status, created_at, updated_at)
                VALUES
                    (:producer_id, :product_code, :product_name, :category, :description, :production_date, :origin_location, :photo, :status, NOW(), NOW())';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'producer_id'      => $data['producer_id'],
            'product_code'     => $data['product_code'],
            'product_name'     => $data['product_name'],
            'category'         => $data['category'],
            'description'      => $data['description'],
            'production_date'  => $data['production_date'],
            'origin_location'  => $data['origin_location'],
            'photo'            => $data['photo'] ?? null,
            'status'           => $data['status'] ?? 'draft',
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $sql = 'UPDATE products SET
                    product_name = :product_name,
                    category = :category,
                    description = :description,
                    production_date = :production_date,
                    origin_location = :origin_location,
                    updated_at = NOW()';
        $params = [
            'id'               => $id,
            'product_name'     => $data['product_name'],
            'category'         => $data['category'],
            'description'      => $data['description'],
            'production_date'  => $data['production_date'],
            'origin_location'  => $data['origin_location'],
        ];

        if (!empty($data['photo'])) {
            $sql .= ', photo = :photo';
            $params['photo'] = $data['photo'];
        }

        $sql .= ' WHERE id = :id';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
    }

    public function updateStatus(int $id, string $status): void
    {
        $stmt = $this->db->prepare('UPDATE products SET status = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$status, $id]);
    }

    public function updateStatusWithNotes(int $id, string $status, ?string $notes): void
    {
        $stmt = $this->db->prepare('UPDATE products SET status = ?, admin_notes = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$status, $notes, $id]);
    }

    public function findByCode(string $code): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, u.name AS producer_name, u.company_name AS producer_company
             FROM products p
             JOIN users u ON u.id = p.producer_id
             WHERE p.product_code = ? LIMIT 1'
        );
        $stmt->execute([$code]);
        $product = $stmt->fetch();
        return $product ?: null;
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM products WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function countAll(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM products')->fetchColumn();
    }

    public function countByProducer(int $producerId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM products WHERE producer_id = ?');
        $stmt->execute([$producerId]);
        return (int) $stmt->fetchColumn();
    }

    public function countByStatus(string $status, ?int $producerId = null): int
    {
        if ($producerId !== null) {
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM products WHERE status = ? AND producer_id = ?');
            $stmt->execute([$status, $producerId]);
        } else {
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM products WHERE status = ?');
            $stmt->execute([$status]);
        }
        return (int) $stmt->fetchColumn();
    }

    public function countGroupedByStatus(): array
    {
        $statuses = ['draft', 'submitted', 'verified', 'certified', 'rejected'];
        $counts = array_fill_keys($statuses, 0);

        $rows = $this->db->query('SELECT status, COUNT(*) AS cnt FROM products GROUP BY status')->fetchAll();
        foreach ($rows as $row) {
            $counts[$row['status']] = (int) $row['cnt'];
        }

        return $counts;
    }

    public function generateProductCode(): string
    {
        do {
            $code = 'PROD-' . date('ym') . '-' . strtoupper(bin2hex(random_bytes(3)));
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM products WHERE product_code = ?');
            $stmt->execute([$code]);
            $exists = (int) $stmt->fetchColumn() > 0;
        } while ($exists);

        return $code;
    }
}
