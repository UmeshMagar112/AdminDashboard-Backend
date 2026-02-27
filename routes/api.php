<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::post('login', [AuthController::class , 'login']);
});

Route::prefix('admin')->middleware(['auth:sanctum'])->group(function () {

    // Auth
    Route::post('logout', [AuthController::class , 'logout']);
    Route::get('me', [AuthController::class , 'me']);
    Route::put('profile', [AuthController::class , 'updateProfile']);

    // Dashboard
    Route::get('dashboard/stats', [DashboardController::class , 'stats']);

    // Categories 
    Route::controller(CategoryController::class)->prefix('categories')->group(function () {
            Route::get('', 'index');
            Route::post('', 'store');
            Route::post('bulk-delete', 'delete');
            Route::get('{id}', 'show');
            Route::put('{id}', 'update');
            Route::delete('{id}', 'destroy');
            Route::put('{id}/status', 'changeStatus');
            Route::put('{id}/restore', 'restoreTrashed');
            Route::post('restore-all', 'restoreAllTrashed');
            Route::delete('{id}/force-delete', 'forceDeleteTrashed');
        }
        );

        // Products 
        Route::controller(ProductController::class)->prefix('products')->group(function () {
            Route::get('', 'index');
            Route::post('', 'store');
            Route::post('bulk-delete', 'delete');
            Route::get('{id}', 'show');
            Route::put('{id}', 'update');
            Route::delete('{id}', 'destroy');
            Route::put('{id}/status', 'changeStatus');
            Route::put('{id}/featured', 'changeStatusOtherColumn', ['column' => 'is_featured']);
            Route::put('{id}/restore', 'restoreTrashed');
            Route::post('restore-all', 'restoreAllTrashed');
            Route::delete('{id}/force-delete', 'forceDeleteTrashed');
        }
        );

        // Orders 
        Route::controller(OrderController::class)->prefix('orders')->group(function () {
            Route::get('', 'index');
            Route::post('', 'store');
            Route::get('{id}', 'show');
            Route::put('{id}', 'update');
            Route::delete('{id}', 'destroy');
            Route::put('{id}/restore', 'restoreTrashed');
        }
        );

        // Customers
        Route::controller(CustomerController::class)->prefix('customers')->group(function () {
            Route::get('', 'index');
            Route::post('', 'store');
            Route::post('bulk-delete', 'delete');
            Route::get('{id}', 'show');
            Route::put('{id}', 'update');
            Route::delete('{id}', 'destroy');
            Route::put('{id}/status', 'changeStatus');
            Route::put('{id}/restore', 'restoreTrashed');
            Route::post('restore-all', 'restoreAllTrashed');
            Route::delete('{id}/force-delete', 'forceDeleteTrashed');
        }
        );
        // Inventory
        Route::controller(InventoryController::class)->prefix('inventory')->group(function () {
            Route::get('', 'index');
            Route::get('{id}', 'show');
            Route::put('{id}', 'update');
            Route::post('{id}/adjust', 'adjust'); // Stock adjustment
        }
        );    });
