<?php

namespace App\Services;

use App\Repositories\CategoryRepository;
use App\Repositories\ICategoryRepository;

/**
 * Returns product categories
 * Simple pass-through to the repository
 */
class CategoryService implements ICategoryService
{
    private ICategoryRepository $repo;

    /**
     * @param ICategoryRepository|null $repo - optional, allows injecting a mock in tests
     */
    public function __construct(?ICategoryRepository $repo = null)
    {
        $this->repo = $repo ?: new CategoryRepository();
    }

    /**
     * Returns all categories
     *
     * @return \App\Models\Category[]
     */
    public function list(): array
    {
        return $this->repo->all();
    }
}
