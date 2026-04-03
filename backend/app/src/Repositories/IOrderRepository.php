<?php

namespace App\Repositories;

use App\Models\Order;

/**
 * Interface for order database operations
 * The create() method must run inside a transaction to safely handle stock deduction
 */
interface IOrderRepository
{
    /**
     * Returns all orders
     *
     * @return Order[]
     */
    public function all(): array;

    /**
     * Returns orders for a specific user
     *
     * @param int $userId
     * @return Order[]
     */
    public function forUser(int $userId): array;

    /**
     * Inserts a new order and its line items inside a database transaction
     * Uses FOR UPDATE locking to prevent race conditions on stock counts
     *
     * @param int $userId
     * @param array<int, array<string, mixed>> $items - [{ product_id, quantity }]
     * @return Order - the saved order with items
     */
    public function create(int $userId, array $items): Order;
}
