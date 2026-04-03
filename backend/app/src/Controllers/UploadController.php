<?php

namespace App\Controllers;

use App\Framework\Controller;

class UploadController extends Controller
{
    public function upload(): void
    {
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $this->sendErrorResponse('File is required', 400);
            return;
        }

        $file = $_FILES['file'];
        $uploadsDir = __DIR__ . '/../../public/uploads';
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0777, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $safeExtension = preg_replace('/[^a-zA-Z0-9]/', '', $extension);
        $fileName = uniqid('img_', true) . ($safeExtension ? '.' . $safeExtension : '');
        $destination = $uploadsDir . '/' . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            $this->sendErrorResponse('Upload failed', 500);
            return;
        }

        $this->sendSuccessResponse(['url' => '/uploads/' . $fileName], 201);
    }
}
