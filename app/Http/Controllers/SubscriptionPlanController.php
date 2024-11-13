<?php

namespace App\Http\Controllers;

use App\Models\{SubscriptionPlan,User};
use Illuminate\Http\Request;

use Stripe\Stripe;
use Stripe\Product;
use Stripe\Price;
use Stripe\Subscription;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
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

        $request->validate([
            'name' => 'sometimes|required|string',
            'term' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric',
            'features' => 'sometimes|required|array',
        ]);

        $plan = SubscriptionPlan::findOrFail($id);

        // Update Stripe Product if name is provided
        if ($request->has('name')) {
            // Step 1: Retrieve the Price object
            $stripePrice = Price::retrieve($plan->stripe_price_id);

            // Step 2: Get the associated Product ID from the Price object
            $productId = $stripePrice->product;

            // Step 3: Retrieve the Product object using the Product ID
            $stripeProduct = Product::retrieve($productId);

            // Step 4: Update the Product's name
            $stripeProduct->name = $request->name;
            $stripeProduct->save();
        }


        // if ($request->has('name')) {
        //     $stripeProduct = Product::retrieve($plan->stripe_price_id);
        //     $stripeProduct->name = $request->name;
        //     $stripeProduct->save();
        // }

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

        $plan = SubscriptionPlan::findOrFail($id);

        $stripePrice = Price::retrieve($plan->stripe_price_id);

        // Step 2: Get the associated Product ID from the Price object
        $productId = $stripePrice->product;

        // Step 3: Retrieve the Product object using the Product ID
        $stripeProduct = Product::retrieve($productId);


         $stripePrice->delete();
         $stripeProduct->delete();

        // Delete from local database
        $plan->delete();

        return response()->json(['message' => 'Plan deleted successfully']);
    }

    public function getCurrentPlan(Request $request)
    {
        // Validate the incoming request to ensure 'stripe_customer_id' is provided
        $request->validate([
            'stripe_customer_id' => 'required|exists:users,stripe_customer_id',
        ]);

        // Get the user based on the provided stripe_customer_id
        $user = User::where('stripe_customer_id', $request->stripe_customer_id)->first();

        if (!$user || !$user->stripe_customer_id) {
            return response()->json(['error' => 'User not found or no stripe customer ID'], 404);
        }

        // Set Stripe secret key


        try {
            // Retrieve the customer from Stripe
            $customer = Customer::retrieve($user->stripe_customer_id);


            return response()->json([
                'current_plan' => $customer,
            ]);

            // Retrieve subscriptions for the user
            $subscriptions = $customer->subscriptions->data;

            if (empty($subscriptions)) {
                return response()->json(['message' => 'No active subscriptions found'], 404);
            }

            // Get the current subscription
            $subscription = $subscriptions[0]; // Assuming the first subscription is the active one
            $currentPlan = $subscription->items->data[0]->plan->nickname; // Get the plan nickname

            return response()->json([
                'current_plan' => $currentPlan,
                'subscription_status' => $subscription->status,
                'subscription_id' => $subscription->id
            ]);

        } catch (ApiErrorException $e) {
            return response()->json(['error' => 'Failed to fetch subscription details', 'message' => $e->getMessage()], 500);
        }
    }

    public function cancelSubscription(Request $request)
    {
        // Validate the incoming request to ensure 'stripe_customer_id' is provided
        $request->validate([
            'stripe_customer_id' => 'required|exists:users,stripe_customer_id',
            'subscription_id' => 'required|string'
        ]);

        // Get the user based on the provided stripe_customer_id
        $user = User::where('stripe_customer_id', $request->stripe_customer_id)->first();

        if (!$user || !$user->stripe_customer_id) {
            return response()->json(['error' => 'User not found or no stripe customer ID'], 404);
        }

        try {
            // Retrieve the subscription from Stripe
            $subscription = Subscription::retrieve($request->subscription_id);

            // Cancel the subscription immediately
            $subscription->cancel();

            return response()->json([
                'message' => 'Subscription has been successfully cancelled.',
                'subscription_id' => $subscription->id
            ]);

        } catch (ApiErrorException $e) {
            return response()->json(['error' => 'Failed to cancel subscription', 'message' => $e->getMessage()], 500);
        }
    }
}
