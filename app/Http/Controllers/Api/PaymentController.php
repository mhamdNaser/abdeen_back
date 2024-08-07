<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaymentController extends Controller
{
    public function getAllPayments()
    {
        $payments = Payment::with('order')->get();

        return response()->json($payments);
    }

    public function getClientIdPaypal()
    {
        $paypalClientId = PaymentMethod::where("name", "Paypal")->first();
        return response()->json([
            'paypalClientId' => $paypalClientId->client_id
        ]);
    }

    public function createPayPalOrder(Request $request)
    {
        $orderId = $request->orderId;
        $order = Order::findOrFail($orderId);
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->setAccessToken($provider->getAccessToken());
        $amount = $order->total_price / 0.71;

        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => number_format((float)$amount, 2, '.', ''),
                    ]
                ]
            ],
        ]);

        if (isset($response["id"]) && $response["id"] != null) {
            foreach ($response["links"] as $link) {
                if ($link['rel'] === "approve") {
                    // Save the payment order details to the database
                    Payment::create([
                        'payment_id' => $response["id"],
                        'order_id' => $orderId,
                        'amount' => $amount,
                        'currency' => 'USD',
                        'status' => 'CREATED',
                        'transaction_id' => null,
                        'payment_method' => 'PayPal',
                        'payer_name' => null,
                        'payer_email' => null,
                    ]);

                    return response()->json($response);
                }
            }
        } else {
            return response()->json(['error' => 'Error creating PayPal order.'], 500);
        }
    }

    public function capturePayment(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->setAccessToken($provider->getAccessToken());

        $response = $provider->capturePaymentOrder($request->orderID);

        if (isset($response['status']) && $response['status'] === 'COMPLETED') {
            // Update the payment record with the completed transaction details
            $payment = Payment::where('payment_id', $request->orderID)->first();
            if ($payment) {
                $payment->update([
                    'status' => 'COMPLETED',
                    'transaction_id' => $response['id'],
                    'payer_name' => $response['payer']['name']['given_name'] . ' ' . $response['payer']['name']['surname'],
                    'payer_email' => $response['payer']['email_address'],
                ]);
            }

            return response()->json($response);
        } else {
            return response()->json(['error' => 'Error capturing payment.'], 500);
        }
    }

    public function casheOnDelivery(Request $request){
        $username = Auth::user()->username;
        $email = Auth::user()->email;
        $orderId = $request->orderId;
        $order = Order::findOrFail($orderId);
        $amount = $order->total_price ;

        $paymentId = $this->generateUniquePaymentId();
        $transactionId = $this->generateUniqueTransactionId();

        Payment::create([
            'payment_id' => $paymentId,
            'order_id' => $orderId,
            'amount' => $amount,
            'currency' => 'JD',
            'status' => 'COMPLETED',
            'payment_method' => 'Cash On Delivery',
            'transaction_id' => $transactionId,
            'payer_name' => $username,
            'payer_email' => $email
        ]);

        return response()->json(['message' => 'Payment created successfully.']);
    }



    private function generateUniquePaymentId()
    {
        do {
            $paymentId = 'ONDELIVERY-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 10));
        } while (Payment::where('payment_id', $paymentId)->exists());

        return $paymentId;
    }

    private function generateUniqueTransactionId()
    {
        do {
            $transactionId = 'TXN-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 10));
        } while (Payment::where('transaction_id', $transactionId)->exists());

        return $transactionId;
    }
}
