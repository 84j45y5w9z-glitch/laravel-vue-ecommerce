<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();

        $samples = [
            ['name' => '無線藍牙耳機', 'price' => 1290],
            ['name' => '機械式鍵盤', 'price' => 2490],
            ['name' => '純棉短袖T恤', 'price' => 590],
            ['name' => '保濕精華液', 'price' => 890],
            ['name' => '瑜珈墊', 'price' => 690],
            ['name' => '行動電源 20000mAh', 'price' => 990],
        ];

        foreach ($samples as $item) {
            $product = Product::create([
                'category_id' => $categories->random()->id,
                'name' => $item['name'],
                'slug' => Str::slug($item['name']) . '-' . Str::random(6),
                'description' => "{$item['name']} — 精選商品，品質保證。",
                'price' => $item['price'],
                'sku' => 'SKU' . Str::upper(Str::random(8)),
                'is_active' => true,
            ]);

            $product->inventory()->create([
                'quantity' => rand(20, 100),
                'safety_stock' => 10,
            ]);
        }
    }
}
