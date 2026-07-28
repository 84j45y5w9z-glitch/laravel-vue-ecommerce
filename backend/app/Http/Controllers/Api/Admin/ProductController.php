<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        return Product::with(['category', 'inventory'])
            ->latest()
            ->paginate($request->integer('per_page', 20));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'integer', 'min:0'],
            'sku' => ['required', 'string', 'unique:products,sku'],
            'is_active' => ['boolean'],
            'initial_stock' => ['nullable', 'integer', 'min:0'],
            'safety_stock' => ['nullable', 'integer', 'min:0'],
        ]);

        $product = Product::create([
            ...$data,
            'slug' => Str::slug($data['name']) . '-' . Str::random(6),
        ]);

        $product->inventory()->create([
            'quantity' => $data['initial_stock'] ?? 0,
            'safety_stock' => $data['safety_stock'] ?? 0,
        ]);

        return response()->json($product->load('inventory'), 201);
    }

    public function update(Request $request, int $id)
    {
        $product = Product::findOrFail($id);

        $data = $request->validate([
            'category_id' => ['sometimes', 'exists:categories,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $product->update($data);

        return response()->json($product->fresh(['inventory']));
    }

    public function destroy(int $id)
    {
        Product::findOrFail($id)->delete();

        return response()->json(['message' => '已刪除商品']);
    }
}
