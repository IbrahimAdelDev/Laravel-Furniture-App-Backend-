<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Public\ProductController as PublicProductController;
use App\Http\Controllers\Api\User\CartController;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\SuperAdmin\CustomerController;
use App\Http\Controllers\Api\Admin\SettingController;
use App\Http\Controllers\Api\User\ProfileController;
use App\Http\Controllers\Api\User\FavoriteController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::put('/user/profile', [ProfileController::class, 'update'])->middleware('auth:sanctum');;

Route::get('/products', [PublicProductController::class, 'index']);
Route::get('/products/{product}', [PublicProductController::class, 'show']);

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
    });

    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/items', [CartController::class, 'addItem']);
        Route::put('/items/{cartItem}', [CartController::class, 'updateItem']);
        Route::delete('/items/{cartItem}', [CartController::class, 'removeItem']);
        Route::post('/checkout', [CartController::class, 'checkout']);
    });

    Route::prefix('favorites')->group(function () {
        Route::get('/', [FavoriteController::class, 'index']);
        Route::post('/toggle', [FavoriteController::class, 'toggle']);
    });

    Route::middleware('role:admin,super_admin')->prefix('admin/products')->group(function () {
        Route::post('/', [AdminProductController::class, 'store']);
        Route::post('/{product}', [AdminProductController::class, 'update']); // method changed from put to post for update
        Route::delete('/{product}', [AdminProductController::class, 'destroy']);
        Route::delete('/images/{image}', [AdminProductController::class, 'destroyImage']);
    });

    Route::middleware('role:admin,super_admin')->prefix('admin/settings')->group(function () {
        Route::post('/whatsapp', [SettingController::class, 'updateWhatsapp']);
    });

    Route::middleware('role:super_admin')->prefix('super-admin/users')->group(function () {
        Route::get('/search', [CustomerController::class, 'search']);
        Route::put('/{user}', [CustomerController::class, 'updateCredentials']);
    });
});