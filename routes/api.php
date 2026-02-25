<?php
// routes/api.php
// This file defines all JSON API endpoints exposed by the application.
// Here we group the admin panel endpoints under /api/admin.

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReviewController;
use Illuminate\Support\Facades\Route;

// ─── Public Auth Routes ───────────────────────────────────────────────────────
// These endpoints do NOT require a token. Used by the frontend login screen.
Route::prefix('admin')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});

// ─── Protected Admin Routes ───────────────────────────────────────────────────
// Everything here is behind Sanctum auth and is consumed by the admin dashboard.
Route::prefix('admin')->middleware(['auth:sanctum'])->group(function () {

    // Auth
    // Used by the frontend to get current admin info and update profile.
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    Route::put('profile', [AuthController::class, 'updateProfile']);

    // Dashboard
    // Main dashboard widgets (revenue, orders, customers, etc.).
    Route::get('dashboard/stats', [DashboardController::class, 'stats']);

    // ── Categories ──────────────────────────────────────────────────────────
    // Standard CRUD + soft-delete/restore operations for product categories.
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
    });

    // ── Products ────────────────────────────────────────────────────────────
    // CRUD for products plus extra routes for toggling status/featured flags
    // and restoring soft-deleted records.
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
    });

    // ── Orders ──────────────────────────────────────────────────────────────
    // Orders created from the storefront or admin. Index/show/update are
    // consumed from the admin panel, status changes are logged in history.
    Route::controller(OrderController::class)->prefix('orders')->group(function () {
        Route::get('', 'index');
        Route::post('', 'store');
        Route::get('{id}', 'show');
        Route::put('{id}', 'update');
        Route::delete('{id}', 'destroy');
        Route::put('{id}/restore', 'restoreTrashed');
    });

    // ── Customers ────────────────────────────────────────────────────────────
    // Customer directory for the admin panel. Users are filtered by "customer"
    // role at the model/query level.
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
    });

    // ── Coupons ──────────────────────────────────────────────────────────────
    // Discount coupon CRUD plus a dedicated validate route the checkout can use.
    Route::controller(CouponController::class)->prefix('coupons')->group(function () {
        Route::get('', 'index');
        Route::post('', 'store');
        Route::post('bulk-delete', 'delete');
        Route::get('validate/{code}', 'validate');  // Check coupon validity
        Route::get('{id}', 'show');
        Route::put('{id}', 'update');
        Route::delete('{id}', 'destroy');
        Route::put('{id}/status', 'changeStatus');
        Route::put('{id}/restore', 'restoreTrashed');
        Route::delete('{id}/force-delete', 'forceDeleteTrashed');
    });

    // ── Reviews ──────────────────────────────────────────────────────────────
    // Customer reviews that can be moderated from the admin UI.
    Route::controller(ReviewController::class)->prefix('reviews')->group(function () {
        Route::get('', 'index');
        Route::post('bulk-delete', 'delete');
        Route::get('{id}', 'show');
        Route::put('{id}', 'update');      // Mainly for approving/rejecting
        Route::delete('{id}', 'destroy');
        Route::put('{id}/status', 'changeStatus');
        Route::put('{id}/restore', 'restoreTrashed');
        Route::delete('{id}/force-delete', 'forceDeleteTrashed');
    });

    // ── Inventory ────────────────────────────────────────────────────────────
    // Per-product / per-variant stock tracking and manual stock adjustments.
    Route::controller(InventoryController::class)->prefix('inventory')->group(function () {
        Route::get('', 'index');
        Route::get('{id}', 'show');
        Route::put('{id}', 'update');
        Route::post('{id}/adjust', 'adjust');  // Stock adjustment
    });
});
