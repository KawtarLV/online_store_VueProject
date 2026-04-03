<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Services\IAuthService;
use App\Services\IOrderService;
use RuntimeException;

/**
 * Handles order creation and retrieval
 *
 * Routes:
 *   GET  /orders    - list all orders (admin only)
 *   GET  /my-orders - list the logged-in user's orders
 *   POST /orders    - place a new order
 */
class OrderController extends Controller
{
    private IOrderService $orders;
    private IAuthService $auth;

    /**
     * @param IOrderService $orders - injected by the IoC container
     * @param IAuthService $auth - injected by the IoC container
     */
    public function __construct(IOrderService $orders, IAuthService $auth)
    {
        parent::__construct();
        $this->orders = $orders;
        $this->auth   = $auth;
    }

    /**
     * Returns all orders in the system (admin only)
     */
    public function getAll(): void
    {
        $payload = $this->requireAdmin();
        if ($payload === null) {
            return;
        }

        $this->sendSuccessResponse($this->orders->list());
    }

    /**
     * Returns orders for the currently logged-in user
     */
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

    /**
     * Places a new order for the logged-in user
     * Expects JSON body: { items: [{ product_id, quantity }] }
     */
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
     * Checks that the request has a valid JWT (any logged-in user)
     * Returns the decoded payload, or null and sends 401 if not authenticated
     *
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
     * Checks that the request has a valid admin JWT
     * Returns the decoded payload, or null and sends 401/403 if unauthorized
     *
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
