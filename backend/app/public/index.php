<?php

/**
 * Application entry point
 *
 * This file:
 * 1. Sets CORS headers so the Vue frontend can call the API
 * 2. Sets up the IoC container and registers all dependencies
 * 3. Uses FastRoute to map URLs to the right controller method
 */

// Allow requests from localhost (needed for local development with the Vue frontend)
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (preg_match('/^https?:\/\/(localhost|127\.0\.0\.1|::1)(:\d+)?$/', $origin)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
}

// Handle preflight OPTIONS requests (browsers send these before cross-origin requests)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require __DIR__ . '/../vendor/autoload.php';

use App\Controllers\AuthController;
use App\Controllers\CategoryController;
use App\Controllers\OrderController;
use App\Controllers\ProductController;
use App\Controllers\SettingsController;
use App\Controllers\UploadController;
use App\Controllers\UserController;
use App\Framework\Container;
use App\Repositories\CategoryRepository;
use App\Repositories\ICategoryRepository;
use App\Repositories\IOrderRepository;
use App\Repositories\IProductRepository;
use App\Repositories\ISettingsRepository;
use App\Repositories\IUserRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Repositories\SettingsRepository;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Services\CategoryService;
use App\Services\IAuthService;
use App\Services\ICategoryService;
use App\Services\IOrderService;
use App\Services\IProductService;
use App\Services\ISettingsService;
use App\Services\OrderService;
use App\Services\ProductService;
use App\Services\SettingsService;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

// ---------------------------------------------------------------------------
// IoC Container setup
//
// Instead of each controller creating its own dependencies with "new",
// we register everything here and inject them through constructors.
// This makes the code more testable and keeps controllers decoupled
// from concrete implementations.
// ---------------------------------------------------------------------------

$container = new Container();

// Repositories — bind each interface to its concrete implementation
$container->bind(IUserRepository::class,     fn()  => new UserRepository());
$container->bind(IProductRepository::class,  fn()  => new ProductRepository());
$container->bind(IOrderRepository::class,    fn()  => new OrderRepository());
$container->bind(ICategoryRepository::class, fn()  => new CategoryRepository());
$container->bind(ISettingsRepository::class, fn()  => new SettingsRepository());

// Services — receive their repository through constructor injection
$container->bind(IAuthService::class,     fn($c) => new AuthService($c->make(IUserRepository::class)));
$container->bind(IProductService::class,  fn($c) => new ProductService($c->make(IProductRepository::class)));
$container->bind(IOrderService::class,    fn($c) => new OrderService($c->make(IOrderRepository::class)));
$container->bind(ICategoryService::class, fn($c) => new CategoryService($c->make(ICategoryRepository::class)));
$container->bind(ISettingsService::class, fn($c) => new SettingsService($c->make(ISettingsRepository::class)));

// Controllers — receive their services through constructor injection
$container->bind(AuthController::class,     fn($c) => new AuthController($c->make(IAuthService::class)));
$container->bind(ProductController::class,  fn($c) => new ProductController($c->make(IProductService::class), $c->make(IAuthService::class)));
$container->bind(OrderController::class,    fn($c) => new OrderController($c->make(IOrderService::class), $c->make(IAuthService::class)));
$container->bind(UserController::class,     fn($c) => new UserController($c->make(IUserRepository::class), $c->make(IAuthService::class)));
$container->bind(CategoryController::class, fn($c) => new CategoryController($c->make(ICategoryService::class)));
$container->bind(SettingsController::class, fn($c) => new SettingsController($c->make(ISettingsService::class), $c->make(IAuthService::class)));
$container->bind(UploadController::class,   fn()   => new UploadController());

// ---------------------------------------------------------------------------
// Routes — map HTTP method + URL to [ControllerClass, methodName]
// ---------------------------------------------------------------------------

$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    // Products
    $r->addRoute('GET',    '/products',      [ProductController::class,  'getAll']);
    $r->addRoute('GET',    '/products/{id}', [ProductController::class,  'get']);
    $r->addRoute('POST',   '/products',      [ProductController::class,  'create']);
    $r->addRoute('PUT',    '/products/{id}', [ProductController::class,  'update']);
    $r->addRoute('DELETE', '/products/{id}', [ProductController::class,  'delete']);

    // Authentication
    $r->addRoute('POST', '/login',    [AuthController::class, 'login']);
    $r->addRoute('POST', '/register', [AuthController::class, 'register']);

    // Categories
    $r->addRoute('GET', '/categories', [CategoryController::class, 'getAll']);

    // Settings
    $r->addRoute('GET', '/settings', [SettingsController::class, 'get']);
    $r->addRoute('PUT', '/settings', [SettingsController::class, 'update']);

    // File uploads
    $r->addRoute('POST', '/upload', [UploadController::class, 'upload']);

    // Users (admin only)
    $r->addRoute('GET',    '/users',      [UserController::class, 'getAll']);
    $r->addRoute('POST',   '/users',      [UserController::class, 'create']);
    $r->addRoute('DELETE', '/users/{id}', [UserController::class, 'delete']);

    // Orders
    $r->addRoute('GET',  '/orders',    [OrderController::class, 'getAll']);
    $r->addRoute('GET',  '/my-orders', [OrderController::class, 'getMine']);
    $r->addRoute('POST', '/orders',    [OrderController::class, 'create']);
});

// ---------------------------------------------------------------------------
// Dispatch — resolve the matched controller from the container and call it
// ---------------------------------------------------------------------------

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri        = strtok($_SERVER['REQUEST_URI'], '?');
$routeInfo  = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        header('Content-Type: application/json');
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        header('Content-Type: application/json');
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        break;

    case FastRoute\Dispatcher::FOUND:
        [$class, $method] = $routeInfo[1];
        $vars             = $routeInfo[2];
        // Resolve the controller through the container (this injects all dependencies)
        $controller = $container->make($class);
        $controller->$method($vars);
        break;
}
