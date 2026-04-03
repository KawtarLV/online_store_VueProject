<?php

namespace App\Repositories;

use App\Models\Setting;

interface ISettingsRepository
{
    public function get(): Setting;

    public function update(Setting $setting): Setting;
}
