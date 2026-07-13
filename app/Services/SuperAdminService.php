<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Collection;

class SuperAdminService
{
    public function searchCustomers(int $perPage, string $query): Collection
    {
        return User::whereIn('role', ['user', 'admin'])
            ->where(function ($q) use ($query) {
                $q->where('phone', 'like', "%{$query}%")
                  ->orWhere('name', 'like', "%{$query}%");
            })
            ->limit($perPage)
            ->get(['id', 'name', 'phone']);
    }

    public function updateCustomerCredentials(User $customer, array $data): User
    {
        $customer->update([
            'phone' => $data['phone'] ?? $customer->phone,
            'password' => isset($data['password']) ? Hash::make($data['password']) : $customer->password,
        ]);

        $customer->tokens()->delete();

        return $customer;
    }
}