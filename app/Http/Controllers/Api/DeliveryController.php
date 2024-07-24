<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\order\DeliveryRequest;
use App\Http\Resources\Admin\order\DeliveryResource;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $delivery = Delivery::all();

        return DeliveryResource::collection($delivery);
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
    public function store(DeliveryRequest $request)
    {
        $validated = $request->validated();

        // Begin database transaction
        DB::beginTransaction();

        // Create new admin
        $delivery = Delivery::create([
            'cost' => $validated['cost'],
            'country_id' => $validated['country_id'],
            'state_id' => $validated['state_id'],
            'city_id' => $validated['city_id'],
        ]);

        // Commit the transaction
        DB::commit();

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Admin created successfully',
            'data' => $delivery
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Delivery $delivery)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Delivery $delivery)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Delivery $delivery)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Delivery $delivery)
    {
        //
    }
}
