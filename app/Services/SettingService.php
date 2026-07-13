<?php

namespace App\Services;

use App\Models\Setting;

class SettingService
{
    public function updateWhatsappNumber(string $whatsappNumber): Setting
    {
        return Setting::updateOrCreate(
            ['key' => 'whatsapp_number'],
            ['value' => $whatsappNumber]
        );
    }
}