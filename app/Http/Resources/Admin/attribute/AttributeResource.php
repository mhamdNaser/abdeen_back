<?php

namespace App\Http\Resources\Admin\attribute;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttributeResource extends JsonResource
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
            'status' => $this->status,
        ];
    }
}
