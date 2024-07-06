<?php

namespace App\Http\Resources\Admin\admins;

use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    public static $wrap = false;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone,
            'image' => $this->image,
            'status' => $this->status,
            'name' => $this->first_name . " " . $this->medium_name . " " . $this->last_name,
            'role' => $this->role ? $this->role : null,
            'permission' => $this->permissions->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'permissionName' => $permission->name,
                    'permissionStatus' => $permission->status,
                ];
            })
        ];
    }
}
