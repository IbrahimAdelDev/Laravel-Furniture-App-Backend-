<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    public function getPaginatedProducts(int $perPage = 10, ?string $search = null): LengthAwarePaginator
    {
        return Product::with('images')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($perPage);
    }

    public function getProductDetails(Product $product): Product
    {
        return $product->load('images');
    }

    public function createProduct(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $product = Product::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'price' => $data['price'],
            ]);

            $this->uploadImages($product, $data['images'] ?? []);

            return $product->load('images');
        });
    }

    public function updateProduct(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            $product->update([
                'name' => $data['name'] ?? $product->name,
                'description' => array_key_exists('description', $data) ? $data['description'] : $product->description,
                'price' => $data['price'] ?? $product->price,
            ]);

            if (isset($data['images'])) {
                $this->uploadImages($product, $data['images']);
            }

            return $product->load('images');
        });
    }

    public function deleteProduct(Product $product): void
    {
        DB::transaction(function () use ($product) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_path);
            }
            $product->delete(); 
        });
    }

    public function deleteProductImage(ProductImage $image): void
    {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();
    }

    private function uploadImages(Product $product, array $images): void
{
    foreach ($images as $image) {
        if (!$image->isValid()) {
            throw new \Exception( 'Upload failed: ' . $image->getErrorMessage());
        }

        $path = $image->store('products', 'public');

        if (!$path) {
            throw new \Exception('Failed to store image');
        }

        $product->images()->create(['image_path' => $path]);
    }
}
}