<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;

class ProductService
{
    private ProductRepository $repo;

    public function __construct()
    {
        $this->repo = new ProductRepository();
    }

    /**
     * @return Product[]
     */
    public function list(): array
    {
        return $this->repo->all();
    }

    public function get(int $id): ?Product
    {
        return $this->repo->find($id);
    }

    public function create(Product $product): Product
    {
        return $this->repo->create($product);
    }

    public function update(int $id, Product $product): ?Product
    {
        return $this->repo->update($id, $product);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }
}
