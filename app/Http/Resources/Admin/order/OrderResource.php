<?php

namespace App\Http\Resources\Admin\order;

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
            'user_id' => $this->user_id,
            'user_name' => $this->user->first_name . " " . $this->user->medium_name . " " . $this->user->last_name,
            'status' => $this->status,
            'price' => $this->price,
            'total_price' => $this->total_price,
            'total_discount' => $this->total_discount,
            'tax' => $this->tax,
            'delivery' => $this->delivery,
            'created_at' => Carbon::parse($this->created_at)->format('d-m-Y'),
            'products' => $this->orderProducts->map(function ($orderProduct) {
                return [
                    'id' => $orderProduct->id,
                    'image' => $orderProduct->product->image,
                    'sku' => $orderProduct->product->sku,
                    'en_name' => $orderProduct->product->en_name,
                    'ar_name' => $orderProduct->product->ar_name,
                    'product_price' => $orderProduct->product->public_price,
                    'tag_id' => $orderProduct->tag_id,
                    'quantity' => $orderProduct->quantity,
                    'price' => $orderProduct->price,
                    'discount' => $orderProduct->discount,
                    'created_at' => Carbon::parse($this->created_at)->format('d-m-Y'),
                ];
            }),
            "address" => isset($this->orderAddress) && $this->orderAddress->isNotEmpty() ? $this->orderAddress->map(function ($orderAddres) {
                return [
                    'id' => isset($orderAddres->id) ? $orderAddres->id : null,
                    'country' => isset($orderAddres->country) ? $orderAddres->country->name : null,
                    'state' => isset($orderAddres->state) ? $orderAddres->state->name : null,
                    'city' => isset($orderAddres->city) ? $orderAddres->city->name : null,
                    'address_1' => isset($orderAddres->address_1) ? $orderAddres->address_1 : null,
                    'address_2' => isset($orderAddres->address_2) ? $orderAddres->address_2 : null,
                    'address_3' => isset($orderAddres->address_3) ? $orderAddres->address_3 : null,
                ];
            }) : null,
            'payment' => $this->payment ? [
                'payment_id' => $this->payment->payment_id,
                'payment_method' => $this->payment->payment_method,
                'transaction_id' => $this->payment->transaction_id,
            ] : null,
        ];
    }
}
