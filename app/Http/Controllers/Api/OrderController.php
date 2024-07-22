<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Site\OrderResource;
use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userOrders = Order::all();

        return OrderResource::collection($userOrders);
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
        // Validate the incoming request data
        $request->validate([
            'products.*.id' => 'required|integer|exists:products,id',
            'products.*.quantity' => 'required|integer',
            'price' => 'required|integer',
            'tax' => 'required|integer',
            'delivery' => 'required|integer',
            'totalprice' => 'required|integer',
        ]);

        // Create the order record
        $order = Order::create([
            'user_id' => Auth::user()->id,
            'status' => "pending",
            'price' => $request->price,
            'tax' => $request->tax,
            'delivery' => $request->delivery, 
            'total_price' => $request->totalprice, 
        ]);

        foreach ($request->products as $productData) {
            $product = OrderProduct::create([
                'order_id' => $order->id,
                'product_id' => $productData['id'],
                'quantity' => $productData['quantity'], 
                'tag_id' => null, 
            ]);
        }

        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => $order, // Return the created order if needed
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    public function ordersUser($id)
    {
        $userOrders = Order::where('user_id', $id)->get();

        return OrderResource::collection($userOrders);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
