<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\location\AllCitiesResource;
use App\Http\Resources\Admin\location\CitiesResource;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Fetch all cities directly from the database
            $cities = City::get();

            // Return the collection of cities as a resource
            return CitiesResource::collection($cities);
        } catch (\Exception $e) {
            // Handle any exceptions, e.g., log the error or return a custom error response
            return response()->json(['error' => 'Failed to retrieve cities.'], 500);
        }
    }

    public function allcities($id)
    {
        $cities = City::where("state_id", $id)->with("state")->get();
        return AllCitiesResource::collection($cities);
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
    public function show(City $city)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(City $city)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, City $city)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $country = City::findOrFail($id);
        DB::beginTransaction();

        $country->delete();

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'City deleted successfully.'
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
            $countrydelete = City::whereIn('id', $idsToDelete)->get();

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
