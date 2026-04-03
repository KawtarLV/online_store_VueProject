<?php

/**
 * This is the central route handler of the application.
 * It uses FastRoute to map URLs to controller methods.
 * 
 * See the documentation for FastRoute for more information: https://github.com/nikic/FastRoute
 */

// CORS headers for localhost requests
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (preg_match('/^https?:\/\/(localhost|127\.0\.0\.1|::1)(:\d+)?$/', $origin)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    // Specifies which HTTP methods are allowed when accessing the resource from the origin
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    // Specifies which HTTP headers can be used when making the actual request
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    // Allows cookies and authentication credentials to be sent with cross-origin requests
    header('Access-Control-Allow-Credentials: true');
    // Specifies how long (in seconds) the browser can cache the preflight response (24 hours)
    header('Access-Control-Max-Age: 86400');
}

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require __DIR__ . '/../vendor/autoload.php';

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

/**
 * Define the routes for the application.
 */
$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    // Product routes
    $r->addRoute('GET', '/products', ['App\Controllers\ProductController', 'getAll']);
    $r->addRoute('GET', '/products/{id}', ['App\Controllers\ProductController', 'get']);
    $r->addRoute('POST', '/products', ['App\Controllers\ProductController', 'create']);
    $r->addRoute('PUT', '/products/{id}', ['App\Controllers\ProductController', 'update']);
    $r->addRoute('DELETE', '/products/{id}', ['App\Controllers\ProductController', 'delete']);

    // Auth
    $r->addRoute('POST', '/login', ['App\Controllers\AuthController', 'login']);
    $r->addRoute('POST', '/register', ['App\Controllers\AuthController', 'register']);

    // Categories
    $r->addRoute('GET', '/categories', ['App\Controllers\CategoryController', 'getAll']);

    // Settings
    $r->addRoute('GET', '/settings', ['App\Controllers\SettingsController', 'get']);
    $r->addRoute('PUT', '/settings', ['App\Controllers\SettingsController', 'update']);

    // Uploads
    $r->addRoute('POST', '/upload', ['App\Controllers\UploadController', 'upload']);

    // Users
    $r->addRoute('GET', '/users', ['App\Controllers\UserController', 'getAll']);
    $r->addRoute('POST', '/users', ['App\Controllers\UserController', 'create']);
    $r->addRoute('DELETE', '/users/{id}', ['App\Controllers\UserController', 'delete']);

    // Orders
    $r->addRoute('GET', '/orders', ['App\Controllers\OrderController', 'getAll']);
    $r->addRoute('GET', '/my-orders', ['App\Controllers\OrderController', 'getMine']);
    $r->addRoute('POST', '/orders', ['App\Controllers\OrderController', 'create']);
});


/**
 * Get the request method and URI from the server variables and invoke the dispatcher.
 */
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = strtok($_SERVER['REQUEST_URI'], '?');
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

/**
 * Switch on the dispatcher result and call the appropriate controller method if found.
 */
switch ($routeInfo[0]) {
    // Handle not found routes
    case FastRoute\Dispatcher::NOT_FOUND:
        header('Content-Type: application/json');
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
        break;
    // Handle routes that were invoked with the wrong HTTP method
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        header('Content-Type: application/json');
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        break;
    // Handle found routes
    case FastRoute\Dispatcher::FOUND:
        $class = $routeInfo[1][0];
        $method = $routeInfo[1][1];
        $controller = new $class();
        $vars = $routeInfo[2];
        $controller->$method($vars);
        break;
}
