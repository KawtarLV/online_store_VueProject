<?php

namespace App\Models;

class OrderItem
{
    public int $id;
    public int $product_id;
    public string $product_name = '';
    public int $quantity = 0;
    public float $price = 0.0;
}
