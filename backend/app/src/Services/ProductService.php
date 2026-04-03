<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\IProductRepository;
use App\Repositories\ProductRepository;

/**
 * Handles product business logic
 * Sits between the controllers and the product repository
 */
class ProductService implements IProductService
{
    private IProductRepository $repo;

    /**
     * @param IProductRepository|null $repo - optional, allows injecting a mock in tests
     */
    public function __construct(?IProductRepository $repo = null)
    {
        $this->repo = $repo ?: new ProductRepository();
    }

    /**
     * Returns a paginated list of products, optionally filtered by category
     *
     * @param int|null $categoryId - filter by category or null for all
     * @param int $page - page number (1-based)
     * @param int $perPage - items per page
     * @return array<string, mixed>
     */
    public function list(?int $categoryId, int $page, int $perPage): array
    {
        return $this->repo->paginate($categoryId, $page, $perPage);
    }

    /**
     * Returns a single product by ID, or null if not found
     *
     * @param int $id
     * @return Product|null
     */
    public function get(int $id): ?Product
    {
        return $this->repo->find($id);
    }

    /**
     * Creates a new product and returns the saved record
     *
     * @param Product $product
     * @return Product
     */
    public function create(Product $product): Product
    {
        return $this->repo->create($product);
    }

    /**
     * Updates a product by ID
     * Returns null if the product was not found
     *
     * @param int $id
     * @param Product $product
     * @return Product|null
     */
    public function update(int $id, Product $product): ?Product
    {
        return $this->repo->update($id, $product);
    }

    /**
     * Deletes a product by ID
     * Returns false if the product was not found
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }
}
