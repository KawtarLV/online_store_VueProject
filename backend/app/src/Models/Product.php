<?php

namespace App\Models;

class Product
{
    public int $id;
    public string $name;
    public string $description;
    public float $price;
    public int $stock;
    public ?string $brand = null;
    /** @var array<string,mixed>|null */
    public ?array $specs = null;
    public float $rating = 0.0;
    public ?int $categoryId = null;
    public ?string $image = null; // main image url
    /** @var string[]|null */
    public ?array $images = null; // gallery images
    public ?string $createdAt = null;
}
