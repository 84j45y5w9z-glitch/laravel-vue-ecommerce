<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = ['product_id', 'quantity', 'reserved', 'safety_stock'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function isLowStock(): bool
    {
        return ($this->quantity - $this->reserved) <= $this->safety_stock;
    }
}
