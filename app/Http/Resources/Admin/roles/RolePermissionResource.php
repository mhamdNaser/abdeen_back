<?php

namespace App\Http\Resources\Admin\roles;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RolePermissionResource extends JsonResource
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
            'roleName' => $this->role->name,
            'permissionName' => $this->permission->name,
            'value' => $this->status,
        ];
    }
}
