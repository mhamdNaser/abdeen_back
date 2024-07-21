<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\product_details\BrandRequest;
use App\Http\Resources\Admin\brand\BrandResource;
use App\Models\Brand;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

use function Symfony\Component\String\b;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cacheDuration = 60; // Cache duration in minutes
        $cacheKey = 'brands_cache';

        $brands = Cache::remember($cacheKey, $cacheDuration, function () {
            return Brand::get();
        });

        $countryCountInDB = Brand::count();

        if ($brands->count() !== $countryCountInDB) {
            $brands = Brand::get();
            Cache::put($cacheKey, $brands, $cacheDuration);
        }

        return BrandResource::collection($brands);
    }

    public function menuBrand()
    {
        $brands = Brand::where("status", 1)->get();

        return BrandResource::collection($brands);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BrandRequest $request)
    {
        // Validate the request
        $validated = $request->validated();

        $brand = Brand::create([
            'en_name' => $validated['en_name'],
            'ar_name' => $validated['ar_name'],
            'en_description' => $validated['en_description'],
            'ar_description' => $validated['ar_description'],
            'country_id' => $validated['country_id'] ?? null,
            'status' => 1,
        ]);

        if ($request->hasFile('image')) {
            $imageName = $validated['en_name'] . uniqid()  . '.' . $validated['image']->getClientOriginalExtension();

            // Specify the destination directory within the public disk
            $destinationPath = public_path('upload/images/brands/');

            // Move the uploaded file to the destination directory
            $validated['image']->move($destinationPath, $imageName);

            // Construct the image path
            $imagePath = 'upload/images/brands/' . $imageName;

            $brand->update([
                'image' => $imagePath
            ]);
        }

        // Save the brand to the database
        $brand->save();

        // Return a response (you can adjust this as needed)
        return response()->json([
            'success' => true,
            'message' => 'Brand created successfully',
            'data' => $brand
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BrandRequest $request, $id)
    {
        $brand = Brand::findOrFail($id);

        $validatedData = $request->validated();

        $updateData = [
            'en_name' => $validatedData['en_name'] ?? $brand->en_name,
            'ar_name' => $validatedData['ar_name'] ?? $brand->ar_name,
            'en_description' => $validatedData['en_description'] ?? $brand->en_description,
            'ar_description' => $validatedData['ar_description'] ?? $brand->ar_description,
            'country_id' => $validatedData['country_id'] == null ? $brand->country_id : $validatedData['country_id'],
            'status' => 1,
        ];

        if ($request->hasFile('image')) {
            $imageName = $validatedData['en_name'] . uniqid() . '.' . $validatedData['image']->getClientOriginalExtension();

            // Specify the destination directory within the public disk
            $destinationPath = public_path('upload/images/brands/');

            // Move the uploaded file to the destination directory
            $validatedData['image']->move($destinationPath, $imageName);

            // Construct the image path
            $imagePath = 'upload/images/brands/' . $imageName;

            // If the category already has an image, delete the old image
            if ($brand->image) {
                $oldImagePath = public_path($brand->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
                $updateData['image'] = $imagePath;
            } else {
                $updateData['image'] = $imagePath;
            }
        }

        $brand->update($updateData);

        // Clear the cache since a role has been updated
        Cache::forget('brands_cache');

        return response()->json([
            'success' => true,
            'message' => '  Brand updated successfully.',
            'data' => $updateData
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return response()->json([
                'success' => false,
                'message' => 'Admin not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'brand' => new BrandResource($brand),
        ], 200);
    }

    public function showbyname($name)
    {
        $brand = Brand::where("en_name", $name)->first();

        if (!$brand) {
            return response()->json([
                'success' => false,
                'message' => 'Admin not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'brand' => $brand,
        ], 200);
    }

    public function changestatus($id)
    {
        $brand = Brand::findOrFail($id);

        // Toggle the status between 1 and 0
        $brand->update([
            'status' => $brand->status == 1 ? 0 : 1,
        ]);

        // Clear the cache since a role status has been updated
        Cache::forget('brands_cache');

        return response()->json([
            'success' => true,
            'message' => 'Brand status updated successfully.',
            "data" => $brand
        ], 200);
    }

    public function destroyarray(Request $request)
    {
        $validatedData = $request->validate([
            'array' => 'required|array',
        ]);

        $idsToDelete = $validatedData['array'];

        DB::beginTransaction();

        try {
            // Fetch brands with IDs matching $idsToDelete
            $brandsToDelete = Brand::whereIn('id', $idsToDelete)->get();

            // Delete images of each brand from storage
            foreach ($brandsToDelete as $brand) {
                if ($brand->image) {
                    $imagePath = public_path($brand->image);
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
                $brand->delete();
            }

            DB::commit();

            // Clear the cache since brands have been deleted
            Cache::forget('brands_cache');

            return response()->json([
                'success' => true,
                'message' => 'Brands deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete Brands.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $brand = Brand::findOrFail($id);

            // Delete images of the brand from storage
            if ($brand->image) {
                $imagePath = public_path($brand->image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $brand->delete();

            DB::commit();

            // Clear the cache since a brand has been deleted
            Cache::forget('brands_cache');

            return response()->json([
                'success' => true,
                'message' => 'Brand deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete brand.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
