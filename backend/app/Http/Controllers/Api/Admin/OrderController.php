<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['items', 'user'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        return OrderResource::collection($query->paginate($request->integer('per_page', 20)));
    }

    public function show(int $id)
    {
        return new OrderResource(Order::with(['items', 'user', 'payments'])->findOrFail($id));
    }

    public function updateStatus(Request $request, int $id)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,paid,processing,shipped,completed,cancelled'],
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $data['status']]);

        return new OrderResource($order->fresh('items'));
    }
}
