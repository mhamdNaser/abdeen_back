<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LandPageImage;
use Illuminate\Http\Request;

class LandPageImageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'primary_image_1' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'secondary_image_1' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'secondary_image_2' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Fetch the first record or create a new instance
        $image = LandPageImage::first();

        if (!$image) {
            $image = new LandPageImage;
        }

        // Handling primary images
        if ($request->hasFile('primary_image_1')) {
            // Delete the old image if it exists
            if ($image->primary_image_1 && file_exists(public_path($image->primary_image_1))) {
                unlink(public_path($image->primary_image_1));
            }

            $file = $request->file('primary_image_1');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/landPage'), $fileName);
            $image->primary_image_1 = 'images/landPage/' . $fileName;
        }

        // Handling secondary images
        if ($request->hasFile('secondary_image_1')) {
            // Delete the old image if it exists
            if ($image->secondary_image_1 && file_exists(public_path($image->secondary_image_1))) {
                unlink(public_path($image->secondary_image_1));
            }

            $file = $request->file('secondary_image_1');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/landPage'), $fileName);
            $image->secondary_image_1 = 'images/landPage/' . $fileName;
        }

        if ($request->hasFile('secondary_image_2')) {
            // Delete the old image if it exists
            if ($image->secondary_image_2 && file_exists(public_path($image->secondary_image_2))) {
                unlink(public_path($image->secondary_image_2));
            }

            $file = $request->file('secondary_image_2');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/landPage'), $fileName);
            $image->secondary_image_2 = 'images/landPage/' . $fileName;
        }

        $image->save();

        return response()->json(['message' => 'Images saved successfully!']);
    }


    public function getImageSite(){
        $image = LandPageImage::first();

        // Prepare the response data
        $data = [
            'primary_image_1' => $image ? $image->primary_image_1 : null,
            'secondary_image_1' => $image ? $image->secondary_image_1 : null,
            'secondary_image_2' => $image ? $image->secondary_image_2 : null,
        ];

        // Return the response as JSON
        return response()->json($data);
    }
}
