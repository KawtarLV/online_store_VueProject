<?php

namespace App\Services;

interface IAuthService
{
    /**
     * @return array<string, mixed>
     */
    public function register(string $name, string $email, string $password): array;

    /**
     * @return array<string, mixed>
     */
    public function login(string $email, string $password): array;

    /**
     * @return array<string, mixed>|null
     */
    public function authenticate(?string $token): ?array;
}
