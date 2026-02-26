<?php

namespace App\Repositories;

use App\Models\Product;
use App\Utils\JsonStore;

class ProductRepository
{
    private JsonStore $store;

    public function __construct()
    {
        $this->store = new JsonStore(__DIR__ . '/../data/products.json');
    }

    /**
     * @return Product[]
     */
    public function all(): array
    {
        return array_map([$this, 'mapToProduct'], $this->store->all());
    }

    public function find(int $id): ?Product
    {
        foreach ($this->store->all() as $item) {
            if ((int) $item['id'] === $id) {
                return $this->mapToProduct($item);
            }
        }
        return null;
    }

    public function create(Product $product): Product
    {
        $records = $this->store->all();
        $product->id = empty($records) ? 1 : max(array_column($records, 'id')) + 1;
        $records[] = $this->toArray($product);
        $this->store->persist($records);
        return $product;
    }

    public function update(int $id, Product $product): ?Product
    {
        $records = $this->store->all();
        $found = false;
        foreach ($records as &$record) {
            if ((int) $record['id'] === $id) {
                $product->id = $id;
                $record = $this->toArray($product);
                $found = true;
                break;
            }
        }
        if (!$found) {
            return null;
        }
        $this->store->persist($records);
        return $product;
    }

    public function delete(int $id): bool
    {
        $records = $this->store->all();
        $new = array_filter($records, fn($r) => (int) $r['id'] !== $id);
        if (count($new) === count($records)) {
            return false;
        }
        $this->store->persist(array_values($new));
        return true;
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
        $p->image = (string) ($item['image'] ?? '');
        return $p;
    }

    /**
     * @return array<string, mixed>
     */
    private function toArray(Product $p): array
    {
        return [
            'id' => $p->id,
            'name' => $p->name,
            'description' => $p->description,
            'price' => $p->price,
            'stock' => $p->stock,
            'image' => $p->image,
        ];
    }
}
