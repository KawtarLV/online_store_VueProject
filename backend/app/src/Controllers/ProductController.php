<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Models\Product;
use App\Services\ProductService;

class ProductController extends Controller
{
    private ProductService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new ProductService();
    }

    public function getAll(): void
    {
        $this->sendSuccessResponse($this->service->list());
    }

    /**
     * @param array<string, mixed> $vars
     */
    public function get(array $vars): void
    {
        $id = (int) ($vars['id'] ?? 0);
        $product = $this->service->get($id);
        if (!$product) {
            $this->sendErrorResponse('Product not found', 404);
            return;
        }
        $this->sendSuccessResponse($product);
    }

    public function create(): void
    {
        $product = $this->mapPostDataToClass(Product::class);
        if (!$product) {
            $this->sendErrorResponse('Invalid payload', 400);
            return;
        }
        $created = $this->service->create($product);
        $this->sendSuccessResponse($created, 201);
    }

    /**
     * @param array<string, mixed> $vars
     */
    public function update(array $vars): void
    {
        $id = (int) ($vars['id'] ?? 0);
        $product = $this->mapPostDataToClass(Product::class);
        if (!$product) {
            $this->sendErrorResponse('Invalid payload', 400);
            return;
        }
        $updated = $this->service->update($id, $product);
        if (!$updated) {
            $this->sendErrorResponse('Product not found', 404);
            return;
        }
        $this->sendSuccessResponse($updated);
    }

    /**
     * @param array<string, mixed> $vars
     */
    public function delete(array $vars): void
    {
        $id = (int) ($vars['id'] ?? 0);
        $deleted = $this->service->delete($id);
        if (!$deleted) {
            $this->sendErrorResponse('Product not found', 404);
            return;
        }
        $this->sendSuccessResponse(['message' => 'Product deleted']);
    }
}
