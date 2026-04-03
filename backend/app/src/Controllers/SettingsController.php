<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Services\IAuthService;
use App\Services\ISettingsService;

/**
 * Handles store settings
 * GET /settings is public, PUT /settings requires admin
 */
class SettingsController extends Controller
{
    private ISettingsService $settings;
    private IAuthService $auth;

    /**
     * @param ISettingsService $settings - injected by the IoC container
     * @param IAuthService $auth - injected by the IoC container
     */
    public function __construct(ISettingsService $settings, IAuthService $auth)
    {
        parent::__construct();
        $this->settings = $settings;
        $this->auth     = $auth;
    }

    /**
     * Returns the current store settings (public route)
     */
    public function get(): void
    {
        $this->sendSuccessResponse($this->settings->get());
    }

    /**
     * Updates store settings (admin only)
     * Sanitizes store_name and contact_email to prevent script injection
     */
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

        // sanitize user-supplied text fields before saving
        if (isset($input['store_name'])) {
            $input['store_name'] = $this->sanitize((string) $input['store_name']);
        }
        if (isset($input['contact_email'])) {
            $input['contact_email'] = $this->sanitize((string) $input['contact_email']);
        }

        $updated = $this->settings->update($input);
        $this->sendSuccessResponse($updated);
    }
}
