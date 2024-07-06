<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\product_details\TagsRequest;
use App\Http\Resources\Admin\attribute\TagsResource;
use App\Models\AttributeTags;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttributeTagsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function alltags($id)
    {
        $tags = AttributeTags::where("attribute_id", $id)->get();
        return TagsResource::collection($tags);
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
    public function store(TagsRequest $request)
    {
        $validatedData = $request->validated();

        // Create a new resource using the validated data
        $resource = new AttributeTags();
        $resource->name = $validatedData['name'];
        $resource->description = $validatedData['description'];
        $resource->attribute_id = $validatedData['attribute_id'] ?? null; // Handle nullable parent_id
        $resource->status = 1;

        // Save the resource to the database
        $resource->save();

        // Return a response indicating the resource was created
        return response()->json([
            'message' => 'Resource created successfully',
            'data' => $resource
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(AttributeTags $attributeTags)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AttributeTags $attributeTags)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TagsRequest $request, $id)
    {
        $tag = AttributeTags::findOrFail($id);

        $validatedData = $request->validated();

        $tag->update([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'status' => 1,
        ]);

        // Clear the cache since a role has been updated
        // Cache::forget('attribute_cache');

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully.'
        ], 200);
    }

    public function changestatus ($id){
        $tag = AttributeTags::findOrFail($id);

        // Toggle the status between 1 and 0
        $tag->update([
            'status' => $tag->status == 1 ? 0 : 1,
        ]);

        // Clear the cache since a role status has been updated
        // Cache::forget('attribute_cache');

        return response()->json([
            'success' => true,
            'message' => 'Tag status updated successfully.',
            "data" => $tag
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
            $TagsToTable = AttributeTags::whereIn('id', $idsToDelete)->get();

            // Move admins to AdminArchive and delete from Admin
            foreach ($TagsToTable as $Tag) {
                $Tag->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tags deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete Tags.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $attribute = AttributeTags::findOrFail($id);
        $attribute->delete();

        // Clear the cache since a role has been deleted
        // Cache::forget('attribute_cache');

        return response()->json([
            'success' => true,
            'message' => 'Tag deleted successfully.'
        ], 200);
    }
}
