<?php

namespace App\Services;

use App\Models\Product;

interface IProductService
{
    /**
     * @return array<string, mixed>
     */
    public function list(?int $categoryId, int $page, int $perPage): array;

    public function get(int $id): ?Product;

    public function create(Product $product): Product;

    public function update(int $id, Product $product): ?Product;

    public function delete(int $id): bool;
}
