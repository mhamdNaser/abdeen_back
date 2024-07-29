<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SocialMediaRequest;
use App\Models\SocialMedia;
use Illuminate\Http\Request;

class SocialMediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $socialMedia = SocialMedia::all();

        return response()->json([
            'success' => true,
            'socialMedia' => $socialMedia,
        ]);
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
    public function show(SocialMedia $socialMedia)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SocialMedia $socialMedia)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SocialMediaRequest $request, $id)
    {
        $validated = $request->validated();

        $socialmedia = SocialMedia::findOrFail($id);


        $socialmedia->update([
            "title" => $validated["title"] ?? $socialmedia->title,
            'link' => $validated['link'] ?? $socialmedia->link,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'delivery update successfully',
            'data' => $socialmedia, // Return the created order if needed
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SocialMedia $socialMedia)
    {
        //
    }
}
