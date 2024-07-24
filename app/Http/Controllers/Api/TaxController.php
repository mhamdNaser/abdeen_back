<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\order\TaxRequest;
use App\Http\Resources\Admin\order\TaxResource;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tax = Tax::all();

        return TaxResource::collection($tax);
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
    public function store(TaxRequest $request)
    {
        $validated = $request->validated();

        // Begin database transaction
        DB::beginTransaction();

        // Create new admin
        $tax = Tax::create([
            'tax' => $validated['tax'],
        ]);

        // Commit the transaction
        DB::commit();

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Admin created successfully',
            'data' => $tax
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tax $tax)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tax $tax)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tax $tax)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tax $tax)
    {
        //
    }
}
