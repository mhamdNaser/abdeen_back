<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\order\OrderStatusRequest;
use App\Http\Resources\Site\OrdersResource;
use App\Http\Resources\Admin\order\OrderResource as OrderAdminResource;
use App\Http\Resources\Site\ViewOrderResource;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userOrders = Order::all();

        return OrderAdminResource::collection($userOrders);
    }

    public function pendingOrder()
    {
        $userOrders = Order::where('status', 'pending')->get();
        return OrderAdminResource::collection($userOrders);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }


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
            'address' => 'nullable'
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


        if ($request->address) {
            $orderAddress = OrderAddress::create([
                'country_id' => $request->address["country_id"],
                'state_id' => $request->address["state_id"],
                'city_id' => $request->address["city_id"],
                'address_1' => $request->address["address_1"],
                'address_2' => $request->address["address_2"],
                'address_3' => $request->address["address_3"],
                'order_id' => $order->id,
            ]);
        }


        foreach ($request->products as $productData) {
            // Fetch product details from database based on product_id
            $product = Product::findOrFail($productData['id']); // Adjust 'Product' to your actual model name

            // Calculate price after applying discount
            $price = $product->public_price;
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
    public function show($id)
    {
        $order = Order::with('orderProducts')->findOrFail($id);

        return new ViewOrderResource($order);
    }

    public function ordersUser($id)
    {
        $userOrders = Order::where('user_id', $id)->get();

        return OrdersResource::collection($userOrders);
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
    public function update(OrderStatusRequest $request, $id)
    {

        $validated = $request->validated();

        $order = Order::findOrFail($id);


        $order->update([
            "status" => $validated["status"]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => $order, // Return the created order if needed
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        DB::beginTransaction();

        $order->delete();

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Admin soft deleted successfully.'
        ], 200);
    }
}
