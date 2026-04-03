<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\AuthService;

class UserController extends Controller
{
    private UserRepository $repo;
    private AuthService $auth;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new UserRepository();
        $this->auth = new AuthService();
    }

    public function getAll(): void
    {
        if (!$this->requireAdmin()) {
            return;
        }

        $this->sendSuccessResponse($this->repo->all());
    }

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

        $name = trim((string) ($input['name'] ?? ''));
        $email = trim((string) ($input['email'] ?? ''));
        $password = (string) ($input['password'] ?? '');
        $role = (string) ($input['role'] ?? 'customer');

        if ($name === '' || $email === '' || $password === '') {
            $this->sendErrorResponse('name, email, password are required', 400);
            return;
        }

        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = password_hash($password, PASSWORD_DEFAULT);
        $user->role = in_array($role, ['admin', 'customer'], true) ? $role : 'customer';

        $created = $this->repo->create($user);
        unset($created->password);
        $this->sendSuccessResponse($created, 201);
    }

    /**
     * @param array<string,mixed> $vars
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
        if (!$this->repo->delete($id)) {
            $this->sendErrorResponse('User not found', 404);
            return;
        }
        $this->sendSuccessResponse(['message' => 'User deleted']);
    }

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
