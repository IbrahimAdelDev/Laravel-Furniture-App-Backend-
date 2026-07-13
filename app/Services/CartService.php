<?php

namespace App\Services;

use App\Models\User;
use App\Models\CartItem;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminNotificationMail;

class CartService
{
    public function getUserCart(User $user)
    {
        // To prevent N+1 query problem, we eager load the product and its images when fetching the cart.
        return $user->cart()->with('items.product.images')->firstOrCreate();
    }

    public function addItem(User $user, array $data)
    {
        $cart = $this->getUserCart($user);
        
        $cartItem = $cart->items()->where('product_id', $data['product_id'])->first();
        
        if ($cartItem) {
            $cartItem->increment('quantity', $data['quantity'] ?? 1);
        } else {
            $cart->items()->create([
                'product_id' => $data['product_id'],
                'quantity' => $data['quantity'] ?? 1,
            ]);
        }
    }

    public function updateItem(User $user, CartItem $cartItem, array $data): void
    {
        $this->verifyItemOwnership($user, $cartItem);
        $cartItem->update(['quantity' => $data['quantity']]);
    }

    public function removeItem(User $user, CartItem $cartItem): void
    {
        $this->verifyItemOwnership($user, $cartItem);
        $cartItem->delete();
    }

    public function generateCheckoutLink(User $user): string
    {
        return DB::transaction(function () use ($user) {
            $cart = $this->getUserCart($user);
            
            if ($cart->items->isEmpty()) {
                throw new \Exception('The cart is empty.');
            }

            $message = "مرحباً، أريد تأكيد هذا الطلب:\n\n";
            $total = 0;

            foreach ($cart->items as $item) {
                $productName = $item->product->name;
                $price = $item->product->price;
                $qty = $item->quantity;
                $subtotal = $price * $qty;
                
                $message .= "- {$productName} | الكمية: {$qty} | السعر: {$subtotal} جنيه\n";
                $total += $subtotal;
            }

            $message .= "\nالإجمالي: {$total} جنيه.";
            $message .= "\nرقم هاتفي: {$user->phone}";

            $cart->items()->delete();

            $adminPhoneSetting = Setting::where('key', 'whatsapp_number')->first();
            $adminPhone = $adminPhoneSetting ? $adminPhoneSetting->value : '201028089643';

            $encodedMessage = urlencode($message);
            
            // Send email notification to admin
            $notificationData = [
                'event' => 'طلب جديد بقيمة ' . $total . ' جنيه',
                'user_name' => $user->name,
                'action_url' => config('app.url')
            ];

            Mail::to('admin@shenawy.com')->send(new AdminNotificationMail($notificationData));

            return "https://wa.me/{$adminPhone}?text={$encodedMessage}";
        });
    }

    private function verifyItemOwnership(User $user, CartItem $cartItem): void
    {
        $cart = $this->getUserCart($user);
        if ($cartItem->cart_id !== $cart->id) {
            throw new AccessDeniedHttpException('You do not have permission to modify this cart item.');
        }
    }
}