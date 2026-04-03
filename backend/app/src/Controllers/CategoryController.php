<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Services\ICategoryService;

/**
 * Returns product categories
 * Route: GET /categories (public)
 */
class CategoryController extends Controller
{
    private ICategoryService $service;

    /**
     * @param ICategoryService $service - injected by the IoC container
     */
    public function __construct(ICategoryService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * Returns all categories
     */
    public function getAll(): void
    {
        $this->sendSuccessResponse($this->service->list());
    }
}
