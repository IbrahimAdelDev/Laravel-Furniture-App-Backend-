<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SuperAdminService;
use Illuminate\Http\Request;
use App\Http\Requests\SuperAdmin\UpdateCustomerRequest; 

class CustomerController extends Controller
{
    public function __construct(private SuperAdminService $service) {}

    public function search(Request $request)
    {
        $request->validate(['query' => 'nullable|string']);
        
        $perPage = $request->query('per_page', 10);
        
        $users = $this->service->searchCustomers(
            $perPage, 
            $request->query('query'), 
            $request->user()?->id
        );
        
        return response()->json(['data' => $users]);
    }

    public function updateCredentials(UpdateCustomerRequest $request, User $user)
    {
        if ($user->isSuperAdmin()) {
            return response()->json(['message' => 'Cannot update super admin credentials'], 403);
        }

        $this->service->updateCustomerCredentials($user, $request->validated());
        return response()->json(['message' => 'Customer credentials updated successfully']);
    }
}