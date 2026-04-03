<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\IUserRepository;
use App\Repositories\UserRepository;
use App\Utils\JwtManager;

/**
 * Handles user registration, login, and JWT authentication
 * Passwords are always hashed with bcrypt and never returned to the client
 */
class AuthService implements IAuthService
{
    private IUserRepository $users;

    /**
     * @param IUserRepository|null $users - optional, allows injecting a mock in tests
     */
    public function __construct(?IUserRepository $users = null)
    {
        $this->users = $users ?: new UserRepository();
    }

    /**
     * Registers a new user and returns a JWT token
     * Password is hashed with bcrypt before storing — never saved as plain text
     *
     * @param string $name - display name (already sanitized by controller)
     * @param string $email - email address
     * @param string $password - plain text password (gets hashed here)
     * @return array{token:string,user:User}|array{error:string,code:int}
     */
    public function register(string $name, string $email, string $password): array
    {
        $existing = $this->users->findByEmail($email);
        if ($existing) {
            return ['error' => 'Email already registered', 'code' => 409];
        }

        // GDPR: we only store the minimum data needed (name, email, hashed password, role)
        // No address, phone, or date of birth is collected
        $user           = new User();
        $user->name     = $name;
        $user->email    = $email;
        $user->password = password_hash($password, PASSWORD_DEFAULT);
        $user->role     = 'customer';

        $created = $this->users->create($user);
        unset($created->password);

        return [
            'token' => $this->makeToken($created),
            'user'  => $created,
        ];
    }

    /**
     * Logs in a user and returns a JWT token
     * Uses password_verify() for safe comparison against the stored hash
     *
     * @param string $email
     * @param string $password - plain text to verify
     * @return array{token:string,user:User}|array{error:string,code:int}
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
            'user'  => $user,
        ];
    }

    /**
     * Validates a JWT token and returns the decoded payload
     * Returns null if the token is missing, invalid, or expired
     *
     * @param string|null $token
     * @return array<string, mixed>|null
     */
    public function authenticate(?string $token): ?array
    {
        if (!$token) {
            return null;
        }

        return JwtManager::decode($token);
    }

    /**
     * Creates a signed JWT for a user
     * Token includes user id, email, role, and expires after 24 hours
     *
     * @param User $user
     * @return string - signed JWT
     */
    private function makeToken(User $user): string
    {
        return JwtManager::encode([
            'sub'   => $user->id,
            'email' => $user->email,
            'role'  => $user->role,
            'exp'   => time() + (60 * 60 * 24),
        ]);
    }
}
