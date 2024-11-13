<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Stripe\Stripe;
use Illuminate\Support\Facades\Log;

class StripeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    public function boot()
    {
        try {
            // Try to set the Stripe API key globally
            // $apiKey = env('STRIPE_WEBHOOK_SECRET');
            $apiKey ='sk_test_51P2poYFOjqYjuziSfdIFxl7rdUrrNkIhm00XeHPVjKCeWIIGaoSPzRQKNq4pWzezAaVA8Y0LmpJGazSMYgvotkpH00OAhOSnb4';
            if (!$apiKey) {
                throw new \Exception("Stripe secret key is not set in .env");
            }

            Stripe::setApiKey($apiKey);
            Log::info("Stripe API key successfully set.");
        } catch (\Exception $e) {
            Log::error("Error in StripeServiceProvider boot method: " . $e->getMessage());
        }
    }


}
