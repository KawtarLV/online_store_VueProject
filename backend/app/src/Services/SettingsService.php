<?php

namespace App\Services;

use App\Models\Setting;
use App\Repositories\ISettingsRepository;
use App\Repositories\SettingsRepository;

/**
 * Handles reading and updating store settings
 * There is only one settings row in the database
 */
class SettingsService implements ISettingsService
{
    private ISettingsRepository $repo;

    /**
     * @param ISettingsRepository|null $repo - optional, allows injecting a mock in tests
     */
    public function __construct(?ISettingsRepository $repo = null)
    {
        $this->repo = $repo ?: new SettingsRepository();
    }

    /**
     * Returns the current store settings
     *
     * @return Setting
     */
    public function get(): Setting
    {
        return $this->repo->get();
    }

    /**
     * Updates store settings with the provided values
     * Only store_name and contact_email are updated; other keys are ignored
     *
     * @param array<string, mixed> $input - pre-sanitized values from the controller
     * @return Setting - the updated settings record
     */
    public function update(array $input): Setting
    {
        $setting                = $this->repo->get();
        $setting->store_name    = trim((string) ($input['store_name']    ?? $setting->store_name));
        $setting->contact_email = trim((string) ($input['contact_email'] ?? $setting->contact_email));

        return $this->repo->update($setting);
    }
}
