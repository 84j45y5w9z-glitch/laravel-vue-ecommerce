<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_no' => $this->order_no,
            'status' => $this->status,
            'total_amount' => $this->total_amount,
            'receiver_name' => $this->receiver_name,
            'receiver_phone' => $this->receiver_phone,
            'shipping_address' => $this->shipping_address,
            'items' => $this->whenLoaded('items', fn () => $this->items->map(fn ($item) => [
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'price' => $item->price,
                'quantity' => $item->quantity,
            ])),
            'created_at' => $this->created_at,
        ];
    }
}
