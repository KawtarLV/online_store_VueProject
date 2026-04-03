<?php

namespace App\Repositories;

use App\Models\Product;

/**
 * Interface for product database operations
 * All implementations must use PDO parameterized queries to prevent SQL injection
 */
interface IProductRepository
{
    /**
     * Returns all products
     *
     * @return Product[]
     */
    public function all(): array;

    /**
     * Returns a paginated list of products, optionally filtered by category
     *
     * @param int|null $categoryId
     * @param int $page
     * @param int $perPage
     * @return array<string, mixed>
     */
    public function paginate(?int $categoryId, int $page, int $perPage): array;

    /**
     * Finds a product by ID
     *
     * @param int $id
     * @return Product|null
     */
    public function find(int $id): ?Product;

    /**
     * Inserts a new product and returns the saved record with its ID
     *
     * @param Product $product
     * @return Product
     */
    public function create(Product $product): Product;

    /**
     * Updates an existing product
     *
     * @param int $id
     * @param Product $product
     * @return Product|null - updated product or null if not found
     */
    public function update(int $id, Product $product): ?Product;

    /**
     * Deletes a product by ID
     *
     * @param int $id
     * @return bool - true on success, false if not found
     */
    public function delete(int $id): bool;
}
