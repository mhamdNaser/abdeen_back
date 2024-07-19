<?php

namespace App\Http\Resources\Admin\attribute;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TagsResource extends JsonResource
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
            "attribute" => $this->attribute->en_name,
            'status' => $this->status,
        ];
    }
}
