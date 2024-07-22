<?php

namespace App\Http\Resources\Site;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

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
            "price" => $this->price,
            "tax" => $this->tax,
            "delivery" => $this->delivery,
            'total_price'=> $this->total_price ,
            'status'=> $this->status ,
            'date' => Carbon::parse($this->created_at)->format('d-m-Y'),
        ];
    }
}
