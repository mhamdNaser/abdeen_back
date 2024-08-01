<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\product_details\ProductRequest;
use App\Http\Requests\Admin\product_details\UpdateProductRequest;
use App\Http\Requests\Admin\product_details\StoreProductImageRequest;
use App\Http\Resources\Admin\product\ProductResource;
use App\Http\Resources\Admin\product\ViewProductResource;
use App\Http\Resources\Site\ProductResource as ProductResourceSite;
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

    public function allproducts()
    {
        $cacheDuration = 60; // Cache duration in minutes
        $cacheKey = 'all_products_cache';

        $products = Cache::remember($cacheKey, $cacheDuration, function () {
            return Product::get();
        });

        $productCountInDB = Product::count();

        if ($products->count() !== $productCountInDB) {
            $products = Product::get();
            Cache::put($cacheKey, $products, $cacheDuration);
        }

        return ProductResourceSite::collection($products);
    }

    public function categoryProducts($id)
    {
        // Fetch the top 6 products with the highest buy_num
        $categoryProducts = Product::where('category_id', $id)->get();

        // Return the products as a resource collection
        return ProductResource::collection($categoryProducts);
    }

    public function topSellingProducts()
    {
        // Fetch the top 6 products with the highest buy_num
        $topSellingProducts = Product::orderBy('buy_num', 'desc')->take(4)->get();

        // Return the products as a resource collection
        return ProductResource::collection($topSellingProducts);
    }

    public function topDiscountedProducts()
    {
        // Fetch the top 6 products with the highest discount
        $topDiscountedProducts = Product::orderBy('discount', 'desc')->take(4)->get();

        // Return the products as a resource collection
        return ProductResource::collection($topDiscountedProducts);
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
            'en_name' => $validated['en_name'],
            'ar_name' => $validated['ar_name'],
            'en_description' => $validated['en_description'],
            'ar_description' => $validated['ar_description'],
            'cost_Price' => $validated['cost_Price'],
            'public_price' => $validated['public_price'],
            'discount' => 0,
            'quantity' => $validated['quantity'],
            'category_id' => $validated['category_id'],
            'brand_id' => $validated['brand_id'],
            'status' => 1,
        ]);


        if ($validated['image']) {
            $imageName = uniqid() . '_' . $validated['image']->getClientOriginalName();
            $destinationPath = public_path('upload/images/products/');
            $validated['image']->move($destinationPath, $imageName);
            $imagePath = 'upload/images/products/' . $imageName;
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
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Admin not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'product' => new ViewProductResource($product),
        ], 200);
    }

    public function cartProduct($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Admin not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'product' => new ProductResourceSite($product),
        ], 200);
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
    public function update(UpdateProductRequest $request, $id)
    {
        $validated = $request->validated();

        // Begin a database transaction
        DB::beginTransaction();

        // Find the product by ID
        $product = Product::findOrFail($id);

        // Prepare data for update
        $updateData = [
            'sku' => $validated['sku'] ?? $product->sku,
            'en_name' => $validated['en_name'] ?? $product->en_name,
            'ar_name' => $validated['ar_name'] ?? $product->ar_name,
            'en_description' => $validated['en_description'] ?? $product->en_description,
            'ar_description' => $validated['ar_description'] ?? $product->ar_description,
            'cost_Price' => $validated['cost_Price'] ?? $product->cost_Price,
            'public_price' => $validated['public_price'] ?? $product->public_price,
            'discount' => $validated['discount'] ?? 0,
            'quantity' => $validated['quantity'] ?? $product->quantity,
            'category_id' => $validated['category_id'] ?? $product->category_id,
            'brand_id' => $validated['brand_id'] ?? $product->brand_id,
            'status' => 1,
        ];

        if (isset($validated['image'])) {
            if ($product->image && file_exists(public_path($product->image))) {
                unlink(public_path($product->image));
            }
            $imageName = $validated['en_name'] . uniqid() . '.' . $validated['image']->getClientOriginalExtension();
            $destinationPath = public_path('upload/images/products/');
            $validated['image']->move($destinationPath, $imageName);
            $imagePath = 'upload/images/products/' . $imageName;
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
            'message' => 'Product status updated successfully.',
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
            $productToDelete = Product::whereIn('id', $idsToDelete)->get();

            foreach ($productToDelete as $product) {
                ProductArchives::create([
                    'sku' => $product->sku,
                    'en_name' => $product->en_name,
                    'ar_name' => $product->ar_name,
                    'image' => $product->image,
                    'en_description' => $product->en_description,
                    'ar_description' => $product->ar_description,
                    'cost_Price' => $product->cost_Price,
                    'public_price' => $product->public_price,
                    'category_id' => $product->category_id,
                    'brand_id' => $product->brand_id,
                    'quantity' => 0,
                    'status' => 0,
                    'discount' => 0,
                ]);

                $images = $product->images;

                foreach ($images as $image) {
                    $imagePath = public_path($image->path);
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                    $image->delete();
                }

                $product->delete();
            }

            Cache::forget("products_cache");

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Users soft deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to soft delete Users.',
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
                'en_name' => $product->en_name,
                'ar_name' => $product->ar_name,
                'image' => $product->image,
                'en_description' => $product->en_description,
                'ar_description' => $product->ar_description,
                'cost_Price' => $product->cost_Price,
                'public_price' => $product->public_price,
                'category_id' => $product->category_id,
                'brand_id' => $product->brand_id,
                'quantity' => 0,
                'status' => 0,
                'discount' => 0,
            ]);

            $images = $product->images;

            // Delete image records from database
            foreach ($images as $image) {
                // Delete image file from the server
                $imagePath = public_path($image->path);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                // Delete the image record from the database
                $image->delete();
            }

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

    public function storeImage(StoreProductImageRequest $request, $productId)
    {
        $product = Product::findOrFail($productId);

        if ($request->hasFile('images')) {
            $product->storeImages($request->file('images'));
        }

        return response()->json([
            'success' => true,
            'message' => 'Images uploaded successfully.',
            'product' => $product // إعادة البيانات المنتج مع الاستجابة
        ]);
    }

    public function deleteImage($productId, $imageId)
    {
        $product = Product::findOrFail($productId);

        if ($product->deleteImage($imageId)) {
            return response()->json(['success' => true, 'message' => 'Image deleted successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Image not found.']);
        }
    }

    public function showImages($productId)
    {
        $product = Product::findOrFail($productId);
        $images = $product->getImages();

        return response()->json([
            'success' => true,
            'images' => $images,
        ], 200);
    }
}
