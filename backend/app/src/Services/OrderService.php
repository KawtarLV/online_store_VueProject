<?php

namespace App\Services;

use App\Models\Order;
use App\Repositories\IOrderRepository;
use App\Repositories\OrderRepository;
use RuntimeException;

/**
 * Handles order business logic
 * Validates and cleans up cart items before passing them to the repository
 */
class OrderService implements IOrderService
{
    private IOrderRepository $repo;

    /**
     * @param IOrderRepository|null $repo - optional, allows injecting a mock in tests
     */
    public function __construct(?IOrderRepository $repo = null)
    {
        $this->repo = $repo ?: new OrderRepository();
    }

    /**
     * Returns all orders (admin view)
     *
     * @return Order[]
     */
    public function list(): array
    {
        return $this->repo->all();
    }

    /**
     * Returns orders for a specific user
     *
     * @param int $userId
     * @return Order[]
     * @throws RuntimeException if userId is invalid
     */
    public function listForUser(int $userId): array
    {
        if ($userId <= 0) {
            throw new RuntimeException('Invalid user');
        }

        return $this->repo->forUser($userId);
    }

    /**
     * Places a new order for a user
     * Sanitizes cart items: removes invalid product IDs, clamps quantity to min 1
     * The repository handles stock deduction and totals inside a transaction
     *
     * @param int $userId
     * @param array<int, array<string, mixed>> $items - raw cart items from the request
     * @return Order - the saved order with items
     * @throws RuntimeException if cart is empty or userId is invalid
     */
    public function create(int $userId, array $items): Order
    {
        if ($userId <= 0) {
            throw new RuntimeException('Invalid user');
        }

        if ($items === []) {
            throw new RuntimeException('Cart is empty');
        }

        $cleanItems = [];
        foreach ($items as $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            $quantity  = max(1, (int) ($item['quantity'] ?? 0));
            if ($productId > 0) {
                $cleanItems[] = [
                    'product_id' => $productId,
                    'quantity'   => $quantity,
                ];
            }
        }

        if ($cleanItems === []) {
            throw new RuntimeException('Cart is empty');
        }

        return $this->repo->create($userId, $cleanItems);
    }
}
