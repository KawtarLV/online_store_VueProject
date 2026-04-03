<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Services\AuthService;
use App\Services\SettingsService;

class SettingsController extends Controller
{
    private SettingsService $settings;
    private AuthService $auth;

    public function __construct()
    {
        parent::__construct();
        $this->settings = new SettingsService();
        $this->auth = new AuthService();
    }

    public function get(): void
    {
        $this->sendSuccessResponse($this->settings->get());
    }

    public function update(): void
    {
        $payload = $this->auth->authenticate($this->getBearerToken());
        if (!$payload || ($payload['role'] ?? '') !== 'admin') {
            $this->sendErrorResponse('Admin token required', 403);
            return;
        }

        $input = $this->getJsonBody();
        if ($input === null) {
            $this->sendErrorResponse('Invalid JSON body', 400);
            return;
        }

        $updated = $this->settings->update($input);
        $this->sendSuccessResponse($updated);
    }
}
