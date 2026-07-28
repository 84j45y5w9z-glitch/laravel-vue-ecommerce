<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'price', 'sku', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    public function availableStock(): int
    {
        if (!$this->inventory) {
            return 0;
        }
        return max(0, $this->inventory->quantity - $this->inventory->reserved);
    }
}
