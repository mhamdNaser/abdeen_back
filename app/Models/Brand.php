<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $table = 'brands';
    protected $guarded = [];

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'brand_category');
    }
}
