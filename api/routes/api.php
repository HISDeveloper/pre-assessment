<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\OrderController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');

    Route::middleware('auth:sanctum')->post('/logout', 'logout');

    Route::get('/email/verify/{id}/{hash}', 'verify')
        ->middleware(['signed'])
        ->name('verification.verify');
});

Route::middleware('auth:sanctum')->controller(ProductController::class)->prefix('product')->group(function () {
    Route::get('/get-all-products', 'getAllProducts');
    Route::get('/get-categories', 'getCategories');
});

Route::middleware('auth:sanctum')->controller(CartController::class)->prefix('cart')->group(function () {
    Route::post('/add-to-cart',  'addToCart');
    Route::get('/get-user-cart',  'viewCart');
    Route::post('/update-quantity/{id}', 'updateQuantity');
    Route::delete('/remove-item/{id}', 'removeItem');
});

Route::middleware('auth:sanctum')->controller(OrderController::class)->prefix('order')->group(function () {
    Route::post('/place-order', 'placeOrder');
    Route::get('/get-order-history', 'orderHistory');
});
