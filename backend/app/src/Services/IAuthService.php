<?php

namespace App\Services;

/**
 * Interface for authentication services
 * Defines the contract for login, registration, and JWT validation
 */
interface IAuthService
{
    /**
     * Registers a new user account
     *
     * @param string $name
     * @param string $email
     * @param string $password - plain text, implementation must hash it
     * @return array<string, mixed> - { token, user } on success or { error, code } on failure
     */
    public function register(string $name, string $email, string $password): array;

    /**
     * Authenticates a user with email and password
     *
     * @param string $email
     * @param string $password - plain text to verify against the stored hash
     * @return array<string, mixed> - { token, user } on success or { error, code } on failure
     */
    public function login(string $email, string $password): array;

    /**
     * Validates a JWT token and returns its decoded payload
     * Returns null if the token is invalid or expired
     *
     * @param string|null $token
     * @return array<string, mixed>|null
     */
    public function authenticate(?string $token): ?array;
}
