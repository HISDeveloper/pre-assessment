<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Cart;

use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::all();
        return response()->json([
            'carts' => $cart,
            'message' => 'cart retrieve successful',
        ], 200);
    }
    public function store(Request $request)
    {
        $input = $request->all();
        $cart = Cart::create($input);

        return response()->json([
            'carts' => $cart,
            'message' => 'cart retrieve successful',
        ], 200);
    }

    public function show($id)
    {
        $cart = Cart::find($id);
        if (is_null($cart)) {
            return response()->json([
                'carts' => $cart,
                'message' => 'cart retrieve failed',
            ], 401);
        }
        return response()->json([
            'carts' => $cart,
            'message' => 'cart retrieve successful',
        ], 401);
    }
    // public function update(Request $request)
    // {
    //     $input = $request->all();
    //     $cart = Cart::find($input['id']);
    //     $cart->update([
    //         'quantity'=> $input[''],
    //     ]);
    //     $cart->save();
    // }

    public function destroy(Request $request)
    {
        $cart = Cart::find($request->id);
        $cart->delete();
        return response()->json([
            'message' => 'cart deleted successful',
        ], 200);
    }

    public function getUserCart(Request $request)
    {
        $user_id = $request->user()->id;
        $cart = Cart::where('user_id', $user_id)
            ->with('cartProduct')
            ->get();
        $count = Cart::where('user_id', $user_id)->count();
        $totalPage = $count / 5;
        return response()->json([
            'carts' => $cart,
            'totalPages' => $totalPage,
        ], 200);
    }

    public function addToCart(Request $request)
    {
        //find existing cart item.
        $cart = Cart::where('product_id', $request->product_id)
            ->where('user_id', $request->user()->id)
            ->with('cartProduct')
            ->first();

        if ($cart) {
            $add_qty = $cart->quantity + $request->quantity;
            if ($add_qty > $cart->cartProduct->quantity) {
                $add_qty = $cart->cartProduct->quantity;
            }
            $cart->update([
                'quantity' => $add_qty,
            ]);
            $cart->save();
            return response()->json([
                'carts' => $cart,
                'message' => 'cart added successful',
            ], 200);
        } else {
            $input = $request->all();
            $cart = Cart::create($input);
            return response()->json([
                'carts' => $cart,
                'message' => 'cart added successful',
            ], 200);
        }
    }

    public function removeFromCart(Request $request)
    {
        // $input = $request->all();
        try {
            $validated = $request->validate([
                'cart_id' => 'required|integer|exists:carts,id'
            ]);

            $cart = Cart::findOrFail($validated['cart_id']);
            $cart->delete();
            return response()->json([
                'message' => 'cart removed successful',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }

    }

    public function removeUserCart(Request $request)
    {
        $user_id = $request->user()->id;
        try {
            $cart = Cart::where('user_id', $user_id)->get();
            $cart->each->delete();
            return response()->json([
                'message' => 'cart removed successful',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 200);
        }

    }

    public function getCartPaginate(Request $request)
    {
        $skip = (int) $request->input('skip');

        $product = Cart::skip($skip)->take(10)->get();
        return response()->json([
            'products' => $product,
        ], 200);
    }
}
