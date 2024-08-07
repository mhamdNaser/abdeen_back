<?php

namespace App\Http\Resources\Admin\category;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandMenuResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'en_name' => $this->en_name,
            'ar_name' => $this->ar_name,
            'en_description' => $this->en_description,
            'ar_description' => $this->ar_description,
            'image' => $this->image,
            'status' => $this->status,
        ];
    }
}
