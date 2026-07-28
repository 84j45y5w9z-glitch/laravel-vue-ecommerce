<?php

use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\InventoryController;
use App\Http\Controllers\Api\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

// ---------- 公開路由 ----------
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{slug}', [ProductController::class, 'show']);

// 金流 Server 端通知（webhook），不可加驗證middleware，由簽章驗證取代
Route::post('/payments/ecpay/notify', [PaymentController::class, 'notifyEcpay']);
Route::post('/payments/newebpay/notify', [PaymentController::class, 'notifyNewebpay']);

// ---------- 需登入（Sanctum）----------
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/items', [CartController::class, 'addItem']);
    Route::patch('/cart/items/{itemId}', [CartController::class, 'updateItem']);
    Route::delete('/cart/items/{itemId}', [CartController::class, 'removeItem']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders/{orderId}/pay', [PaymentController::class, 'pay']);

    // ---------- 需管理員（Sanctum + admin）----------
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

        Route::apiResource('products', AdminProductController::class);

        Route::get('/orders', [AdminOrderController::class, 'index']);
        Route::get('/orders/{id}', [AdminOrderController::class, 'show']);
        Route::patch('/orders/{id}/status', [AdminOrderController::class, 'updateStatus']);

        Route::get('/inventories', [InventoryController::class, 'index']);
        Route::patch('/inventories/{id}', [InventoryController::class, 'update']);
    });
});
