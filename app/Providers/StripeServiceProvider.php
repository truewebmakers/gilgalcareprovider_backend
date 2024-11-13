<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Stripe\Stripe;
class StripeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
        Stripe::setApiKey('sk_test_51P2poYFOjqYjuziSfdIFxl7rdUrrNkIhm00XeHPVjKCeWIIGaoSPzRQKNq4pWzezAaVA8Y0LmpJGazSMYgvotkpH00OAhOSnb4');
    }
}
