<?php

namespace App\Repositories;

use App\Framework\Database;
use App\Models\User;
use PDO;

class UserRepository implements IUserRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?: Database::pdo();
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare('SELECT id, name, email, password, role, created_at FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ? $this->map($row) : null;
    }

    public function create(User $user): User
    {
        $stmt = $this->db->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
        $stmt->execute([
            $user->name,
            $user->email,
            $user->password,
            $user->role,
        ]);
        $user->id = (int) $this->db->lastInsertId();
        return $user;
    }

    /**
     * @return User[]
     */
    public function all(): array
    {
        $stmt = $this->db->query('SELECT id, name, email, role, created_at FROM users ORDER BY id DESC');
        return array_map([$this, 'map'], $stmt->fetchAll());
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * @param array<string,mixed> $row
     */
    private function map(array $row): User
    {
        $u = new User();
        $u->id = (int) $row['id'];
        $u->name = (string) $row['name'];
        $u->email = (string) $row['email'];
        $u->password = isset($row['password']) ? (string) $row['password'] : '';
        $u->role = (string) $row['role'];
        $u->created_at = isset($row['created_at']) ? (string) $row['created_at'] : null;
        return $u;
    }
}
