<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Models\Product;
use App\Services\AuthService;
use App\Services\ProductService;

class ProductController extends Controller
{
    private ProductService $service;
    private AuthService $auth;

    public function __construct()
    {
        parent::__construct();
        $this->service = new ProductService();
        $this->auth = new AuthService();
    }

    public function getAll(): void
    {
        $categoryId = isset($_GET['category']) && $_GET['category'] !== '' ? (int) $_GET['category'] : null;
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $perPage = isset($_GET['per_page']) ? (int) $_GET['per_page'] : 9;

        $this->sendSuccessResponse($this->service->list($categoryId, $page, $perPage));
    }

    /**
     * @param array<string, mixed> $vars
     */
    public function get(array $vars): void
    {
        $id = (int) ($vars['id'] ?? 0);
        $product = $this->service->get($id);
        if (!$product) {
            $this->sendErrorResponse('Product not found', 404);
            return;
        }
        $this->sendSuccessResponse($product);
    }

    public function create(): void
    {
        if (!$this->requireAdmin()) {
            return;
        }

        $product = $this->hydrateProductFromRequest();
        if (!$product) {
            return; // error already sent
        }
        $created = $this->service->create($product);
        $this->sendSuccessResponse($created, 201);
    }

    /**
     * @param array<string, mixed> $vars
     */
    public function update(array $vars): void
    {
        if (!$this->requireAdmin()) {
            return;
        }

        $id = (int) ($vars['id'] ?? 0);
        $product = $this->hydrateProductFromRequest();
        if (!$product) {
            return; // error already sent
        }
        $updated = $this->service->update($id, $product);
        if (!$updated) {
            $this->sendErrorResponse('Product not found', 404);
            return;
        }
        $this->sendSuccessResponse($updated);
    }

    /**
     * @param array<string, mixed> $vars
     */
    public function delete(array $vars): void
    {
        if (!$this->requireAdmin()) {
            return;
        }

        $id = (int) ($vars['id'] ?? 0);
        $deleted = $this->service->delete($id);
        if (!$deleted) {
            $this->sendErrorResponse('Product not found', 404);
            return;
        }
        $this->sendSuccessResponse(['message' => 'Product deleted']);
    }

    /**
     * Build Product from JSON or multipart/form-data.
     */
    private function hydrateProductFromRequest(): ?Product
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (stripos($contentType, 'multipart/form-data') !== false) {
            // If the form sends files, read everything from $_POST and $_FILES.
            $product = new Product();
            $product->name = (string) ($_POST['name'] ?? '');
            $product->description = (string) ($_POST['description'] ?? '');
            $product->price = (float) ($_POST['price'] ?? 0);
            $product->stock = (int) ($_POST['stock'] ?? 0);
            $product->brand = isset($_POST['brand']) ? (string) $_POST['brand'] : null;
            $product->rating = isset($_POST['rating']) ? (float) $_POST['rating'] : 0;
            $product->categoryId = isset($_POST['categoryId']) && $_POST['categoryId'] !== '' ? (int) $_POST['categoryId'] : null;

            $specsRaw = $_POST['specs'] ?? '';
            if ($specsRaw !== '') {
                $decoded = json_decode((string) $specsRaw, true);
                if (!is_array($decoded)) {
                    $this->sendErrorResponse('Invalid specs JSON', 400);
                    return null;
                }
                $product->specs = $decoded;
            }

            // main image
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $product->image = $this->storeUploadedFile($_FILES['image']);
                if ($product->image === null) {
                    $this->sendErrorResponse('Failed to store main image', 500);
                    return null;
                }
            } elseif (isset($_POST['image_url'])) {
                $product->image = (string) $_POST['image_url'];
            }

            // gallery up to 2
            $galleryUrls = [];
            if (isset($_FILES['gallery']) && is_array($_FILES['gallery']['name'])) {
                $count = count($_FILES['gallery']['name']);
                for ($i = 0; $i < $count && count($galleryUrls) < 2; $i++) {
                    if ($_FILES['gallery']['error'][$i] === UPLOAD_ERR_OK) {
                        $fileArr = [
                            'name' => $_FILES['gallery']['name'][$i],
                            'type' => $_FILES['gallery']['type'][$i],
                            'tmp_name' => $_FILES['gallery']['tmp_name'][$i],
                            'error' => $_FILES['gallery']['error'][$i],
                            'size' => $_FILES['gallery']['size'][$i],
                        ];
                        $stored = $this->storeUploadedFile($fileArr);
                        if ($stored) {
                            $galleryUrls[] = $stored;
                        }
                    }
                }
            }
            if (isset($_POST['gallery_urls']) && is_array($_POST['gallery_urls'])) {
                foreach ($_POST['gallery_urls'] as $u) {
                    if (count($galleryUrls) >= 2) {
                        break;
                    }
                    $u = trim((string) $u);
                    if ($u !== '') {
                        $galleryUrls[] = $u;
                    }
                }
            }
            $product->images = $galleryUrls ?: null;

            if ($product->name === '' || $product->description === '') {
                $this->sendErrorResponse('Invalid payload', 400);
                return null;
            }
            return $product;
        }

        // If there are no files, treat the request like normal JSON.
        $product = $this->mapPostDataToClass(Product::class);
        if (!$product) {
            $this->sendErrorResponse('Invalid payload', 400);
            return null;
        }
        return $product;
    }

    /**
     * Save uploaded file to public/uploads and return its public URL path.
     *
     * @param array{name:string,type:string,tmp_name:string,error:int,size:int} $file
     */
    private function storeUploadedFile(array $file): ?string
    {
        $uploadsDir = __DIR__ . '/../../public/uploads';
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0777, true);
        }
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $safeExt = preg_replace('/[^a-zA-Z0-9]/', '', $ext);
        $filename = uniqid('img_', true) . ($safeExt ? '.' . $safeExt : '');
        $dest = $uploadsDir . '/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return null;
        }
        return '/uploads/' . $filename;
    }

    private function requireAdmin(): bool
    {
        $payload = $this->auth->authenticate($this->getBearerToken());
        if (!$payload || ($payload['role'] ?? '') !== 'admin') {
            $this->sendErrorResponse('Admin token required', 403);
            return false;
        }

        return true;
    }
}
