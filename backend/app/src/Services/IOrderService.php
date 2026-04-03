<?php

namespace App\Services;

use App\Models\Order;

interface IOrderService
{
    /**
     * @return Order[]
     */
    public function list(): array;

    /**
     * @return Order[]
     */
    public function listForUser(int $userId): array;

    /**
     * @param array<int, array<string, mixed>> $items
     */
    public function create(int $userId, array $items): Order;
}
