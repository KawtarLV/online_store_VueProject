<?php

namespace App\Services;

use App\Models\Category;

interface ICategoryService
{
    /**
     * @return Category[]
     */
    public function list(): array;
}
