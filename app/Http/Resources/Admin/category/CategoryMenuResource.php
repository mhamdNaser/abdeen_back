<?php

namespace App\Http\Resources\Admin\category;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryMenuResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'en_name' => $this->en_name,
            'ar_name' => $this->ar_name,
            'en_description' => $this->en_description,
            'ar_description' => $this->ar_description,
            'parent_id' => $this->parent_id,
            'status' => $this->status,
            'in_menu' => $this->in_menu,
            'image' => $this->image,
            'children' => CategoryMenuResource::collection($this->whenLoaded('children')),
        ];
    }
}
