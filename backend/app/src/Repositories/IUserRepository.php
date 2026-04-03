<?php

namespace App\Repositories;

use App\Models\User;

/**
 * Interface for user database operations
 * Implementations must never return the password hash in list queries
 */
interface IUserRepository
{
    /**
     * Finds a user by email address (used for login)
     * This is the only method that returns the password hash
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User;

    /**
     * Inserts a new user and returns the saved record
     * The password must already be hashed before calling this method
     *
     * @param User $user
     * @return User
     */
    public function create(User $user): User;

    /**
     * Returns all users (password field excluded)
     *
     * @return User[]
     */
    public function all(): array;

    /**
     * Deletes a user by ID
     *
     * @param int $id
     * @return bool - true on success, false if not found
     */
    public function delete(int $id): bool;
}
