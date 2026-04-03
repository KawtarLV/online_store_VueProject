<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Models\User;
use App\Repositories\IUserRepository;
use App\Services\IAuthService;

/**
 * Handles user management for admins
 * Routes: GET /users, POST /users, DELETE /users/{id}
 */
class UserController extends Controller
{
    private IUserRepository $repo;
    private IAuthService $auth;

    /**
     * @param IUserRepository $repo - injected by the IoC container
     * @param IAuthService $auth - injected by the IoC container
     */
    public function __construct(IUserRepository $repo, IAuthService $auth)
    {
        parent::__construct();
        $this->repo = $repo;
        $this->auth = $auth;
    }

    /**
     * Returns all users (password excluded)
     * Admin only
     */
    public function getAll(): void
    {
        if (!$this->requireAdmin()) {
            return;
        }

        $this->sendSuccessResponse($this->repo->all());
    }

    /**
     * Creates a new user account
     * The password is hashed with bcrypt before being stored
     * Admin only
     */
    public function create(): void
    {
        if (!$this->requireAdmin()) {
            return;
        }

        $input = $this->getJsonBody();
        if ($input === null) {
            $this->sendErrorResponse('Invalid JSON', 400);
            return;
        }

        // sanitize user-supplied text fields to prevent script injection
        $name     = $this->sanitize((string) ($input['name'] ?? ''));
        $email    = $this->sanitize((string) ($input['email'] ?? ''));
        $password = (string) ($input['password'] ?? '');
        $role     = (string) ($input['role'] ?? 'customer');

        if ($name === '' || $email === '' || $password === '') {
            $this->sendErrorResponse('name, email, password are required', 400);
            return;
        }

        $user           = new User();
        $user->name     = $name;
        $user->email    = $email;
        $user->password = password_hash($password, PASSWORD_DEFAULT);
        $user->role     = in_array($role, ['admin', 'customer'], true) ? $role : 'customer';

        $created = $this->repo->create($user);
        unset($created->password);
        $this->sendSuccessResponse($created, 201);
    }

    /**
     * Deletes a user by ID
     * Admin only
     *
     * @param array<string,mixed> $vars - route params (id)
     */
    public function delete(array $vars): void
    {
        if (!$this->requireAdmin()) {
            return;
        }

        $id = (int) ($vars['id'] ?? 0);
        if ($id === 0) {
            $this->sendErrorResponse('Invalid id', 400);
            return;
        }

        // GDPR right to erasure: deleting the user also cascades to their orders
        // (the orders table has ON DELETE CASCADE on user_id)
        if (!$this->repo->delete($id)) {
            $this->sendErrorResponse('User not found', 404);
            return;
        }
        $this->sendSuccessResponse(['message' => 'User deleted']);
    }

    /**
     * Checks that the request has a valid admin JWT
     * Returns false and sends a 403 if not
     *
     * @return bool
     */
    private function requireAdmin(): bool
    {
        $payload = $this->auth->authenticate($this->getBearerToken());
        if (!$payload || ($payload['role'] ?? '') !== 'admin') {
            $this->sendErrorResponse('Admin token required', 403);
            return false;
        }

        return true;
    }
}
