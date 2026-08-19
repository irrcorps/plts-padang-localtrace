<?php

class BlockchainBlock
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function append(?int $productId, string $actor, string $referenceType, int $referenceId, string $eventHash): int
    {
        $last = $this->db->query('SELECT block_index, block_hash FROM blockchain_blocks ORDER BY block_index DESC LIMIT 1')->fetch();
        $blockIndex = $last ? ((int) $last['block_index'] + 1) : 1;
        $prevHash = $last['block_hash'] ?? null;
        $blockHash = hash('sha256', $eventHash . '|' . $prevHash . '|' . $blockIndex . '|' . microtime());

        $sql = 'INSERT INTO blockchain_blocks
                    (block_index, product_id, actor, reference_type, reference_id, block_hash, prev_hash, status, created_at)
                VALUES
                    (:block_index, :product_id, :actor, :reference_type, :reference_id, :block_hash, :prev_hash, :status, NOW())';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'block_index'    => $blockIndex,
            'product_id'     => $productId,
            'actor'          => $actor,
            'reference_type' => $referenceType,
            'reference_id'   => $referenceId,
            'block_hash'     => $blockHash,
            'prev_hash'      => $prevHash,
            'status'         => 'valid',
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function latest(int $limit = 20): array
    {
        $sql = 'SELECT b.*, p.product_name
                FROM blockchain_blocks b
                LEFT JOIN products p ON p.id = b.product_id
                ORDER BY b.block_index DESC
                LIMIT ' . (int) $limit;
        return $this->db->query($sql)->fetchAll();
    }

    public function countAll(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM blockchain_blocks')->fetchColumn();
    }

    public function byProduct(int $productId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM blockchain_blocks WHERE product_id = ? ORDER BY block_index ASC');
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }
}
