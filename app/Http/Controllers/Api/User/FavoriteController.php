<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ToggleFavoriteRequest;
use App\Services\FavoriteService;
use App\Http\Resources\ProductResource; 
use Illuminate\Http\Request;


class FavoriteController extends Controller
{
    public function __construct(private FavoriteService $favoriteService)
    {
    }

    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        
        $favorites = $this->favoriteService->getPaginatedFavorites($request->user(), $perPage);
        
        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($favorites)
        ]);
    }

    public function toggle(ToggleFavoriteRequest $request)
    {
        $result = $this->favoriteService->toggleFavorite(
            $request->user(),
            $request->validated('product_id')
        );

        return response()->json([
            'success' => true,
            'is_favorited' => $result['is_favorited'],
            'message' => $result['message']
        ]);
    }
}