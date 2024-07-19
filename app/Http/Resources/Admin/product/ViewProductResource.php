<?php

namespace App\Http\Resources\Admin\product;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ViewProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'en_name' => $this->en_name,
            'ar_name' => $this->ar_name,
            'en_description' => $this->en_description,
            'ar_description' => $this->ar_description,
            'cost_Price' => $this->cost_Price,
            'public_price' => $this->public_price,
            'discount' => $this->discount,
            'quantity' => $this->quantity,
            'image' => $this->image ?  $this->image : null,
            "brand" => $this->brand_id,
            "category" => $this->category_id,
            "status" => $this->status,
        ];
    }
}
