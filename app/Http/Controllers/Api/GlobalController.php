<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\Category;
use App\Models\Brand;

class GlobalController extends Controller
{
    public function getStatistics()
    {
        $productCount = Product::count();
        $userCount = User::count();
        $orderCount = Order::count();
        $adminCount = Admin::count();
        $categoryCount = Category::count();
        $brandCount = Brand::count();
        $totalCompletedOrderValue = Order::where('status', 'complete')->sum('total_price');
        $totalReturnedOrderValue = Order::where('status', 'return')->sum('total_price');

        $data = [
            'productCount' => $productCount,
            'userCount' => $userCount,
            'orderCount' => $orderCount,
            'domainCount' => $adminCount,
            'categoryCount' => $categoryCount,
            'brandCount' => $brandCount,
            'totalCompletedOrderValue' => $totalCompletedOrderValue,
            'totalReturnedOrderValue' => $totalReturnedOrderValue,
        ];

        return response()->json([
            "data"=> $data,
        ], 201);
    }
}

