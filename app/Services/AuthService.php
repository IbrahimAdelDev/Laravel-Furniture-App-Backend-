<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function registerUser(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
            ]);

            $user->cart()->create();

            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'user' => $user,
                'token' => $token,
            ];
        });
    }

    public function loginUser(array $data): array
    {
        $user = User::where('phone', $data['phone'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'phone' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function changePassword(User $user, array $data): void
    {
        if (!Hash::check($data['old_password'], $user->password)) {
            throw ValidationException::withMessages([
                'old_password' => ['The provided old password is incorrect.'],
            ]);
        }

        DB::transaction(function () use ($user, $data) {
            $user->update([
                'password' => Hash::make($data['new_password']),
            ]);

            // تسجيل الخروج الإجباري من كل الأجهزة بعد تغيير الباسورد
            $user->tokens()->delete();
        });
    }

    public function logoutUser(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}