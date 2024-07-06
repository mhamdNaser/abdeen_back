<?php

namespace App\Http\Resources\Admin\admins;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminArchivesResource extends JsonResource
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
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone,
            'image' => $this->image,
            'name' => $this->first_name . " " . $this->medium_name . " " . $this->last_name,
            'role' => $this->role ? $this->role : null, 
        ];
    }
}
