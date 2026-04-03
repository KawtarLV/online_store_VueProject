<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Services\IAuthService;

/**
 * Handles user login and registration
 * Routes: POST /login, POST /register
 */
class AuthController extends Controller
{
    private IAuthService $auth;

    /**
     * @param IAuthService $auth - injected by the IoC container
     */
    public function __construct(IAuthService $auth)
    {
        parent::__construct();
        $this->auth = $auth;
    }

    /**
     * Log in with email and password
     * Returns a JWT token and user info on success
     */
    public function login(): void
    {
        $payload = $this->getJsonBody();
        if ($payload === null) {
            $this->sendErrorResponse('Invalid JSON body', 400);
            return;
        }

        // sanitize user input to prevent script injection (htmlspecialchars)
        $email    = $this->sanitize((string) ($payload['email'] ?? ''));
        $password = (string) ($payload['password'] ?? '');

        if ($email === '' || $password === '') {
            $this->sendErrorResponse('email and password are required', 400);
            return;
        }

        $result = $this->auth->login($email, $password);
        if (isset($result['error'])) {
            $this->sendErrorResponse($result['error'], $result['code'] ?? 400);
            return;
        }

        $this->sendSuccessResponse($result, 200);
    }

    /**
     * Register a new user account
     * Returns a JWT token and user info on success
     */
    public function register(): void
    {
        $payload = $this->getJsonBody();
        if ($payload === null) {
            $this->sendErrorResponse('Invalid JSON body', 400);
            return;
        }

        // sanitize free-text fields to prevent script injection
        $name     = $this->sanitize((string) ($payload['name'] ?? ''));
        $email    = $this->sanitize((string) ($payload['email'] ?? ''));
        $password = (string) ($payload['password'] ?? '');

        if ($name === '' || $email === '' || $password === '') {
            $this->sendErrorResponse('name, email, and password are required', 400);
            return;
        }

        $result = $this->auth->register($name, $email, $password);

        if (isset($result['error'])) {
            $this->sendErrorResponse($result['error'], $result['code'] ?? 400);
            return;
        }

        $this->sendSuccessResponse($result, 201);
    }
}
