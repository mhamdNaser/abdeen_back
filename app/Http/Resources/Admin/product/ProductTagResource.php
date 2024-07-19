<?php

namespace App\Http\Resources\Admin\product;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductTagResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                    => $this->id,
            'product_id'            => $this->product_id,
            'tag_id'                => $this->tag_id,
            'cost_Price'            => $this->cost_Price,
            'public_price'          => $this->public_price,
            'tag_en_name'           => $this->tag->en_name,
            'tag_ar_name'           => $this->tag->ar_name,
            'tag_en_description'    => $this->tag->en_description,
            'tag_ar_description'    => $this->tag->ar_description,
            'tag_ar_description'    => $this->tag->ar_description,
            "en_attribute"          => $this->tag->attribute->en_name,
            "ar_attribute"          => $this->tag->attribute->ar_name,
        ];
    }
}
