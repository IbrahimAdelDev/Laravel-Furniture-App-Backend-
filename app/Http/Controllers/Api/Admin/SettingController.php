<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateWhatsappRequest;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    public function __construct(private SettingService $settingService) {}

    public function updateWhatsapp(UpdateWhatsappRequest $request): JsonResponse
    {
        $setting = $this->settingService->updateWhatsappNumber(
            $request->validated('whatsapp_number')
        );

        return response()->json([
            'message' => 'Whatsapp number updated successfully',
            'data' => [
                'whatsapp_number' => $setting->value
            ]
        ]);
    }
}