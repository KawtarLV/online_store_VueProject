<?php

namespace App\Services;

use App\Models\Product;

/**
 * Interface for product service operations
 */
interface IProductService
{
    /**
     * Returns a paginated list of products, optionally filtered by category
     *
     * @param int|null $categoryId
     * @param int $page
     * @param int $perPage
     * @return array<string, mixed>
     */
    public function list(?int $categoryId, int $page, int $perPage): array;

    /**
     * Returns a single product by ID, or null if not found
     *
     * @param int $id
     * @return Product|null
     */
    public function get(int $id): ?Product;

    /**
     * Creates a new product
     *
     * @param Product $product
     * @return Product - saved product with assigned ID
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
