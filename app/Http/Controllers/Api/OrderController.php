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
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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
            'address' => 'nullable|array', // Ensure address is an array if provided
            'address.country_id' => 'nullable|exists:countries,id',
            'address.state_id' => 'nullable|exists:states,id',
            'address.city_id' => 'nullable|exists:cities,id',
            'address.address_1' => 'nullable|string|max:255',
            'address.address_2' => 'nullable|string|max:255',
            'address.address_3' => 'nullable|string|max:255',
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

        $user = Auth::user();

        // Check if the address is provided and valid, otherwise use the authenticated user's address
        if ($request->has('address') && !empty($request->address)) {
            $addressData = $request->address;
            $orderAddress = OrderAddress::create([
                'country_id' => $addressData['country_id'] ?? $user->country_id,
                'state_id' => $addressData['state_id'] ?? $user->state_id,
                'city_id' => $addressData['city_id'] ?? $user->city_id,
                'address_1' => $addressData['address_1'] ?? $user->address_1,
                'address_2' => $addressData['address_2'] ?? $user->address_2,
                'address_3' => $addressData['address_3'] ?? $user->address_3,
                'order_id' => $order->id,
            ]);
        } else {
            $orderAddress = OrderAddress::create([
                'country_id' => $user->country_id,
                'state_id' => $user->state_id,
                'city_id' => $user->city_id,
                'address_1' => $user->address_1,
                'address_2' => $user->address_2,
                'address_3' => $user->address_3,
                'order_id' => $order->id,
            ]);
        }

        // Process each product in the order
        foreach ($request->products as $productData) {
            $product = Product::findOrFail($productData['id']);

            $price = $product->public_price;
            $discount = $product->discount;

            $finalPrice = $price - ($price * ($discount / 100));

            if ($product->quantity < $productData['quantity']) {
                return response()->json(['error' => 'Quantity not sufficient for product ID ' . $productData['id']], 400);
            }

            // Create OrderProduct record
            $orderProduct = OrderProduct::create([
                'order_id' => $order->id,
                'product_id' => $productData['id'],
                'quantity' => $productData['quantity'],
                'price' => $finalPrice, // Use the final calculated price here
                'discount' => $discount, // Or you may store the discount separately if needed
                'tag_id' => null,
            ]);

            // Update product quantity and buy number
            $product->quantity -= $productData['quantity'];
            $product->buy_num += 1;
            $product->save();
        }

        // Clear the products cache
        Cache::forget("products_cache");

        // Return a success response with the created order ID
        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'orderId' => $order->id, // Return the created order if needed
        ], 201);
    }

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

    public function update(OrderStatusRequest $request, $id)
    {

        $validated = $request->validated();

        $order = Order::findOrFail($id);

        if (in_array($validated["status"], ['reject', 'return'])) {
            // Delete payments related to this order
            Payment::where('order_id', $order->id)->delete();
        }


        $order->update([
            "status" => $validated["status"]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => $order, // Return the created order if needed
        ], 201);
    }

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
