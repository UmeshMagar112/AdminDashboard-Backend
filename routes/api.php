<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\{
    CategoryController,
    ProductController,
    OrderController,
    CustomerController,
    CouponController,
    ReviewController,
    InventoryController
};

Route::prefix('admin')->group(function () {
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('coupons', CouponController::class);
    Route::apiResource('reviews', ReviewController::class);
    Route::apiResource('inventories', InventoryController::class);
});