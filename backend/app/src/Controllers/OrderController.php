<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Services\AuthService;
use App\Services\OrderService;
use RuntimeException;

class OrderController extends Controller
{
    private OrderService $orders;
    private AuthService $auth;

    public function __construct()
    {
        parent::__construct();
        $this->orders = new OrderService();
        $this->auth = new AuthService();
    }

    public function getAll(): void
    {
        $payload = $this->requireAdmin();
        if ($payload === null) {
            return;
        }

        $this->sendSuccessResponse($this->orders->list());
    }

    public function getMine(): void
    {
        $payload = $this->requireAuthenticatedUser();
        if ($payload === null) {
            return;
        }

        try {
            $this->sendSuccessResponse(
                $this->orders->listForUser((int) ($payload['sub'] ?? 0))
            );
        } catch (RuntimeException $e) {
            $this->sendErrorResponse($e->getMessage(), 400);
        }
    }

    public function create(): void
    {
        $payload = $this->requireAuthenticatedUser();
        if ($payload === null) {
            return;
        }

        $input = $this->getJsonBody();
        if ($input === null) {
            $this->sendErrorResponse('Invalid JSON body', 400);
            return;
        }

        try {
            $order = $this->orders->create(
                (int) ($payload['sub'] ?? 0),
                is_array($input['items'] ?? null) ? $input['items'] : []
            );

            $this->sendSuccessResponse($order, 201);
        } catch (RuntimeException $e) {
            $this->sendErrorResponse($e->getMessage(), 400);
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    private function requireAuthenticatedUser(): ?array
    {
        $payload = $this->auth->authenticate($this->getBearerToken());
        if ($payload === null) {
            $this->sendErrorResponse('Login required', 401);
            return null;
        }

        return $payload;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function requireAdmin(): ?array
    {
        $payload = $this->requireAuthenticatedUser();
        if ($payload === null) {
            return null;
        }

        if (($payload['role'] ?? '') !== 'admin') {
            $this->sendErrorResponse('Admin token required', 403);
            return null;
        }

        return $payload;
    }
}
