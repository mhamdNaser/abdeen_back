<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $table = 'permissions';
    protected $guarded = [];

    public function permissionRoles()
    {
        return $this->hasMany(PermissionRole::class, 'permission_id');
    }

    public function roles()
    {
        return $this->belongsToMany(AdminRole::class, 'permission_roles', 'permission_id', 'role_id')
        ->withPivot('status');
    }
}
