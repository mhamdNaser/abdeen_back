<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Model
{
    use HasFactory, Notifiable, HasApiTokens;


    protected $table = 'admins';
    protected $guarded = [];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function role()
    {
        return $this->belongsTo(AdminRole::class, 'role_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function city()
    {
        return $this->belongsTo(city::class, 'city_id');
    }

    public function permissions()
    {
        return $this->role ? $this->role->permissions() : collect();
    }
}
