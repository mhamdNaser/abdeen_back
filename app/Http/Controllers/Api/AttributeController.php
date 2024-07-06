<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\product_details\AttributeRequest;
use App\Http\Resources\Admin\attribute\AttributeResource;
use App\Models\Attribute;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cacheDuration = 60; // Cache duration in minutes
        $cacheKey = 'attribute_cache';

        $attribute = Cache::remember($cacheKey, $cacheDuration, function () {
            return Attribute::all();
        });

        $countryCountInDB = Attribute::count();

        if ($attribute->count() !== $countryCountInDB) {
            $attribute = Attribute::all();
            Cache::put($cacheKey, $attribute, $cacheDuration);
        }

        return AttributeResource::collection($attribute);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AttributeRequest $request)
    {
        $validatedData = $request->validated();

        // Create a new resource using the validated data
        $resource = new Attribute();
        $resource->name = $validatedData['name'];
        $resource->status = 1;

        // Save the resource to the database
        $resource->save();

        // Return a response indicating the resource was created
        return response()->json([
            'message' => 'Resource created successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Attribute $attribute)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attribute $attribute)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AttributeRequest $request, $id)
    {
        $attribute = Attribute::findOrFail($id);

        $validatedData = $request->validated();

        $attribute->update([
            'name' => $validatedData['name'],
            'status' => 1,
        ]);

        // Clear the cache since a role has been updated
        Cache::forget('attribute_cache');

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully.'
        ], 200);
    }

    public function changestatus($id)
    {
        $attribute = Attribute::findOrFail($id);

        // Toggle the status between 1 and 0
        $attribute->update([
            'status' => $attribute->status == 1 ? 0 : 1,
        ]);

        // Clear the cache since a role status has been updated
        Cache::forget('attribute_cache');

        return response()->json([
            'success' => true,
            'message' => 'attribute status updated successfully.',
            "data" => $attribute
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
            $categoriesToTable = Attribute::whereIn('id', $idsToDelete)->get();

            // Move admins to AdminArchive and delete from Admin
            foreach ($categoriesToTable as $category) {
                $category->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Attribute deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete Attribute.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $id)
    {
        $attribute = Attribute::findOrFail($id);
        $attribute->delete();

        // Clear the cache since a role has been deleted
        Cache::forget('attribute_cache');

        return response()->json([
            'success' => true,
            'message' => 'attribute deleted successfully.'
        ], 200);
    }
}
