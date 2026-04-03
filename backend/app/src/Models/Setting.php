<?php

namespace App\Models;

class Setting
{
    public int $id;
    public string $store_name = '';
    public string $contact_email = '';
    public ?string $updated_at = null;
}
