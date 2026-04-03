<?php

namespace App\Models;

class User
{
    public int $id;
    public string $name;
    public string $email;
    public string $password; // hashed
    public string $role = 'customer';
    public ?string $created_at = null;
}
