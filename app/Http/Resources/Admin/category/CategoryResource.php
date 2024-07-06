<?php

namespace App\Http\Resources\Admin\category;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'parent' => $this->parent ? $this->parent->name : null,
            'parent_id' => $this->parent_id,
            'status' => $this->status,
            'in_menu' => $this->in_menu,
        ];
    }
}
