<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminRole extends Model
{
    use HasFactory;

    protected $table = 'admin_roles';
    protected $guarded = [];

    public function permissions()
    {
        return $this->hasManyThrough(Permission::class, PermissionRole::class, 'role_id', 'id', 'id', 'permission_id')
        ->where('permission_roles.status', 1);
    }
}
