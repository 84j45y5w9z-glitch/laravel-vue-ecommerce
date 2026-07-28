<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = $request->user()->orders()->with('items')->latest()->paginate(10);

        return OrderResource::collection($orders);
    }

    public function show(Request $request, int $id)
    {
        $order = $request->user()->orders()->with('items')->findOrFail($id);

        return new OrderResource($order);
    }

    /**
     * 從目前購物車結帳，建立訂單並「預留」庫存（reserved），
     * 尚未真正扣庫存，等收到金流付款成功通知後才正式出貨扣庫存。
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'receiver_name' => ['required', 'string', 'max:255'],
            'receiver_phone' => ['required', 'string', 'max:50'],
            'shipping_address' => ['required', 'string', 'max:255'],
        ]);

        $cart = Cart::where('user_id', $request->user()->id)
            ->with('items.product.inventory')
            ->firstOrFail();

        if ($cart->items->isEmpty()) {
            return response()->json(['message' => '購物車是空的'], 422);
        }

        $order = DB::transaction(function () use ($cart, $data, $request) {
            foreach ($cart->items as $item) {
                $stock = $item->product->availableStock();
                if ($item->quantity > $stock) {
                    throw new \RuntimeException("「{$item->product->name}」庫存不足");
                }
            }

            $order = Order::create([
                'order_no' => 'ORD' . now()->format('YmdHis') . Str::upper(Str::random(4)),
                'user_id' => $request->user()->id,
                'total_amount' => $cart->totalAmount(),
                'status' => Order::STATUS_PENDING,
                'receiver_name' => $data['receiver_name'],
                'receiver_phone' => $data['receiver_phone'],
                'shipping_address' => $data['shipping_address'],
            ]);

            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                ]);

                // 預留庫存，避免下單後、付款前被其他人搶購一空
                $item->product->inventory()->increment('reserved', $item->quantity);
            }

            $cart->items()->delete();

            return $order;
        });

        return new OrderResource($order->load('items'));
    }
}
