<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminArchive extends Model
{
    use HasFactory;

    protected $table = 'admin_archives';
    protected $guarded = [];

    public function role()
    {
        return $this->belongsTo(AdminRole::class, 'role_id');
    }
}
