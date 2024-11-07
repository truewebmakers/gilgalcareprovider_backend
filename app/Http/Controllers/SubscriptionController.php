<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentMethod;
use Stripe\Subscription;
use App\Models\{User,SubscriptionPlan};

class SubscriptionController extends Controller
{
    public function createSubscription(Request $request)
    {
        $user = auth()->user();
        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        Stripe::setApiKey(config('services.stripe.secret'));

        // Create Stripe customer if not already created
        if (!$user->stripe_customer_id) {
            $customer = Customer::create([
                'email' => $user->email,
                'name' => $user->name,
            ]);
            $user->update(['stripe_customer_id' => $customer->id]);
        } else {
            $customer = Customer::retrieve($user->stripe_customer_id);
        }

        // Attach payment method
        PaymentMethod::retrieve($request->payment_method)->attach(['customer' => $customer->id]);
        $customer->invoice_settings = ['default_payment_method' => $request->payment_method];
        $customer->save();

        // Create subscription
        $subscription = Subscription::create([
            'customer' => $customer->id,
            'items' => [['price' => $plan->stripe_price_id]],
            'expand' => ['latest_invoice.payment_intent'],
        ]);

        // Store the subscription in your database
        $user->subscription()->create([
            'name' => $plan->name,
            'stripe_subscription_id' => $subscription->id,
            'status' => $subscription->status,
            'stripe_price_id' => $plan->stripe_price_id,
        ]);

        return response()->json(['subscription' => $subscription]);
    }
}
