<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $guarded = [];

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function tags()
    {
        return $this->hasMany(ProductTags::class, 'product_id');
    }


    public function storeImages($images)
    {
        foreach ($images as $image) {
            $imageName = uniqid() . '_' . $image->getClientOriginalName();

            // Specify the destination directory within the public disk
            $destinationPath = public_path('upload/images/products/');

            // Move the uploaded file to the destination directory
            $image->move($destinationPath, $imageName);

            // Return the image path
            $path = 'upload/images/products/' . $imageName;

            // $destinationPath = public_path('upload/images/admin/');
            
            // $path = $image->store($destinationPath, '/public');

            $this->images()->create([
                'name' => $imageName,
                'path' => $path,
                'imageable_id' => $this->id,
                'imageable_type' => get_class($this),
            ]);
        }
    }

    public function deleteImage($imageId)
    {
        $image = $this->images()->find($imageId);

        if ($image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
            return true;
        }

        return false;
    }

    public function getImages()
    {
        return $this->images->map(function ($image) {
            return [
                'id' => $image->id,
                'name' => $image->name,
                'url' => asset($image->path), 
            ];
        });
    }
}
