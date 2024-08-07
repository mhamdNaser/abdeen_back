<?php

namespace App\Http\Resources\Admin\Payment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            "name" => $this->name,
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'currency' => $this->currency,
            'locale' => $this->locale,
        ];
    }
}
