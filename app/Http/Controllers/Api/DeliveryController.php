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
            'message' => 'delivery created successfully',
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
    public function update(DeliveryRequest $request, $id)
    {
        $validated = $request->validated();

        $delivery = Delivery::findOrFail($id);


        $delivery->update([
            "cost" => $validated["cost"] ?? $delivery->cost,
            'country_id' => $validated['country_id'] ?? $delivery->country_id,
            'state_id' => $validated['state_id'] ?? $delivery->state_id,
            'city_id' => $validated['city_id'] ?? $delivery->city_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'delivery update successfully',
            'data' => $delivery, // Return the created order if needed
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $delivery = Delivery::findOrFail($id);

        $delivery->delete();

        return response()->json([
            'success' => true,
            'message' => 'delivery delete successfully',
        ], 201);
    }
}
