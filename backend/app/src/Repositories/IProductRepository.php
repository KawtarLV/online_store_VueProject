<?php

namespace App\Repositories;

use App\Models\Product;

interface IProductRepository
{
    /**
     * @return Product[]
     */
    public function all(): array;

    /**
     * @return array<string, mixed>
     */
    public function paginate(?int $categoryId, int $page, int $perPage): array;

    public function find(int $id): ?Product;

    public function create(Product $product): Product;

    public function update(int $id, Product $product): ?Product;

    public function delete(int $id): bool;
}
