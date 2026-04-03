<?php

namespace App\Repositories;

use App\Models\Product;
use App\Framework\Database;
use PDO;

class ProductRepository implements IProductRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?: Database::pdo();
    }

    /**
     * @return Product[]
     */
    public function all(): array
    {
        $stmt = $this->db->query(
            'SELECT id, name, description, price, stock, brand, specs, rating, category_id, created_at, image_main, image_2, image_3
             FROM products
             ORDER BY id'
        );
        return array_map([$this, 'mapToProduct'], $stmt->fetchAll());
    }

    public function find(int $id): ?Product
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, description, price, stock, brand, specs, rating, category_id, created_at, image_main, image_2, image_3
             FROM products
             WHERE id = ?'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? $this->mapToProduct($row) : null;
    }

    public function paginate(?int $categoryId, int $page, int $perPage): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(50, $perPage));
        $offset = ($page - 1) * $perPage;

        $where = '';
        $params = [];
        if ($categoryId !== null) {
            $where = ' WHERE category_id = ?';
            $params[] = $categoryId;
        }

        $countStmt = $this->db->prepare('SELECT COUNT(*) FROM products' . $where);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $stmt = $this->db->prepare(
            'SELECT id, name, description, price, stock, brand, specs, rating, category_id, created_at, image_main, image_2, image_3
             FROM products' . $where . '
             ORDER BY id DESC
             LIMIT ? OFFSET ?'
        );

        $index = 1;
        foreach ($params as $param) {
            $stmt->bindValue($index++, $param, PDO::PARAM_INT);
        }
        $stmt->bindValue($index++, $perPage, PDO::PARAM_INT);
        $stmt->bindValue($index, $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'items' => array_map([$this, 'mapToProduct'], $stmt->fetchAll()),
            'meta' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => (int) ceil($total / $perPage),
            ],
        ];
    }

    public function create(Product $product): Product
    {
        $images = $this->flattenImages($product);
        $stmt = $this->db->prepare('INSERT INTO products (name, description, price, stock, brand, specs, rating, category_id, image_main, image_2, image_3) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $product->name,
            $product->description,
            $product->price,
            $product->stock,
            $product->brand,
            $product->specs ? json_encode($product->specs) : null,
            $product->rating,
            $product->categoryId,
            $images[0] ?? null,
            $images[1] ?? null,
            $images[2] ?? null,
        ]);
        $product->id = (int) $this->db->lastInsertId();
        return $product;
    }

    public function update(int $id, Product $product): ?Product
    {
        $images = $this->flattenImages($product);
        $stmt = $this->db->prepare('UPDATE products SET name = ?, description = ?, price = ?, stock = ?, brand = ?, specs = ?, rating = ?, category_id = ?, image_main = ?, image_2 = ?, image_3 = ? WHERE id = ?');
        $stmt->execute([
            $product->name,
            $product->description,
            $product->price,
            $product->stock,
            $product->brand,
            $product->specs ? json_encode($product->specs) : null,
            $product->rating,
            $product->categoryId,
            $images[0] ?? null,
            $images[1] ?? null,
            $images[2] ?? null,
            $id
        ]);

        if ($stmt->rowCount() === 0) {
            return $this->find($id);
        }

        $product->id = $id;
        return $product;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM products WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * @param array<string, mixed> $item
     */
    private function mapToProduct(array $item): Product
    {
        $p = new Product();
        $p->id = (int) ($item['id'] ?? 0);
        $p->name = (string) ($item['name'] ?? '');
        $p->description = (string) ($item['description'] ?? '');
        $p->price = (float) ($item['price'] ?? 0);
        $p->stock = (int) ($item['stock'] ?? 0);
        $p->brand = isset($item['brand']) ? (string) $item['brand'] : null;
        $p->specs = isset($item['specs']) && $item['specs'] !== null ? json_decode((string) $item['specs'], true) ?: null : null;
        $p->rating = isset($item['rating']) ? (float) $item['rating'] : 0.0;
        $p->categoryId = isset($item['category_id']) ? (int) $item['category_id'] : null;
        $p->image = isset($item['image_main']) ? (string) $item['image_main'] : null;
        $p->images = $this->collectImages($item);
        $p->createdAt = isset($item['created_at']) ? (string) $item['created_at'] : null;
        return $p;
    }

    /**
     * @param array<string,mixed> $row
     * @return string[]
     */
    private function collectImages(array $row): array
    {
        $urls = [];
        foreach (['image_main', 'image_2', 'image_3'] as $col) {
            if (!empty($row[$col])) {
                $urls[] = (string) $row[$col];
            }
        }
        return $urls;
    }

    /**
     * @return string[]
     */
    private function flattenImages(Product $product): array
    {
        $images = [];
        if ($product->image) {
            $images[] = $product->image;
        }
        if (is_array($product->images)) {
            foreach ($product->images as $url) {
                $url = trim((string) $url);
                if ($url !== '') {
                    $images[] = $url;
                }
            }
        }
        return array_slice($images, 0, 3);
    }
}
