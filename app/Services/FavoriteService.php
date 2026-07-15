<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FavoriteService
{
    public function toggleFavorite(User $user, int $productId): array
    {
        return DB::transaction(function () use ($user, $productId) {
            $changes = $user->favorites()->toggle($productId);
            
            $isAdded = count($changes['attached']) > 0;

            return [
                'is_favorited' => $isAdded,
                'message' => $isAdded ? 'Product added to favorites' : 'Product removed from favorites',
            ];
        });
    }

    public function getPaginatedFavorites(User $user, int $perPage = 10): LengthAwarePaginator
    {
        // N+1 problem solved by eager loading the 'images' relationship
        return clone $user->favorites()
                    ->with('images') 
                    ->paginate($perPage);
    }
}