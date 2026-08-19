<?php

class TraceabilityLog
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function allByProduct(int $productId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM traceability_logs WHERE product_id = ? ORDER BY id ASC');
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    public function getLastLog(int $productId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM traceability_logs WHERE product_id = ? ORDER BY id DESC LIMIT 1');
        $stmt->execute([$productId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $last = $this->getLastLog($data['product_id']);
        $prevHash = $last['data_hash'] ?? null;

        $dataHash = hash('sha256', implode('|', [
            $data['product_id'],
            $data['activity'],
            $data['location'],
            $data['actor'],
            $data['timestamp'],
            $prevHash,
            uniqid('', true),
        ]));

        $sql = 'INSERT INTO traceability_logs
                    (product_id, actor_id, actor, activity, location, notes, timestamp, data_hash, prev_hash, status)
                VALUES
                    (:product_id, :actor_id, :actor, :activity, :location, :notes, :timestamp, :data_hash, :prev_hash, :status)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'product_id' => $data['product_id'],
            'actor_id'   => $data['actor_id'] ?? null,
            'actor'      => $data['actor'],
            'activity'   => $data['activity'],
            'location'   => $data['location'],
            'notes'      => $data['notes'] ?? null,
            'timestamp'  => $data['timestamp'],
            'data_hash'  => $dataHash,
            'prev_hash'  => $prevHash,
            'status'     => $data['status'] ?? 'recorded',
        ]);

        $logId = (int) $this->db->lastInsertId();

        (new BlockchainBlock())->append(
            $data['product_id'],
            $data['actor'],
            'traceability_log',
            $logId,
            $dataHash
        );

        return $logId;
    }

    public function countAll(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM traceability_logs')->fetchColumn();
    }

    public function countByActivity(string $activity): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM traceability_logs WHERE activity = ?');
        $stmt->execute([$activity]);
        return (int) $stmt->fetchColumn();
    }

    public function countGroupedByActivity(): array
    {
        $activities = ['Production', 'Distribution', 'Retail Receiving', 'Consumer Verification'];
        $counts = array_fill_keys($activities, 0);

        $rows = $this->db->query('SELECT activity, COUNT(*) AS cnt FROM traceability_logs GROUP BY activity')->fetchAll();
        foreach ($rows as $row) {
            $counts[$row['activity']] = (int) $row['cnt'];
        }

        return $counts;
    }

    public function countByActor(int $actorId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM traceability_logs WHERE actor_id = ?');
        $stmt->execute([$actorId]);
        return (int) $stmt->fetchColumn();
    }

    public function recentByActor(int $actorId, int $limit = 5): array
    {
        $sql = 'SELECT l.*, p.product_name
                FROM traceability_logs l
                JOIN products p ON p.id = l.product_id
                WHERE l.actor_id = ?
                ORDER BY l.id DESC
                LIMIT ' . (int) $limit;
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$actorId]);
        return $stmt->fetchAll();
    }
}
