<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\category\CategoryMenuResource;
use App\Models\BrandCategory;
use App\Models\Category;
use Illuminate\Http\Request;

class BrandCategoryController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
        ]);

        BrandCategory::create($data);

        $categories = Category::where('id', '!=', 1)
            ->where('in_menu', 1)
            ->where('status', 1)
            ->with([
                'brands' => function ($query) {
                    $query->where('status', 1); // يمكنك تخصيص الشروط حسب الحاجة
                }
            ])
            ->get();

        return response()->json([
            'message' => 'Data stored successfully',
            "data" => CategoryMenuResource::collection($categories)
        ], 201);
    }
}
