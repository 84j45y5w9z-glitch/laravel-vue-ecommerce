<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['3C 電子', '居家生活', '服飾配件', '美妝保養', '運動戶外'];

        foreach ($categories as $name) {
            Category::create([
                'name' => $name,
                'slug' => \Illuminate\Support\Str::slug($name) ?: \Illuminate\Support\Str::random(8),
            ]);
        }
    }
}
