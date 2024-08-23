<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\BusinessListing;
use App\Models\Feedbacks;

class DashboardController extends Controller
{
    public function getListCounts()
    {
        $adminId = Auth::id(); // Get the currently logged-in admin's ID

        if (!$adminId) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        // Count the total number of business listings created by the admin
        $listingCount = BusinessListing::where('added_by', $adminId)->count();

        // Count the total number of reviews for these business listings
        $reviewCount = Feedbacks::whereIn('business_listing_id', function($query) use ($adminId) {
            $query->select('id')
                  ->from('business_listings')
                  ->where('added_by', $adminId);
        })->count();

        return response()->json([
            'listing_count' => $listingCount,
            'review_count' => $reviewCount,
        ], 200);
    }

    public function getReviewCount()
    {
        $adminId = Auth::id(); // Get the currently logged-in admin's ID

        if (!$adminId) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        // Count the total number of reviews for business listings created by the admin
        $reviewCount = Feedbacks::whereIn('business_listing_id', function($query) use ($adminId) {
            $query->select('id')
                  ->from('business_listings')
                  ->where('added_by', $adminId);
        })->count();

        return response()->json([
            'review_count' => $reviewCount,
        ], 200);
    }
}
