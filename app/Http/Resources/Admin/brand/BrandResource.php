<?php

namespace App\Http\Resources\Admin\brand;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
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
            'en_name' => $this->en_name,
            'ar_name' => $this->ar_name,
            'en_description' => $this->en_description,
            'ar_description' => $this->ar_description,
            'country' => $this->country->name,
            'status' => $this->status,
            'image' => $this->image ?  $this->image : null, // Assuming the image is stored in the public storage
        ];
    }
}
