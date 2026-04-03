<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\IProductRepository;
use App\Repositories\ProductRepository;

class ProductService implements IProductService
{
    private IProductRepository $repo;

    public function __construct(?IProductRepository $repo = null)
    {
        $this->repo = $repo ?: new ProductRepository();
    }

    public function list(?int $categoryId, int $page, int $perPage): array
    {
        return $this->repo->paginate($categoryId, $page, $perPage);
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
