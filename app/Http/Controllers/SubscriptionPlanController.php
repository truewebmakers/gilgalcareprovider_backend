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

    public function index($planId="")
    {
        if($planId){
            $plans = SubscriptionPlan::find($planId);
        }else{
            $plans = SubscriptionPlan::get();
        }

        return response()->json(['plans' => $plans]);
    }


    public function store(Request $request)
    {
        Stripe::setApiKey('sk_test_51P2poYFOjqYjuziSfdIFxl7rdUrrNkIhm00XeHPVjKCeWIIGaoSPzRQKNq4pWzezAaVA8Y0LmpJGazSMYgvotkpH00OAhOSnb4');
        // Stripe::setApiKey(config('services.stripe.secret'));
        // return response()->json(['message' => 'Plan created successfully', 'plan' => $request->all()]);

        $request->validate([
            'name' => 'required|string',
            'term' => 'required|string',
            'price' => 'required|numeric',
            'features' => 'required', // Validate features as an array
        ]);

        $StripeProduct = Product::create([
            'name' => $request->name,
        ]);

        $price = Price::create([
            'unit_amount' => $request->price * 100, // Amount in cents
            'currency' => 'aud',
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

    public function update(Request $request, $id)
    {
        Stripe::setApiKey('sk_test_51P2poYFOjqYjuziSfdIFxl7rdUrrNkIhm00XeHPVjKCeWIIGaoSPzRQKNq4pWzezAaVA8Y0LmpJGazSMYgvotkpH00OAhOSnb4');

        $request->validate([
            'name' => 'sometimes|required|string',
            'term' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric',
            'features' => 'sometimes|required|array',
        ]);

        $plan = SubscriptionPlan::findOrFail($id);

        // Update Stripe Product if name is provided
        if ($request->has('name')) {
            $stripeProduct = Product::retrieve($plan->stripe_price_id);
            $stripeProduct->name = $request->name;
            $stripeProduct->save();
        }

        // Update Stripe Price if price or term is provided
        if ($request->has('price') || $request->has('term')) {
            $price = Price::create([
                'unit_amount' => $request->price * 100 ?? $plan->price * 100, // Amount in cents
                'currency' => 'aud',
                'recurring' => ['interval' => $request->term ?? $plan->term],
                'product' => $stripeProduct->id,
            ]);

            $plan->stripe_price_id = $price->id;
        }

        // Update local SubscriptionPlan data
        $plan->update([
            'name' => $request->name ?? $plan->name,
            'price' => $request->price ?? $plan->price,
            'term' => $request->term ?? $plan->term,
            'features' => $request->features ?? $plan->features,
        ]);

        return response()->json(['message' => 'Plan updated successfully', 'plan' => $plan]);
    }

    public function destroy($id)
    {
        Stripe::setApiKey('sk_test_51P2poYFOjqYjuziSfdIFxl7rdUrrNkIhm00XeHPVjKCeWIIGaoSPzRQKNq4pWzezAaVA8Y0LmpJGazSMYgvotkpH00OAhOSnb4');

        $plan = SubscriptionPlan::findOrFail($id);

        // Delete Stripe Price and Product
        $stripePrice = Price::retrieve($plan->stripe_price_id);
        $stripeProduct = Product::retrieve($stripePrice->product);

        $stripePrice->delete();
        $stripeProduct->delete();

        // Delete from local database
        $plan->delete();

        return response()->json(['message' => 'Plan deleted successfully']);
    }
}
