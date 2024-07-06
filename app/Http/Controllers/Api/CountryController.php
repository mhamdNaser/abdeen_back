<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\location\StoreRequest;
use App\Http\Resources\Admin\location\CountriesResource;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cacheDuration = 60; // Cache duration in minutes
        $cacheKey = 'countries_cache';

        $countries = Cache::remember($cacheKey, $cacheDuration, function () {
            return Country::with('states')->get();
        });

        $countryCountInDB = Country::count();

        if ($countries->count() !== $countryCountInDB) {
            $countries = Country::with('states')->get();
            Cache::put($cacheKey, $countries, $cacheDuration);
        }

        return CountriesResource::collection($countries);
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
    public function store(StoreRequest $request)
    {
        $validatedData = $request->validated();

        // Create a new resource using the validated data
        $resource = new Country();
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
    public function show(Country $country)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Country $country)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Country $country)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $country = Country::findOrFail($id);
        DB::beginTransaction();

        $country->delete();

        Cache::forget("countrys_cache");

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'country deleted successfully.'
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
            // Fetch countrys with IDs matching $idsToDelete
            $countrydelete = Country::whereIn('id', $idsToDelete)->get();

            // Move countrys to countryArchive and delete from country
            foreach ($countrydelete as $country) {
                $country->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'countrys deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete countrys.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
