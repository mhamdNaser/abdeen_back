<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\product_details\CategoryRequest;
use App\Http\Resources\Admin\category\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cacheDuration = 60; // Cache duration in minutes
        $cacheKey = 'categories_cache';

        $categories = Cache::remember($cacheKey, $cacheDuration, function () {
            return Category::whereNot("id", 1)->get();
        });

        $countryCountInDB = Category::whereNot("id", 1)->count();

        if ($categories->count() !== $countryCountInDB) {
            $categories = Category::whereNot("id", 1)->get();
            Cache::put($cacheKey, $categories, $cacheDuration);
        }

        return CategoryResource::collection($categories);
    }

    public function allcategories()
    {
        $categories = Category::all();

        return CategoryResource::collection($categories);
    }

    public function menuCategory()
    {
        $categories = Category::whereNot("id", 1)->where("in_menu", 1)->get();

        return CategoryResource::collection($categories);
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
    public function store(CategoryRequest $request)
    {
        $validatedData = $request->validated();

        // Create a new resource using the validated data
        $resource = new Category();
        $resource->name = $validatedData['name'];
        $resource->description = $validatedData['description'];
        $resource->status = 1;
        $resource->parent_id = $validatedData['parent_id'] ?? null; // Handle nullable parent_id

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
    public function show(Category $category)
    {
        //
    }

    

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, $id)
    {
        $category = Category::findOrFail($id);

        $validatedData = $request->validated();

        $category->update([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'parent_id' => $validatedData['parent_id'],
            'status' => 1,
        ]);

        // Clear the cache since a role has been updated
        Cache::forget('categories_cache');

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully.'
        ], 200);
    }

    public function changestatus($id)
    {
        $category = Category::findOrFail($id);

        // Toggle the status between 1 and 0
        $category->update([
            'status' => $category->status == 1 ? 0 : 1,
        ]);

        // Clear the cache since a role status has been updated
        Cache::forget('categories_cache');

        return response()->json([
            'success' => true,
            'message' => 'category status updated successfully.',
            "data" => $category
        ], 200);
    }


    public function changeview($id)
    {
        $category = Category::findOrFail($id);

        // Toggle the status between 1 and 0
        $category->update([
            'in_menu' => $category->in_menu == 1 ? 0 : 1,
        ]);

        // Clear the cache since a role status has been updated
        Cache::forget('categories_cache');

        return response()->json([
            'success' => true,
            'message' => 'category view in menu updated successfully.',
            "data" => $category
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
            $categoriesToTable = Category::whereIn('id', $idsToDelete)->get();

            // Move admins to AdminArchive and delete from Admin
            foreach ($categoriesToTable as $category) {
                $category->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Admins soft deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to soft delete admins.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        // Clear the cache since a role has been deleted
        Cache::forget('categories_cache');

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully.'
        ], 200);
    }
}
