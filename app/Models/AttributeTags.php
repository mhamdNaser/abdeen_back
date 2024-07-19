<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeTags extends Model
{
    use HasFactory;
    protected $table = 'attribute_tags';
    protected $guarded = [];

    public function productTags()
    {
        return $this->hasMany(ProductTags::class, 'tag_id');
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }
}
