<?php

// namespace App\Providers;

// use App\Models\PaymentMethod;
// use Illuminate\Support\ServiceProvider;

// use Illuminate\Support\Facades\DB;

// class PaypalServiceProvider extends ServiceProvider
// {
//     public function boot()
//     {
//         // Check if the database connection is established
//         try {
//             DB::connection()->getPdo();
//         } catch (\Exception $e) {
//             // If the connection fails, set default configuration values
//             $this->setDefaultConfig();
//             return;
//         }

//         // If the database connection is established, proceed with fetching PayPal settings
//         $paypalSettings = PaymentMethod::where('name', 'Paypal')->first();

//         if ($paypalSettings) {
//             // Set PayPal configuration based on retrieved settings
//             $this->setPaypalConfig($paypalSettings);
//         } else {
//             // If no PayPal settings are found, set default configuration values
//             $this->setDefaultConfig();
//         }
//     }

//     protected function setPaypalConfig($paypalSettings)
//     {
//         config([
//             'paypal.mode' => $paypalSettings->mode,
//             'paypal.live.client_id' => $paypalSettings->client_id,
//             'paypal.live.client_secret' => $paypalSettings->client_secret,
//             'paypal.payment_action' => 'Sale',
//             'paypal.currency' => $paypalSettings->currency,
//             'paypal.locale' => $paypalSettings->locale,
//             'paypal.validate_ssl' => true,
//         ]);
//     }

//     protected function setDefaultConfig()
//     {
//         config([
//             'paypal.mode' => 'live',
//             'paypal.live.client_id' => '',
//             'paypal.live.client_secret' => '',
//             'paypal.payment_action' => 'Sale',
//             'paypal.currency' => '',
//             'paypal.notify_url' => '',
//             'paypal.locale' => 'en_US',
//             'paypal.validate_ssl' => true,
//         ]);
//     }

//     public function register()
//     {
//         //
//     }
// }

namespace App\Providers;

use App\Models\PaymentMethod;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;

class PaypalServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Check if the database connection is established
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            // If the connection fails, set default configuration values
            $this->setDefaultConfig();
            return;
        }

        // If the database connection is established, proceed with fetching PayPal settings
        $paypalSettings = PaymentMethod::where('name', 'Paypal')->first();

        if ($paypalSettings) {
            // Set PayPal configuration based on retrieved settings
            $this->setPaypalConfig($paypalSettings);
        } else {
            // If no PayPal settings are found, set default configuration values
            $this->setDefaultConfig();
        }
    }

    protected function setPaypalConfig($paypalSettings)
    {
        config([
            'paypal.mode' => $paypalSettings->mode,
            'paypal.' . $paypalSettings->mode . '.client_id' => $paypalSettings->client_id,
            'paypal.' . $paypalSettings->mode . '.client_secret' => $paypalSettings->client_secret,
            'paypal.payment_action' => 'Sale',
            'paypal.currency' => $paypalSettings->currency,
            'paypal.locale' => $paypalSettings->locale,
            'paypal.validate_ssl' => true,
        ]);
    }

    protected function setDefaultConfig()
    {
        config([
            'paypal.mode' => 'live',
            'paypal.live.client_id' => env('PAYPAL_LIVE_CLIENT_ID', ''),
            'paypal.live.client_secret' => env('PAYPAL_LIVE_CLIENT_SECRET', ''),
            'paypal.payment_action' => 'Sale',
            'paypal.currency' => 'USD',
            'paypal.notify_url' => '',
            'paypal.locale' => 'en_US',
            'paypal.validate_ssl' => true,
        ]);
    }

    public function register()
    {
        //
    }
}

