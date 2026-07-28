<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $inventories = Inventory::with('product')
            ->when($request->boolean('low_stock_only'), function ($q) {
                $q->whereRaw('(quantity - reserved) <= safety_stock');
            })
            ->paginate($request->integer('per_page', 20));

        return $inventories;
    }

    /** 調整庫存（進貨補貨、盤點修正） */
    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'quantity' => ['sometimes', 'integer', 'min:0'],
            'safety_stock' => ['sometimes', 'integer', 'min:0'],
        ]);

        $inventory = Inventory::findOrFail($id);
        $inventory->update($data);

        return response()->json($inventory->fresh('product'));
    }
}
