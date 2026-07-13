<?php

namespace App\Services;

use App\Models\User;

class ProfileService
{
    public function updateProfile(User $user, array $data): User
    {
        $user->update([
            'name' => $data['name'] ?? $user->name,
            'phone' => $data['phone'] ?? $user->phone,
        ]);

        return $user;
    }
}