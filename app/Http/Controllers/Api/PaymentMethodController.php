<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Payment\PaymentRequest;
use App\Http\Requests\Admin\Payment\UpdatePaymentRequest;
use App\Http\Resources\Admin\Payment\PaymentResource;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentMethodController extends Controller
{
    public function index()
    {
        return PaymentMethod::get();
    }


    public function getPaypal()
    {
        return new PaymentResource(PaymentMethod::where('name', 'Paypal')->first());
    }

    public function changestatus($id)
    {
        $category = PaymentMethod::findOrFail($id);

        // Toggle the status between 1 and 0
        $category->update([
            'status' => $category->status == 1 ? 0 : 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment Method status updated successfully.',
            "data" => $category
        ], 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(PaymentRequest $request)
    {
        DB::beginTransaction();
        try {
            $existingPaymentMethod = PaymentMethod::where('name', $request->input('name'))->exists();

            if ($existingPaymentMethod) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Payment method with the same name already exists',
                ], 200);
            }
            $payment = PaymentMethod::create($request->all());

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Payment method stored successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to store payment method',
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaymentRequest $request, $paymentMethods)
    {
        DB::beginTransaction();
        $user = PaymentMethod::find($paymentMethods);
        $data = $request->all();
        $user->update($data);
        DB::commit();
        return response()->json([
            'success' => true,
            'mes' => 'Update User Successfully',
        ]);
        DB::rollBack();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $paymentMethod = PaymentMethod::findOrFail($id);
            $paymentMethod->delete();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Payment method deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete payment method',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
