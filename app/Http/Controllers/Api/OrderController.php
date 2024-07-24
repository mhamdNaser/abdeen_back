<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Site\OrderResource;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
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
    // public function store(Request $request)
    // {
    //     // Validate the incoming request data
    //     $request->validate([
    //         'products.*.id' => 'required|numeric|exists:products,id',
    //         'products.*.quantity' => 'required|numeric',
    //         'price' => 'required|numeric',
    //         'tax' => 'required|numeric',
    //         'delivery' => 'required|numeric',
    //         'totalprice' => 'required|numeric',
    //         'totaldiscount' => 'required|numeric',
    //     ]);

    //     // Create the order record
    //     $order = Order::create([
    //         'user_id' => Auth::user()->id,
    //         'status' => "pending",
    //         'price' => $request->price,
    //         'tax' => $request->tax,
    //         'delivery' => $request->delivery, 
    //         'total_price' => $request->totalprice,
    //         'total_discount' => $request->totaldiscount, 
    //     ]);

    //     foreach ($request->products as $productData) {
    //         $product = OrderProduct::create([
    //             'order_id' => $order->id,
    //             'product_id' => $productData['id'],
    //             'quantity' => $productData['quantity'], 
    //             'price' => $productData['price'], 
    //             'discount' => $productData['discount'], 
    //             'tag_id' => null, 
    //         ]);
    //     }

    //     $order->save();

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Order created successfully',
    //         'data' => $order, // Return the created order if needed
    //     ], 201);
    // }

    public function store(Request $request)
{
    // Validate the incoming request data
    $request->validate([
        'products.*.id' => 'required|numeric|exists:products,id',
        'products.*.quantity' => 'required|numeric',
        'price' => 'required|numeric',
        'tax' => 'required|numeric',
        'delivery' => 'required|numeric',
        'totalprice' => 'required|numeric',
        'totaldiscount' => 'required|numeric',
    ]);

    // Create the order record
    $order = Order::create([
        'user_id' => Auth::user()->id,
        'status' => "pending",
        'price' => $request->price,
        'tax' => $request->tax,
        'delivery' => $request->delivery,
        'total_price' => $request->totalprice,
        'total_discount' => $request->totaldiscount,
    ]);

    foreach ($request->products as $productData) {
        // Fetch product details from database based on product_id
        $product = Product::findOrFail($productData['id']); // Adjust 'Product' to your actual model name

        // Calculate price after applying discount
        $price = $product->price;
        $discount = $product->discount;

        // You may want to adjust the logic for applying discounts if needed
        // For example, calculate the final price after discount
        $finalPrice = $price - ($price * ($discount / 100));

        // Create OrderProduct record
        $orderProduct = OrderProduct::create([
            'order_id' => $order->id,
            'product_id' => $productData['id'],
            'quantity' => $productData['quantity'],
            'price' => $finalPrice, // Use the final calculated price here
            'discount' => $discount, // Or you may store the discount separately if needed
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
