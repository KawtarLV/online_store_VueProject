<?php

namespace App\Models;

class Order
{
    public int $id;
    public int $user_id;
    public string $user_name = '';
    public string $user_email = '';
    public float $total = 0.0;
    public string $status = 'Pending';
    public string $payment_method = 'Demo Checkout';
    public string $payment_status = 'Paid';
    public ?string $created_at = null;
    /** @var OrderItem[] */
    public array $items = [];
}
