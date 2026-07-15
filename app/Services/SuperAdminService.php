<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Collection;

class SuperAdminService
{
    public function searchCustomers(int $perPage, ?string $query = null, ?int $currentUserId = null): Collection
    {
        return User::whereIn('role', ['user', 'admin'])
            ->when($currentUserId, function ($q) use ($currentUserId) {
                $q->where('id', '!=', $currentUserId);
            })
            ->when(!empty($query), function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('phone', 'like', "%{$query}%")
                        ->orWhere('name', 'like', "%{$query}%");
                });
            })
            ->limit($perPage)
            ->get(['id', 'name', 'phone']);
    }

    public function updateCustomerCredentials(User $customer, array $data): User
    {
        if ($customer->isSuperAdmin()) {
            throw new \Exception('Cannot update super admin credentials');
        }
        
        if (isset($data['is_admin'])) {
            $data['role'] = $data['is_admin'] == 1 ? 'admin' : 'user';
        }
        
        $customer->update([
            'phone' => $data['phone'] ?? $customer->phone,
            'password' => isset($data['password']) ? Hash::make($data['password']) : $customer->password,
            'role' => $data['role'] ?? $customer->role,
        ]);

        $customer->tokens()->delete();

        return $customer;
    }
}