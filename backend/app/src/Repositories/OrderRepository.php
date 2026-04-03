<?php

namespace App\Repositories;

use App\Framework\Database;
use App\Models\Order;
use App\Models\OrderItem;
use PDO;
use RuntimeException;

class OrderRepository implements IOrderRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?: Database::pdo();
    }

    public function all(): array
    {
        $stmt = $this->db->query($this->baseOrderQuery() . ' ORDER BY o.id DESC');
        return $this->mapOrders($stmt->fetchAll());
    }

    public function forUser(int $userId): array
    {
        $stmt = $this->db->prepare($this->baseOrderQuery() . ' WHERE o.user_id = ? ORDER BY o.id DESC');
        $stmt->execute([$userId]);
        return $this->mapOrders($stmt->fetchAll());
    }

    public function create(int $userId, array $items): Order
    {
        $this->db->beginTransaction();

        try {
            // First grab the current prices and stock from the DB.
            $products = $this->fetchProductRows($items, true);
            if ($products === []) {
                throw new RuntimeException('No valid products found');
            }

            $total = 0.0;
            foreach ($items as $item) {
                $productId = (int) ($item['product_id'] ?? 0);
                $quantity = max(1, (int) ($item['quantity'] ?? 0));
                if (!isset($products[$productId])) {
                    continue;
                }

                $stock = (int) ($products[$productId]['stock'] ?? 0);
                if ($quantity > $stock) {
                    throw new RuntimeException('Not enough stock for ' . $products[$productId]['name']);
                }

                $total += ((float) $products[$productId]['price']) * $quantity;
            }

            // Save the main order row before saving each item under it.
            $stmt = $this->db->prepare(
                'INSERT INTO orders (user_id, total, status, payment_method, payment_status) VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $userId,
                $total,
                'Pending',
                'Demo Checkout',
                'Paid',
            ]);

            $orderId = (int) $this->db->lastInsertId();

            $itemStmt = $this->db->prepare(
                'INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)'
            );
            $stockStmt = $this->db->prepare(
                'UPDATE products SET stock = stock - ? WHERE id = ?'
            );

            foreach ($items as $item) {
                $productId = (int) ($item['product_id'] ?? 0);
                $quantity = max(1, (int) ($item['quantity'] ?? 0));
                if (!isset($products[$productId])) {
                    continue;
                }

                $itemStmt->execute([
                    $orderId,
                    $productId,
                    $quantity,
                    (float) $products[$productId]['price'],
                ]);

                $stockStmt->execute([
                    $quantity,
                    $productId,
                ]);
            }

            $this->db->commit();

            return $this->find($orderId);
        } catch (\Throwable $e) {
            // If one step fails, undo everything so the order stays clean.
            $this->db->rollBack();
            throw $e;
        }
    }

    private function find(int $id): Order
    {
        $stmt = $this->db->prepare(
            $this->baseOrderQuery() . ' WHERE o.id = ?'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row) {
            throw new RuntimeException('Order not found');
        }

        $order = $this->mapOrder($row);
        $order->items = $this->fetchItems($id);
        return $order;
    }

    /**
     * @param array<int, array<string, mixed>> $items
     * @return array<int, array<string, mixed>>
     */
    private function fetchProductRows(array $items, bool $lockForUpdate = false): array
    {
        $ids = array_values(array_unique(array_filter(array_map(
            static fn (array $item): int => (int) ($item['product_id'] ?? 0),
            $items
        ))));

        if ($ids === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->db->prepare(
            'SELECT id, name, price, stock FROM products WHERE id IN (' . $placeholders . ')' . ($lockForUpdate ? ' FOR UPDATE' : '')
        );
        $stmt->execute($ids);

        $rows = [];
        foreach ($stmt->fetchAll() as $row) {
            $rows[(int) $row['id']] = $row;
        }

        return $rows;
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return Order[]
     */
    private function mapOrders(array $rows): array
    {
        $orders = [];
        foreach ($rows as $row) {
            $order = $this->mapOrder($row);
            $order->items = $this->fetchItems($order->id);
            $orders[] = $order;
        }

        return $orders;
    }

    private function baseOrderQuery(): string
    {
        return 'SELECT o.id, o.user_id, u.name AS user_name, u.email AS user_email, o.total, o.status, o.payment_method, o.payment_status, o.created_at
                FROM orders o
                JOIN users u ON u.id = o.user_id';
    }

    /**
     * @return OrderItem[]
     */
    private function fetchItems(int $orderId): array
    {
        $stmt = $this->db->prepare(
            'SELECT oi.id, oi.product_id, p.name AS product_name, oi.quantity, oi.price
             FROM order_items oi
             JOIN products p ON p.id = oi.product_id
             WHERE oi.order_id = ?
             ORDER BY oi.id ASC'
        );
        $stmt->execute([$orderId]);

        $items = [];
        foreach ($stmt->fetchAll() as $row) {
            $item = new OrderItem();
            $item->id = (int) $row['id'];
            $item->product_id = (int) $row['product_id'];
            $item->product_name = (string) ($row['product_name'] ?? '');
            $item->quantity = (int) ($row['quantity'] ?? 0);
            $item->price = (float) ($row['price'] ?? 0);
            $items[] = $item;
        }

        return $items;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function mapOrder(array $row): Order
    {
        $order = new Order();
        $order->id = (int) ($row['id'] ?? 0);
        $order->user_id = (int) ($row['user_id'] ?? 0);
        $order->user_name = (string) ($row['user_name'] ?? '');
        $order->user_email = (string) ($row['user_email'] ?? '');
        $order->total = (float) ($row['total'] ?? 0);
        $order->status = (string) ($row['status'] ?? 'Pending');
        $order->payment_method = (string) ($row['payment_method'] ?? 'Demo Checkout');
        $order->payment_status = (string) ($row['payment_status'] ?? 'Paid');
        $order->created_at = isset($row['created_at']) ? (string) $row['created_at'] : null;

        return $order;
    }
}
