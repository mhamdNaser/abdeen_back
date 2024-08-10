<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\product_details\CategoryRequest;
use App\Http\Resources\Admin\category\CategoryMenuResource;
use App\Http\Resources\Admin\category\CategoryResource;
use App\Models\Category;
use App\Models\Image;
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
        $categories = Category::where('id', '!=', 1)
            ->where('in_menu', 1)
            ->where('status', 1)
            ->with([
                'brands' => function ($query) {
                    $query->where('status', 1); // يمكنك تخصيص الشروط حسب الحاجة
                }
            ])
            ->get();

        $categories = $this->loadChildren($categories);

        return CategoryMenuResource::collection($categories);
    }

    public function fiterCategory(){
        $categories = Category::where('id', '!=', 1)
        ->where('in_menu', 1)
        ->where('status', 1)
        ->where('parent_id', 1)
        ->with(['children' => function ($query) {
            $query->where('in_menu', 1)
            ->where('status', 1);
        }])
        ->get();

        $categories = $this->loadChildren($categories);

        return CategoryMenuResource::collection($categories);
    }

    private function loadChildren($categories)
    {
        foreach ($categories as $category) {
            $category->children = $category->children()
                ->where('in_menu', 1)
                ->where('status', 1)
                ->with(['children' => function ($query) {
                    $query->where('in_menu', 1)
                        ->where('status', 1);
                }])
                ->get();

            if ($category->children->isNotEmpty()) {
                $this->loadChildren($category->children);
            }
        }
        return $categories;
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
        $validated = $request->validated();

        $resource = Category::create([
            'en_name' => $validated['en_name'],
            'ar_name' => $validated['ar_name'],
            'en_description' => $validated['en_description'],
            'ar_description' => $validated['ar_description'],
            'status' => 1,
            'in_menu' => 0,
            'parent_id' => $validated['parent_id'] ?? null,
        ]);


        if ($request->hasFile('image')) {
            $imageName = $validated['en_name'] . uniqid()  . '.' . $validated['image']->getClientOriginalExtension();
            $destinationPath = public_path('upload/images/categories/');
            $validated['image']->move($destinationPath, $imageName);
            $imagePath = 'upload/images/categories/' . $imageName;
            $resource->update([
                'image' => $imagePath
            ]);
        }


        // Save the resource to the database
        $resource->save();

        // Return a response indicating the resource was created
        return response()->json([
            'message' => 'Resource created successfully',
            'data' => $resource
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, $id)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            $category = Category::findOrFail($id);

            // Prepare data for update
            $updateData = [
                'en_name' => $validated['en_name'],
                'ar_name' => $validated['ar_name'],
                'en_description' => $validated['en_description'],
                'ar_description' => $validated['ar_description'],
                'parent_id' => $validated['parent_id'],
                'status' => 1,
            ];

            // Handle image upload if provided
            if ($request->hasFile('image')) {
                $imageName = $validated['en_name'] . uniqid() . '.' . $validated['image']->getClientOriginalExtension();
                $destinationPath = public_path('upload/images/categories/');
                $validated['image']->move($destinationPath, $imageName);
                $imagePath = 'upload/images/categories/' . $imageName;
                if ($category->image) {
                    $oldImagePath = public_path($category->image);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                    $updateData['image'] = $imagePath;
                } else {
                    $updateData['image'] = $imagePath;
                }
            }

            $category->update($updateData);
            Cache::forget('categories_cache');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully.',
                "data" => $validated
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update category.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Admin not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'category' => new CategoryResource($category),
        ], 200);
    }

    public function showbyname($name)
    {
        $category = Category::where("en_name", $name)->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Admin not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'category' => new CategoryResource($category),
        ], 200);
    }

    public function changestatus($id)
    {
        $category = Category::findOrFail($id);

        // Toggle the status between 1 and 0
        $category->update([
            'status' => $category->status == 1 ? 0 : 1,
            'in_menu' => 0
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
            // Fetch categories with IDs matching $idsToDelete
            $categoriesToTable = Category::whereIn('id', $idsToDelete)->get();

            // Delete categories and their images
            foreach ($categoriesToTable as $category) {
                // Delete the image from the folder if it exists
                if ($category->image) {
                    $imagePath = public_path($category->image);
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }

                $category->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Categories deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete categories.',
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
            $category = Category::findOrFail($id);

            // Delete the image from the folder if it exists
            if ($category->image) {
                $imagePath = public_path($category->image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $category->delete();

            // Clear the cache since a category has been deleted
            Cache::forget('categories_cache');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
