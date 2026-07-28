<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller
{
    public function stats()
    {
        return response()->json([
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', Order::STATUS_PENDING)->count(),
            'paid_orders' => Order::where('status', Order::STATUS_PAID)->count(),
            'total_sales' => Order::whereIn('status', [
                Order::STATUS_PAID, Order::STATUS_PROCESSING, Order::STATUS_SHIPPED, Order::STATUS_COMPLETED,
            ])->sum('total_amount'),
            'low_stock_count' => Inventory::whereRaw('(quantity - reserved) <= safety_stock')->count(),
        ]);
    }
}
