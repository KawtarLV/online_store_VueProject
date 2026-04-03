<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    private CategoryService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new CategoryService();
    }

    public function getAll(): void
    {
        $this->sendSuccessResponse($this->service->list());
    }
}
