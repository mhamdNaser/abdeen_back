<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\product\ProductResource;
use App\Models\Product;
use App\Models\ProductArchives;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProductArchivesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cacheDuration = 60; // Cache duration in minutes
        $cacheKey = 'archives_products_cache';

        $products = Cache::remember($cacheKey, $cacheDuration, function () {
            return ProductArchives::get();
        });

        $countryCountInDB = ProductArchives::count();

        if ($products->count() !== $countryCountInDB) {
            $products = ProductArchives::get();
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductArchives $productArchives)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductArchives $productArchives)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id)
    {
        $product = ProductArchives::findOrFail($id);
        DB::beginTransaction();

        try {
            $softdelete = Product::create([
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

            $product->delete();

            Cache::forget("admins_cache");

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Admin Recovery successfully.'
            ], 200);
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Admin Recovery failed' . $e->getMessage()
            ], 500);
        }
    }

    public function updatearray(Request $request)
    {
        $validatedData = $request->validate([
            'array' => 'required|array',
        ]);

        $idsToDelete = $validatedData['array'];

        DB::beginTransaction();

        try {
            // Fetch admins with IDs matching $idsToDelete
            $products = ProductArchives::whereIn('id', $idsToDelete)->get();

            // Move admins to AdminArchive and delete from Admin
            foreach ($products as $product) {
                Product::create([
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

                $product->delete();
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

    public function DeleteArray(Request $request)
    {
        $validatedData = $request->validate([
            'array' => 'required|array',
        ]);

        $idsToDelete = $validatedData['array'];

        DB::beginTransaction();

        try {
            // Fetch admins with IDs matching $idsToDelete
            $adminsToTable = ProductArchives::whereIn('id', $idsToDelete)->get();

            // Move admins to AdminArchive and delete from Admin
            foreach ($adminsToTable as $admin) {

                $admin->delete();
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
        $admin = ProductArchives::findOrFail($id);
        DB::beginTransaction();

        $admin->delete();

        Cache::forget("admins_cache");

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Admin soft deleted successfully.'
        ], 200);
    }
}
