<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Services\CartService;
use App\Http\Requests\Cart\AddItemRequest;
use App\Http\Requests\Cart\UpdateItemRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    public function __construct(private CartService $cartService) {}

    public function index(Request $request)
    {
        $cart = $this->cartService->getUserCart($request->user());
        return response()->json([
            'data' => clone $cart,
            'summary' => [
                'total_items' => $cart->items->sum('quantity'),
                'total_price' => $cart->items->sum(function($item) {
                    return $item->quantity * $item->product->price;
                })
            ]
        ]);
    }

    public function addItem(AddItemRequest $request)
    {
        $this->cartService->addItem($request->user(), $request->validated());
        return response()->json(['message' => 'Item added to cart successfully']);
    }

    public function updateItem(UpdateItemRequest $request, CartItem $cartItem): JsonResponse
    {
        $this->cartService->updateItem($request->user(), $cartItem, $request->validated());
        return response()->json(['message' => 'Item quantity updated successfully']);
    }

    public function removeItem(Request $request, CartItem $cartItem): JsonResponse
    {
        $this->cartService->removeItem($request->user(), $cartItem);
        return response()->json(['message' => 'Item removed from cart successfully']);
    }

    public function checkout(Request $request)
    {
        try {
            $whatsappLink = $this->cartService->generateCheckoutLink($request->user());
            return response()->json([
                'message' => 'Checkout link generated successfully',
                'whatsapp_url' => $whatsappLink
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}