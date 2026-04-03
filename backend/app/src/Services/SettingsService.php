<?php

namespace App\Services;

use App\Models\Setting;
use App\Repositories\ISettingsRepository;
use App\Repositories\SettingsRepository;

class SettingsService implements ISettingsService
{
    private ISettingsRepository $repo;

    public function __construct(?ISettingsRepository $repo = null)
    {
        $this->repo = $repo ?: new SettingsRepository();
    }

    public function get(): Setting
    {
        return $this->repo->get();
    }

    public function update(array $input): Setting
    {
        $setting = $this->repo->get();
        $setting->store_name = trim((string) ($input['store_name'] ?? $setting->store_name));
        $setting->contact_email = trim((string) ($input['contact_email'] ?? $setting->contact_email));

        return $this->repo->update($setting);
    }
}
