<?php

use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// routes/api.php

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    // // Other protected API routes for your application
    // Route::apiResource('products', ProductController::class);

    Route::get('/get_products', [ProductController::class, 'getProduct']);
    Route::post('/get_product_paginate', [ProductController::class, 'getProductPaginate']);

    Route::get('/get_user_carts', [CartController::class, 'getUserCart']);
    Route::post('/get_user_cart_paginate', [CartController::class, 'getCartPaginate']);
    Route::post('/add_to_cart', [CartController::class, 'addToCart']);
    Route::delete('/remove_from_cart', [CartController::class, 'removeFromCart']);
    Route::delete('/remove_user_cart', [CartController::class, 'removeUserCart']);

    Route::post('/add_order', [OrderController::class, 'store']);
    Route::get('/get_orders', [OrderController::class, 'getOrder']);
    Route::post('/get_order_paginate', [OrderController::class, 'getOrderPaginate']);
});
