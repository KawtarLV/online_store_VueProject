<?php

namespace App\Repositories;

use App\Models\Order;

interface IOrderRepository
{
    /**
     * @return Order[]
     */
    public function all(): array;

    /**
     * @return Order[]
     */
    public function forUser(int $userId): array;

    /**
     * @param array<int, array<string, mixed>> $items
     */
    public function create(int $userId, array $items): Order;
}
