<?php

namespace App\Http\Resources\Admin\order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryResource extends JsonResource
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
            'cost' => $this->cost ?? null,
            'country' => $this->country->name ?? null,
            'state' => $this->state->name ?? null,
            'city' => $this->city->name ?? null,
        ];
    }
}
