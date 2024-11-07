<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

use Stripe\Stripe;
use Stripe\Product;
use Stripe\Price;

class SubscriptionPlanController extends Controller
{
    //

    public function store(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));



        $request->validate([
            'name' => 'required|string',
            'term' => 'required|string',
            'price' => 'required|numeric',
            'features' => 'required|array', // Validate features as an array
        ]);

        $StripeProduct = Product::create([
            'name' => $request->name,
        ]);

        $price = Price::create([
            'unit_amount' => $request->price * 100, // Amount in cents
            'currency' => 'usd',
            'recurring' => ['interval' => $request->term],
            'product' => $StripeProduct->id,
        ]);


        $plan = SubscriptionPlan::create([
            'name' => $request->name,
            'price' => $request->price,
            'term' => $request->term,
            'stripe_price_id' => $price->id,
            'features' => $request->features,
        ]);

        return response()->json(['message' => 'Plan created successfully', 'plan' => $plan]);
    }
}
