<?php

namespace App\Services;

use App\Models\Order;
use App\Repositories\IOrderRepository;
use App\Repositories\OrderRepository;
use RuntimeException;

class OrderService implements IOrderService
{
    private IOrderRepository $repo;

    public function __construct(?IOrderRepository $repo = null)
    {
        $this->repo = $repo ?: new OrderRepository();
    }

    public function list(): array
    {
        return $this->repo->all();
    }

    public function listForUser(int $userId): array
    {
        if ($userId <= 0) {
            throw new RuntimeException('Invalid user');
        }

        return $this->repo->forUser($userId);
    }

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
            $quantity = max(1, (int) ($item['quantity'] ?? 0));
            if ($productId > 0) {
                $cleanItems[] = [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                ];
            }
        }

        if ($cleanItems === []) {
            throw new RuntimeException('Cart is empty');
        }

        return $this->repo->create($userId, $cleanItems);
    }
}
