<?php

namespace App\Repositories;

use App\Models\User;

interface IUserRepository
{
    public function findByEmail(string $email): ?User;

    public function create(User $user): User;

    /**
     * @return User[]
     */
    public function all(): array;

    public function delete(int $id): bool;
}
