<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /** 取得（或建立）目前使用者的購物車 */
    private function currentCart(Request $request): Cart
    {
        return Cart::firstOrCreate(['user_id' => $request->user()->id]);
    }

    public function index(Request $request)
    {
        $cart = $this->currentCart($request)->load('items.product.inventory');

        return response()->json([
            'items' => $cart->items,
            'total_amount' => $cart->totalAmount(),
        ]);
    }

    public function addItem(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::with('inventory')->findOrFail($data['product_id']);
        $cart = $this->currentCart($request);

        $item = $cart->items()->where('product_id', $product->id)->first();
        $newQuantity = ($item?->quantity ?? 0) + $data['quantity'];

        if ($newQuantity > $product->availableStock()) {
            return response()->json(['message' => '庫存不足'], 422);
        }

        $cart->items()->updateOrCreate(
            ['product_id' => $product->id],
            ['quantity' => $newQuantity]
        );

        return response()->json(['message' => '已加入購物車']);
    }

    public function updateItem(Request $request, int $itemId)
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = $this->currentCart($request);
        $item = $cart->items()->with('product.inventory')->findOrFail($itemId);

        if ($data['quantity'] > $item->product->availableStock()) {
            return response()->json(['message' => '庫存不足'], 422);
        }

        $item->update(['quantity' => $data['quantity']]);

        return response()->json(['message' => '已更新數量']);
    }

    public function removeItem(Request $request, int $itemId)
    {
        $cart = $this->currentCart($request);
        $cart->items()->where('id', $itemId)->delete();

        return response()->json(['message' => '已移除商品']);
    }
}
