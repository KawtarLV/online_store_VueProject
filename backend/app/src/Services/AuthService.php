<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\IUserRepository;
use App\Repositories\UserRepository;
use App\Utils\JwtManager;

class AuthService implements IAuthService
{
    private IUserRepository $users;

    public function __construct(?IUserRepository $users = null)
    {
        $this->users = $users ?: new UserRepository();
    }

    /**
     * Register a user with hashed password.
     *
     * @return array{user:User}|array{error:string,code:int}
     */
    public function register(string $name, string $email, string $password): array
    {
        $existing = $this->users->findByEmail($email);
        if ($existing) {
            return ['error' => 'Email already registered', 'code' => 409];
        }

        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = password_hash($password, PASSWORD_DEFAULT);
        $user->role = 'customer';

        $created = $this->users->create($user);
        unset($created->password);

        return [
            'token' => $this->makeToken($created),
            'user' => $created,
        ];
    }

    /**
     * Login with email/password.
     *
     * @return array{user:User}|array{error:string,code:int}
     */
    public function login(string $email, string $password): array
    {
        $user = $this->users->findByEmail($email);
        if (!$user) {
            return ['error' => 'Invalid credentials', 'code' => 401];
        }

        if (!password_verify($password, $user->password)) {
            return ['error' => 'Invalid credentials', 'code' => 401];
        }

        unset($user->password);
        return [
            'token' => $this->makeToken($user),
            'user' => $user,
        ];
    }

    public function authenticate(?string $token): ?array
    {
        if (!$token) {
            return null;
        }

        // The token already holds the basic user data we need for protected routes.
        return JwtManager::decode($token);
    }

    private function makeToken(User $user): string
    {
        return JwtManager::encode([
            'sub' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'exp' => time() + (60 * 60 * 24),
        ]);
    }
}
