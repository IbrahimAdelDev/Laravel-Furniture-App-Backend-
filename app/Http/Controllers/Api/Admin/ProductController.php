<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ProductService;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService) {}

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->createProduct($request->validated());
        
        return response()->json([
            'message' => 'Product created successfully',
            'data' => $product
        ], 201);
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $updatedProduct = $this->productService->updateProduct($product, $request->validated());
        
        return response()->json([
            'message' => 'Product updated successfully',
            'data' => $updatedProduct
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->productService->deleteProduct($product);
        
        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function destroyImage(ProductImage $image): JsonResponse
    {
        $this->productService->deleteProductImage($image);
        
        return response()->json(['message' => 'Product image deleted successfully']);
    }
}