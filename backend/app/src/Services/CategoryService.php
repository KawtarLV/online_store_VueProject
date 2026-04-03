<?php

namespace App\Services;

use App\Repositories\CategoryRepository;
use App\Repositories\ICategoryRepository;

class CategoryService implements ICategoryService
{
    private ICategoryRepository $repo;

    public function __construct(?ICategoryRepository $repo = null)
    {
        $this->repo = $repo ?: new CategoryRepository();
    }

    public function list(): array
    {
        return $this->repo->all();
    }
}
