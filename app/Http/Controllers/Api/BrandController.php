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

    /**
     * Store a newly created resource in storage.
     */
    public function store(BrandRequest $request)
    {
        // Validate the request
        $validated = $request->validated();

        $brand = Brand::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'country_id' => $validated['country_id'] ?? null,
            'status' => 1,
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $this->uploadImage($request->file('image'));

            // Create image record
            $image = new Image();
            $image->name = $imagePath;
            $brand->images()->save($image);

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
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(BrandRequest $request, $id)
    {
        $brand = Brand::findOrFail($id);

        $validatedData = $request->validated();

        $updateData = [
            'name' => $validatedData['name'] ?? $brand->name,
            'description' => $validatedData['description'] ?? $brand->description,
            'country_id' => $validatedData['country_id'] == null ? $brand->country_id : $validatedData['country_id'],
            'status' => 1,
        ];

        if ($request->hasFile('image')) {
            $imagePath = $this->uploadImage($request->file('image'));
            $updateData['image'] = $imagePath;
        }else{
            $updateData['image'] = $brand->image;
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
            // Fetch admins with IDs matching $idsToDelete
            $categoriesToTable = Brand::whereIn('id', $idsToDelete)->get();

            // Move admins to AdminArchive and delete from Admin
            foreach ($categoriesToTable as $category) {
                $category->delete();
            }

            DB::commit();

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
        $brand =  Brand::findOrFail($id);
        $brand->delete();

        // Clear the cache since a role has been deleted
        Cache::forget('brands_cache');

        return response()->json([
            'success' => true,
            'message' => 'Brand deleted successfully.'
        ], 200);
    }

    private function uploadImage($imageFile)
    {
        // Generate unique image name
        $imageName = uniqid() . '_' . $imageFile->getClientOriginalName();

        // Specify the destination directory within the public disk
        $destinationPath = public_path('upload/images/brand/');

        // Move the uploaded file to the destination directory
        $imageFile->move($destinationPath, $imageName);

        // Return the image path
        return 'upload/images/brand/' . $imageName;
    }
}
