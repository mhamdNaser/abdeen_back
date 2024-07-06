<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\location\StatesResource;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $states = State::get();
        return StatesResource::collection($states);
    }

    public function allstates($id)
    {
        $states = State::where("country_id", $id)->get();
        return StatesResource::collection($states);
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
    public function show(State $state)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(State $state)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, State $state)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $country = State::findOrFail($id);
        DB::beginTransaction();

        $country->delete();

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'State deleted successfully.'
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
            $countrydelete = State::whereIn('id', $idsToDelete)->get();

            // Move countrys to countryArchive and delete from country
            foreach ($countrydelete as $country) {
                $country->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'State deleted successfully.',
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
