<?php

namespace App\Http\Controllers\API;

use App\Models\Order;
use App\Models\CartItem;
use App\Models\OrderItem;
use App\Traits\RequestResponse;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    use RequestResponse;

    public function placeOrder()
    {
        try {
            $user = Auth::user();
            $cart = CartItem::with('product')->where('user_id', $user->id)->get();

            if ($cart->isEmpty()) {
                return $this->error("Cart is empty");
            }

            $total = 0;
            foreach ($cart as $item) {
                $total += $item->product->price * $item->quantity;
            }

            $order = Order::create([
                'user_id' => $user->id,
                'total_price' => $total
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);
            }

            CartItem::where('user_id', $user->id)->delete();
            return $this->success("Order placed successfully", ['order_id' => $order->id]);
        } catch (Exception $e) {
            return $this->internalServerError("Failed to place order: " . $e->getMessage());
        }        
    }

    public function orderHistory()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return $this->unauthorized("Unauthorized");
            }

            $orders = Order::with('orderItems.product')->where('user_id', $user->id)->get();
            
            return $this->success("Order history retrieved", ['orders' => $orders]);
        } catch (Exception $e) {
            return $this->internalServerError("Failed to retrieve order history: " . $e->getMessage());
        }
    }

}
