<?php

namespace App\Providers;

use App\Models\PaymentMethod;
use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\DB;

class StripeServiceProvider extends ServiceProvider
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

        // If the database connection is established, proceed with fetching Stripe settings
        $stripeSettings = PaymentMethod::where('name', 'Stripe')->first();

        if ($stripeSettings) {
            // Set Stripe configuration based on retrieved settings
            $this->setStripeConfig($stripeSettings);
        } else {
            // If no Stripe settings are found, set default configuration values
            $this->setDefaultConfig();
        }
    }

    protected function setStripeConfig($stripeSettings)
    {
        config([
            'services.stripe.key' => $stripeSettings->client_id,
            'services.stripe.secret' => $stripeSettings->client_secret,
        ]);
    }

    protected function setDefaultConfig()
    {
        config([
            'services.stripe.key' => '',
            'services.stripe.secret' => '',
        ]);
    }

    public function register()
    {
        //
    }
}
