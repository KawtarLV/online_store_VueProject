<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Services\AuthService;

class AuthController extends Controller
{
    private AuthService $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = new AuthService();
    }

    public function login(): void
    {
        $payload = $this->getJsonBody();
        if ($payload === null) {
            $this->sendErrorResponse('Invalid JSON body', 400);
            return;
        }

        $email = trim((string) ($payload['email'] ?? ''));
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

    public function register(): void
    {
        $payload = $this->getJsonBody();
        if ($payload === null) {
            $this->sendErrorResponse('Invalid JSON body', 400);
            return;
        }

        $name = trim((string) ($payload['name'] ?? ''));
        $email = trim((string) ($payload['email'] ?? ''));
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
