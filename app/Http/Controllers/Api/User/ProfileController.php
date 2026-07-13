<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    public function __construct(private ProfileService $profileService) {}

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->profileService->updateProfile($request->user(), $request->validated());

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
            ]
        ]);
    }
}