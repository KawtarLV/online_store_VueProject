<?php

namespace App\Services;

use App\Models\Setting;

/**
 * Interface for settings service operations
 */
interface ISettingsService
{
    /**
     * Returns the current store settings
     *
     * @return Setting
     */
    public function get(): Setting;

    /**
     * Updates store settings
     *
     * @param array<string, mixed> $input - pre-sanitized values from the controller
     * @return Setting
     */
    public function update(array $input): Setting;
}
