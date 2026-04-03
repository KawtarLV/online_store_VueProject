<?php

namespace App\Repositories;

use App\Models\Category;

interface ICategoryRepository
{
    /**
     * @return Category[]
     */
    public function all(): array;
}
