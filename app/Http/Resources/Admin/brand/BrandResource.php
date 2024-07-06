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
            'name' => $this->name,
            'description' => $this->description,
            'country' => $this->country->name,
            'status' => $this->status,
            'image' => $this->image ?  $this->image : null, // Assuming the image is stored in the public storage
        ];
    }
}
