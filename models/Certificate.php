<?php

class Certificate
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findByProductId(int $productId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM certificates WHERE product_id = ? ORDER BY id DESC LIMIT 1');
        $stmt->execute([$productId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByCertificateNumber(string $number): ?array
    {
        $sql = 'SELECT c.*, p.product_name, p.product_code, p.category, p.origin_location, p.production_date, p.photo,
                       p.status AS product_status, p.id AS product_id,
                       u.name AS producer_name, u.company_name AS producer_company
                FROM certificates c
                JOIN products p ON p.id = c.product_id
                JOIN users u ON u.id = p.producer_id
                WHERE c.certificate_number = ? LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$number]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO certificates (product_id, certificate_number, issuer, issued_date, certificate_hash, status, created_at)
                VALUES (:product_id, :certificate_number, :issuer, :issued_date, :certificate_hash, :status, NOW())';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'product_id'         => $data['product_id'],
            'certificate_number' => $data['certificate_number'],
            'issuer'             => $data['issuer'] ?? 'PLTS Certification Authority',
            'issued_date'        => $data['issued_date'],
            'certificate_hash'   => $data['certificate_hash'],
            'status'             => $data['status'] ?? 'active',
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function countAll(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM certificates')->fetchColumn();
    }

    public function countActive(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM certificates WHERE status = 'active'")->fetchColumn();
    }

    public function generateCertificateNumber(): string
    {
        $year = date('Y');
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM certificates WHERE certificate_number LIKE ?');
        $stmt->execute(['CERT-PLTS-' . $year . '-%']);
        $sequence = (int) $stmt->fetchColumn() + 1;

        do {
            $number = 'CERT-PLTS-' . $year . '-' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
            $checkStmt = $this->db->prepare('SELECT COUNT(*) FROM certificates WHERE certificate_number = ?');
            $checkStmt->execute([$number]);
            $exists = (int) $checkStmt->fetchColumn() > 0;
            $sequence++;
        } while ($exists);

        return $number;
    }
}
