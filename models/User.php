<?php

class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO users (name, email, password, role, company_name, phone, address, created_at)
                VALUES (:name, :email, :password, :role, :company_name, :phone, :address, NOW())';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'name'         => $data['name'],
            'email'        => $data['email'],
            'password'     => password_hash($data['password'], PASSWORD_BCRYPT),
            'role'         => $data['role'],
            'company_name' => $data['company_name'] ?? null,
            'phone'        => $data['phone'] ?? null,
            'address'      => $data['address'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function emailExists(string $email): bool
    {
        return $this->findByEmail($email) !== null;
    }

    public function countAll(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }

    public function countByRole(string $role): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM users WHERE role = ?');
        $stmt->execute([$role]);
        return (int) $stmt->fetchColumn();
    }

    public function all(): array
    {
        return $this->db->query('SELECT id, name, email, role, company_name, phone, created_at FROM users ORDER BY created_at DESC')->fetchAll();
    }
}
