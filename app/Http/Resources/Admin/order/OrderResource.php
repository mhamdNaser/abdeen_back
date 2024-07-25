<?php

namespace App\Http\Resources\Admin\order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'total_price'=> $this->total_price ,
            'total_discount'=> $this->total_discount,
            "status" => $this->status,
            'user' => $this->tags->map(function ($productTag) {
                return [
                    'id' => $productTag->tag ? $productTag->tag->id : null,
                    'en_name' => $productTag->tag->en_name,
                    'ar_name' => $productTag->tag->ar_name,
                    'en_description' => $productTag->tag->en_description,
                    'ar_description' => $productTag->tag->ar_description,
                    "attribute" => $productTag->tag->attribute ? $productTag->tag->attribute->en_name : null,
                ];
            }),
        ];
    }
}
