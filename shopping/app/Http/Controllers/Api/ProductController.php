<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function getProduct()
    {
        $product = Product::limit(10)->get();
        $count = Product::count();
        $totalPage = $count / 10;
        return response()->json([
            'products' => $product,
            'totalPages' => $totalPage,
        ], 200);
    }

    public function getProductPaginate(Request $request)
    {
        $skip = (int) $request->input('skip');
        $product = Product::skip($skip)->take(10)->get();
        return response()->json([
            'products' => $product,
        ], 200);
    }
}
