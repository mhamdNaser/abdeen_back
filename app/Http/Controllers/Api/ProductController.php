<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\product_details\ProductRequest;
use App\Http\Resources\Admin\product\ProductResource;
use App\Models\Image;
use App\Models\Product;
use App\Models\ProductArchives;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cacheDuration = 60; // Cache duration in minutes
        $cacheKey = 'products_cache';

        $products = Cache::remember($cacheKey, $cacheDuration, function () {
            return Product::get();
        });

        $countryCountInDB = Product::count();

        if ($products->count() !== $countryCountInDB) {
            $products = Product::get();
            Cache::put($cacheKey, $products, $cacheDuration);
        }

        return ProductResource::collection($products);
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
    public function store(ProductRequest $request)
    {
        // Validate and get validated data
        $validated = $request->validated();

        // Begin database transaction
        DB::beginTransaction();

        // Create new product
        $product = Product::create([
            'sku' => $validated['sku'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'quantity' => $validated['quantity'],
            'category_id' => $validated['category_id'],
            'brand_id' => $validated['brand_id'],
            'status' => 1,
        ]);


        if ($request->hasFile('image')) {
            $imagePath = $this->uploadImage($request->file('image'));

            // Create image record
            $image = new Image();
            $image->name = $imagePath; // Store the image path
            $product->images()->save($image);

            $product->update([
                'image' => $imagePath
            ]);
        }

        // Commit the transaction
        DB::commit();

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'product created successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, $id)
    {
        $validated = $request->validated();

        // Begin a database transaction
        DB::beginTransaction();

        // Find the product by ID
        $product = Product::findOrFail($id);

        // Prepare data for update
        $updateData = [
            'sku' => $validated['sku'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'quantity' => $validated['quantity'],
            'category_id' => $validated['category_id'],
            'brand_id' => $validated['brand_id'],
            'status' => 1,
        ];

        // Handle image upload if provided
        if ($request->hasFile('image')) {
            $imagePath = $this->uploadImage($request->file('image'));
            $updateData['image'] = $imagePath;
        }

        // Update product with the prepared data
        $product->update($updateData);

        Cache::forget("products_cache");

        // Commit the transaction
        DB::commit();

        // Return a success response with the updated product data
        return response()->json([
            'success' => true,
            'message' => 'product updated successfully.',
            'product' => $product, 
        ], 200);
    }

    public function changestatus($id)
    {
        $category = Product::findOrFail($id);

        // Toggle the status between 1 and 0
        $category->update([
            'status' => $category->status == 1 ? 0 : 1,
        ]);

        // Clear the cache since a role status has been updated
        Cache::forget('products_cache');

        return response()->json([
            'success' => true,
            'message' => 'category status updated successfully.',
            "data" => $category
        ], 200);
    }


    public function softDeleteArray(Request $request)
    {
        $validatedData = $request->validate([
            'array' => 'required|array',
        ]);

        $idsToDelete = $validatedData['array'];

        DB::beginTransaction();

        try {
            // Fetch product with IDs matching $idsToDelete
            $productsToDelete = Product::whereIn('id', $idsToDelete)->get();

            // Move product to ProductArchives and delete from product
            foreach ($productsToDelete as $product) {
                ProductArchives::create([
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'image' => $product->image,
                    'description' => $product->description,
                    'price' => $product->price,
                    'category_id' => $product->category_id,
                    'brand_id' => $product->brand_id,
                    'quantity' => 0,
                    'status' => 0,
                ]);

                $product->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'product soft deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to soft delete product.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        DB::beginTransaction();

        try {
            $softdelete = ProductArchives::create([
                'sku' => $product->sku,
                'name' => $product->name,
                'image' => $product->image,
                'description' => $product->description,
                'price' => $product->price,
                'category_id' => $product->category_id,
                'brand_id' => $product->brand_id,
                'quantity' => 0,
                'status' => 0,
            ]);

            $product->delete();

            Cache::forget("products_cache");

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'product soft deleted successfully.'
            ], 200);
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'product soft delete failed' . $e->getMessage()
            ], 500);
        }
    }

    private function uploadImage($imageFile)
    {
        // Generate unique image name
        $imageName = uniqid() . '_' . $imageFile->getClientOriginalName();

        // Specify the destination directory within the public disk
        $destinationPath = public_path('upload/images/products/');

        // Move the uploaded file to the destination directory
        $imageFile->move($destinationPath, $imageName);

        // Return the image path
        return 'upload/images/products/' . $imageName;
    }
}
