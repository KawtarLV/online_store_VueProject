<?php

namespace App\Repositories;

use App\Framework\Database;
use App\Models\Category;
use PDO;

class CategoryRepository implements ICategoryRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?: Database::pdo();
    }

    /**
     * @return Category[]
     */
    public function all(): array
    {
        $stmt = $this->db->query('SELECT id, name, created_at FROM categories ORDER BY name');
        $rows = $stmt->fetchAll();
        return array_map([$this, 'map'], $rows);
    }

    /**
     * @param array<string,mixed> $row
     */
    private function map(array $row): Category
    {
        $c = new Category();
        $c->id = (int) $row['id'];
        $c->name = (string) $row['name'];
        $c->created_at = isset($row['created_at']) ? (string) $row['created_at'] : null;
        return $c;
    }
}
