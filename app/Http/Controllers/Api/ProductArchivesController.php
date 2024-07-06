<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\product\ProductResource;
use App\Models\ProductArchives;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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
    public function update(Request $request, ProductArchives $productArchives)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductArchives $productArchives)
    {
        //
    }
}
