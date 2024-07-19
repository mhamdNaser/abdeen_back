<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    
    protected $table = 'categories';
    protected $guarded = [];

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Relationship to get the child categories
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
