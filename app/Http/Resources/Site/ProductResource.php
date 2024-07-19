<?php

namespace App\Http\Resources\Site;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return [
        //     'id' => $this->id,
        //     'sku' => $this->sku,
        //     'en_name' => $this->en_name,
        //     'ar_name' => $this->ar_name,
        //     'en_description' => $this->en_description,
        //     'ar_description' => $this->ar_description,
        //     'price' => $this->price,
        //     'discount' => $this->discount,
        //     'quantity' => $this->quantity,
        //     'image' => $this->image ?  $this->image : null,
        //     "en_brand" => $this->brand ? $this->brand->en_name : null,
        //     "ar_brand" => $this->brand ? $this->brand->ar_name : null,
        //     "en_category" => $this->category->en_name,
        //     "ar_category" => $this->category->ar_name,
        //     "made_in" => $this->brand ? $this->brand->country->name : null,
        //     "status" => $this->status,
        //     'tags' => $this->tags->map(function ($tag) {
        //         return [
        //             'id' => $this->id,
        //             'en_name' => $this->en_name,
        //             'ar_name' => $this->ar_name,
        //             'en_description' => $this->en_description,
        //             'ar_description' => $this->ar_description,
        //             "attribute" => $this->tag->attribute ? $this->tag->attribute->en_name : null,
        //         ];
        //     }),
        // ];
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'en_name' => $this->en_name,
            'ar_name' => $this->ar_name,
            'en_description' => $this->en_description,
            'ar_description' => $this->ar_description,
            'cost_Price' => $this->cost_Price,
            'public_price' => $this->public_price,
            'discount' => $this->discount,
            'quantity' => $this->quantity,
            'view_num' => $this->view_num,
            'like_num' => $this->like_num,
            'buy_num' => $this->buy_num,
            'image' => $this->image ? $this->image : null,
            "en_brand" => $this->brand ? $this->brand->en_name : null,
            "ar_brand" => $this->brand ? $this->brand->ar_name : null,
            "en_category" => $this->category->en_name,
            "ar_category" => $this->category->ar_name,
            "made_in" => $this->brand ? $this->brand->country->name : null,
            "status" => $this->status,
            'tags' => $this->tags->map(function ($productTag) {
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
