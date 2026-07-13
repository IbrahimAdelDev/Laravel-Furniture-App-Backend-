<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductService;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService) {}

    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $perPage = $request->query('per_page', 10); 
        
        $products = $this->productService->getPaginatedProducts($perPage, $search);

        return response()->json([
            'data' => ProductResource::collection($products),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'total' => $products->total(),
                'has_more' => $products->hasMorePages(),
            ]
        ]);
    }

    public function show(Product $product): JsonResponse
    {
        $productDetails = $this->productService->getProductDetails($product);
        return response()->json(['data' => $productDetails]);
    }
}