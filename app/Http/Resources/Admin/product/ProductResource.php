<?php

namespace App\Http\Resources\Admin\product;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'image' => $this->image ?  $this->image : null,
            "brand" => $this->brand->name,
            "category" => $this->category->name,
            "made_in" => $this->brand->country->name,
            "status" => $this->status,
        ];
    }
}
