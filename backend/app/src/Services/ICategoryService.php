<?php

namespace App\Services;

use App\Models\Category;

/**
 * Interface for category service operations
 */
interface ICategoryService
{
    /**
     * Returns all product categories
     *
     * @return Category[]
     */
    public function list(): array;
}
