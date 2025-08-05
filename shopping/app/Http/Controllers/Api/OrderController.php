<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Order;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $input = $request->all();
        $user = $request->user()->id;
        try {
            foreach ($input["product_list"] as $key => $value) {
                // calc
                $total = (float) $value['price'] * (int) $value['quantity'];
                Order::create([
                    "user_id" => $user,
                    "product_id" => $value["product_id"],
                    "quantity" => $value["quantity"],
                    "total" => $total
                ]);
            }

            return response()->json([
                "message" => "order checked out",
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "error" => $e->getMessage(),
            ], 200);
        }
    }

    public function getOrder(Request $request)
    {
        $user_id = $request->user()->id;

        $orders = Order::where('user_id', $user_id)->limit(10)
            ->with("orderProduct")
            ->get();
        $count = Order::where('user_id', $user_id)->count();
        $totalPage = $count / 10;
        return response()->json([
            'orders' => $orders,
            'totalPages' => $totalPage,
        ], 200);
    }

    public function getOrderPaginate(Request $request)
    {
        $skip = (int) $request->input('skip');
        $user_id = $request->user()->id;

        $orders = Order::where('user_id', $user_id)
                        ->with('orderProduct')->skip($skip)->take(10)->get();
        return response()->json([
            'orders' => $orders,
        ], 200);
    }
}
