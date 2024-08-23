<?php

namespace App\Http\Controllers;

use App\Models\Feedbacks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    //
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'review' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'business_listing_id' => 'required|exists:business_listings,id',
            'user_id' => 'required'
        ]);

        $feedback = Feedbacks::create($request->all());

        return response()->json([
            'message' => 'Feedback submitted successfully',
            'feedback' => $feedback,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $feedback = Feedbacks::find($id);

        if (!$feedback) {
            return response()->json([
                'message' => 'Feedback not found',
            ], 404);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'review' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'business_listing_id' => 'required|exists:business_listings,id',
              'user_id' => 'required'
        ]);

        $feedback->update($request->all());

        return response()->json([
            'message' => 'Feedback updated successfully',
            'feedback' => $feedback,
        ], 200);
    }

    public function getFeedbackByBusinessListing($businessListingId)
    {
        $feedbacks = Feedbacks::where('business_listing_id', $businessListingId)->get();

        if ($feedbacks->isEmpty()) {
            return response()->json([
                'message' => 'No feedback found for this business listing',
            ], 404);
        }

        return response()->json([
            'feedbacks' => $feedbacks,
        ], 200);
    }

    public function getFeedback($id)
    {
        $feedback = Feedbacks::find($id);

        if (!$feedback) {
            return response()->json([
                'message' => 'Feedback not found',
            ], 404);
        }

        return response()->json([
            'feedback' => $feedback,
        ], 200);
    }

   
}
