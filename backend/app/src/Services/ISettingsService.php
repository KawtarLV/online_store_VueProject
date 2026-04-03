<?php

namespace App\Services;

use App\Models\Setting;

interface ISettingsService
{
    public function get(): Setting;

    public function update(array $input): Setting;
}
