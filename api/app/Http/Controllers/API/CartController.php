<?php

namespace App\Http\Controllers\API;

use App\Models\CartItem;
use App\Models\Product;
use App\Traits\RequestResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Http\Controllers\Controller;

class CartController extends Controller
{
    use RequestResponse;

    public function addToCart(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1'
            ]);

            $user = Auth::user();
            if (!$user) {
                return $this->unauthorized("Unauthorized");
            }

            $product = Product::findOrFail($request->product_id);

            if ($product->quantity < $request->quantity) {
                return $this->error("Not enough stock");
            }

            $cartItem = CartItem::where('user_id', $user->id)
                                ->where('product_id', $request->product_id)
                                ->first();

            if ($cartItem) {
                $totalQty = $cartItem->quantity + $request->quantity;
                if ($totalQty > $product->quantity + $cartItem->quantity) {
                    return $this->error("Exceeds available stock");
                }

                $product->quantity -= $request->quantity;
                $cartItem->quantity += $request->quantity;
                $cartItem->save();
            } else {
                $product->quantity -= $request->quantity;
                $cartItem = CartItem::create([
                    'user_id' => $user->id,
                    'product_id' => $request->product_id,
                    'quantity' => $request->quantity,
                ]);
            }

            $product->save();

            return $this->success("Product added to cart", ['item' => $cartItem]);
        } catch (Exception $e) {
            return $this->internalServerError("Failed to add product to cart: " . $e->getMessage());
        }
    }

    public function viewCart()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return $this->unauthorized("Unauthorized");
            }

            $cartItems = CartItem::with('product')->where('user_id', $user->id)->get();
            return $this->success("Cart items retrieved", ['items' => $cartItems]);
        } catch (Exception $e) {
            return $this->internalServerError("Failed to retrieve cart: " . $e->getMessage());
        }
    }

    public function updateQuantity(Request $request, $id)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return $this->unauthorized("Unauthorized");
            }

            $data = $request->validate([
                'quantity' => 'required|integer|min:1'
            ]);

            $cartItem = CartItem::where('user_id', $user->id)->where('id', $id)->first();
            if (!$cartItem) {
                return $this->notFound("Cart item not found");
            }

            $product = Product::find($cartItem->product_id);
            if (!$product) {
                return $this->notFound("Product not found");
            }

            $diff = $data['quantity'] - $cartItem->quantity;

            if ($diff > 0 && $product->quantity < $diff) {
                return $this->error("Not enough stock to increase quantity");
            }

            // Adjust product stock
            $product->quantity -= $diff;
            $product->save();

            $cartItem->quantity = $data['quantity'];
            $cartItem->save();

            return $this->success("Cart updated", ['item' => $cartItem]);
        } catch (Exception $e) {
            return $this->internalServerError("Failed to update cart: " . $e->getMessage());
        }
    }

    public function removeItem($id)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return $this->unauthorized("Unauthorized");
            }

            $cartItem = CartItem::where('user_id', $user->id)->where('id', $id)->first();
            if (!$cartItem) {
                return $this->notFound("Cart item not found");
            }

            // Restore product quantity
            $product = Product::find($cartItem->product_id);
            if ($product) {
                $product->quantity += $cartItem->quantity;
                $product->save();
            }

            $cartItem->delete();
            return $this->success("Item removed from cart");
        } catch (Exception $e) {
            return $this->internalServerError("Failed to remove item from cart: " . $e->getMessage());
        }
    }
}
