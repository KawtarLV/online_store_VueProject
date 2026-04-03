<?php

namespace App\Services;

use App\Models\Order;

/**
 * Interface for order service operations
 */
interface IOrderService
{
    /**
     * Returns all orders (admin use)
     *
     * @return Order[]
     */
    public function list(): array;

    /**
     * Returns orders for a specific user
     *
     * @param int $userId
     * @return Order[]
     */
    public function listForUser(int $userId): array;

    /**
     * Places a new order
     *
     * @param int $userId
     * @param array<int, array<string, mixed>> $items - cart items: [{ product_id, quantity }]
     * @return Order
     */
    public function create(int $userId, array $items): Order;
}
