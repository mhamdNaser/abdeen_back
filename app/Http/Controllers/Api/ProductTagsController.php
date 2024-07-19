<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\product_details\ProductTagRequest;
use App\Http\Resources\Admin\product\ProductTagResource;
use App\Models\ProductTags;
use Illuminate\Http\Request;
use App\Services\ProductTagService;

class ProductTagsController extends Controller
{

    protected $productTagService;

    public function __construct(ProductTagService $productTagService)
    {
        $this->productTagService = $productTagService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function getTagByProductId($id)
    {
        $productTags = ProductTags::where("product_id" , $id)->with('tag')->get();

        // Optionally, eager load relationships if defined in the model
        // $productTags = ProductTags::with('product', 'tag')->get();

        // Transform collection using ProductTagResource
        $formattedProductTags = ProductTagResource::collection($productTags);

        return response()->json([
            'success' => true,
            'data' => $formattedProductTags,
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
    public function store(ProductTagRequest $request)
    {
        // Validate the request using ProductTagRequest
        $validatedData = $request->validated();

        // Save the product tag using the service
        $productTag = $this->productTagService->saveProductTag($validatedData);

        // Optionally, return a response indicating success
        return response()->json([
            'success' => true,
            'message' => 'Product tag saved successfully',
            'data' => $productTag,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductTags $productTags)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductTags $productTags)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductTags $productTags)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $productTag = ProductTags::find($id);

        // If product tag not found, return error response
        if (!$productTag) {
            return response()->json([
                'success' => false,
                'message' => 'Product tag not found',
            ], 404);
        }

        // Delete the product tag
        $productTag->delete();

        // Return success response
        return response()->json([
            'success' => true,
            'message' => 'Product tag deleted successfully',
        ]);
    }
}
