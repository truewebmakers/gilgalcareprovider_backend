<?php

namespace App\Http\Controllers;

use App\Models\{BusinessListing, BusinessListingMeta};
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use App\Models\ListingView;

class BusinessListingController extends Controller
{
    /**
     * Display a listing of the business listings.
     *
     * @return JsonResponse
     */
    public function index($id = '')
    {
        if ($id) {
            $listings = BusinessListing::with('categories')->where('added_by', $id)->get();
        } else {
            $listings = BusinessListing::with('categories')->where('status', 'published')->get();
        }

        return response()->json($listings);
    }

    /**
     * Store a newly created business listing in storage.
     *
     * @param  Request  $request
     * @return JsonResponse
     */

    public function store(Request $request): JsonResponse
    {
        // Update validation for multiple categories
        $request->validate([
            'listing_title' => 'required|string|max:255',
            'listing_description' => 'required|string',
            'category_id' => 'required|array',  // Change to array
            'category_id.*' => 'exists:categories,id', // Ensure all provided IDs are valid categories
            'tagline' => 'nullable|string|max:150',
            'features_information' => 'nullable',
            'location' => 'nullable',
            'address' => 'required|string',
            'map_lat' => 'nullable|numeric',
            'map_long' => 'nullable|numeric',
            'email' => 'nullable|email',
            'website' => 'required',
            'phone' => 'nullable|string',
            'facebook' => 'nullable',
            'instagram' => 'nullable',
            // 'featured_image' => 'nullable|image|mimes:jpeg,png,jpg|max:5000',
            'featured_image' => 'nullable|max:5000',
            'logo' => 'nullable|max:5000',
            'added_by' => 'required',
            'status' => 'required',
            'gallery_images.0' => 'required',
        ]);

        // Prepare the data (exclude files first)
        $data = $request->except(['featured_image', 'logo', 'category_id']);

        if($request->has('features_information')){
            $data['features_information'] = json_encode($request->input('features_information')) ;

        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $data['featured_image'] = $file->store('listing_images', 'public');
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $data['logo'] = $file->store('listing_logos', 'public');
        }

        // Create the listing without categories first
        $listing = BusinessListing::create(attributes: $data);
        $listingId = $listing->id;

        // Attach multiple categories to the listing
        if ($request->has('category_id')) {
            $listing->categories()->attach($request->category_id); // Attach the categories to the listing
        }

        // Handle gallery images if provided
        if ($request->has('gallery_images')) {
            $this->finalizeListing($request, $listingId);
        }

        // Return a response with the created listing
        return response()->json([
            'message' => 'Business listing created successfully.',
            'listing' => $listing
        ]);
    }



    // public function store(Request $request): JsonResponse
    // {
    //     $request->validate([
    //         'listing_title' => 'required|string|max:255',
    //         'listing_description' => 'required|string',
    //         'category_id' => 'required|exists:categories,id',
    //         'tagline' => 'nullable|string',
    //         // 'price_range' => 'nullable|numeric',
    //         // 'price_from' => 'nullable|numeric',
    //         // 'price_to' => 'nullable|numeric',
    //         'features_information' => 'nullable|string',
    //         'location' => 'required|string',
    //         'address' => 'required|string',
    //         'map_lat' => 'nullable|numeric',
    //         'map_long' => 'nullable|numeric',
    //         'email' => 'nullable|email',
    //         'website' => 'required',
    //         'phone' => 'nullable|string',
    //         'facebook' => 'nullable',
    //         // 'twitter' => 'nullable',
    //         // 'google_plus' => 'nullable',
    //         'instagram' => 'nullable',
    //         'featured_image' => 'nullable|image|mimes:jpeg,png,jpg|max:5000',
    //         'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:5000',
    //         'added_by' => 'required',
    //         'status' => 'required',
    //         'gallery_images.0' => 'required',

    //         // 'business_open_hours' => 'required|array',
    //         // 'business_open_hours.Monday' => 'required|array',
    //         // 'business_open_hours.Tuesday' => 'required|array',
    //         // Add validation rules for other days as needed
    //         // 'business_open_hours.*.isOpen' => 'required|boolean',
    //         // 'business_open_hours.*.open_at' => 'required|string',
    //         // 'business_open_hours.*.close_at' => 'required|string',


    //     ]);

    //     // return response()->json([
    //     //     'message' => 'Business listing created successfully.',
    //     //     'listing' => $request->all()
    //     // ]);

    //     $data = $request->except(['featured_image', 'logo']);

    //     if ($request->hasFile('featured_image')) {
    //         $file = $request->file('featured_image');
    //         $data['featured_image'] = $file->store('listing_images', 'public');
    //     }

    //     if ($request->hasFile('logo')) {
    //         $file = $request->file('logo');
    //         $data['logo'] = $file->store('listing_logos', 'public');
    //     }
    //     $data['business_open_hours'] =  $request->business_open_hours;

    //     $listing = BusinessListing::create($data);
    //     $listingId = $listing->id;

    //     if ($request->has('gallery_images')) {
    //         $this->finalizeListing($request, $listingId);
    //     }

    //     return response()->json([
    //         'message' => 'Business listing created successfully.',
    //         'listing' => $listing
    //     ]);
    // }


    public function finalizeListing(Request $request, $listingId)
    {


        foreach ($request->gallery_images as $tempPath) {
            $tempPathFull = 'temp/' . $tempPath;

            if (Storage::disk('public')->exists($tempPathFull)) {
                $newPath = 'listing_gallery/' . $listingId . '/' . basename($tempPath);
                Storage::disk('public')->move($tempPathFull, $newPath);
                $data['gallery_image'] = $newPath;
                $data['business_listing_id'] = $listingId;
                BusinessListingMeta::create($data);
            }
        }

        return response()->json(['message' => 'Listing updated with images.'], 200);
    }

    /**
     * Display the specified business listing.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $listing = BusinessListing::with(['categories', 'meta'])->where('uuid', $id)->first();

        if (!$listing) {
            // If no listing found by uuid, try finding by id
            $listing = BusinessListing::with(['categories', 'meta'])->where('id', $id)->first();
        }

        if (!$listing) {
            // If still no listing found, return 404
            return response()->json([
                'status' => false,
                'message' => 'Business listing not found.',
            ], 404);
        }

        return response()->json(['status' => true, 'data' => $listing]);
    }

    /**
     * Update the specified business listing in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return JsonResponse
     */

     public function update(Request $request, $id)
{
    // Update validation to accept multiple category IDs
    $request->validate([
        'listing_title' => 'required|string|max:255',
        'listing_description' => 'required|string',
        'category_id' => 'required|array',  // Accept multiple category IDs as an array
        'category_id.*' => 'exists:categories,id', // Ensure all category IDs are valid
        'tagline' => 'nullable|string',
        'features_information' => 'nullable',
        'location' => 'nullable|string',
        'address' => 'required|string',
        'map_lat' => 'nullable|numeric',
        'map_long' => 'nullable|numeric',
        'email' => 'nullable|email',
        'website' => 'required',
        'phone' => 'nullable|string',
        'facebook' => 'nullable',
        'instagram' => 'nullable|url',
        'featured_image' => 'nullable|max:5000',
        'logo' => 'nullable|max:5000',
        'status' => 'required',
        'gallery_images.0' => 'required',
        // 'business_open_hours' => 'required|array',
    ]);

    // Find the listing by ID
    $listing = BusinessListing::find($id);

    if (!$listing) {
        return response()->json([
            'status' => false,
            'message' => 'Business listing not found.',
        ], 404);
    }

    // Prepare data for update, excluding files
    $data = $request->except(['featured_image', 'logo', 'category_id']);  // Exclude category_id
    if($request->has('features_information')){
        $data['features_information'] = json_encode($request->input('features_information')) ;

    }
    // Handle featured image upload (if exists)
    if ($request->hasFile('featured_image')) {
        // Delete old featured image if it exists
        if ($listing->featured_image && Storage::exists($listing->featured_image)) {
            Storage::delete($listing->featured_image);
        }
        $file = $request->file('featured_image');
        $data['featured_image'] = $file->store('listing_images', 'public');
    }

    // Handle logo upload (if exists)
    if ($request->hasFile('logo')) {
        // Delete old logo if it exists
        if ($listing->logo && Storage::exists($listing->logo)) {
            Storage::delete($listing->logo);
        }
        $file = $request->file('logo');
        $data['logo'] = $file->store('listing_logos', 'public');
    }

    // Update business open hours if provided
    $data['business_open_hours'] = $request->business_open_hours;

    // Update the business listing with the new data
    $listing->update($data);

    // Update categories for this listing
    if ($request->has('category_id')) {
        // First, detach all current categories
        $listing->categories()->detach();

        // Attach the new categories
        $listing->categories()->attach($request->category_id);
    }

    // Handle gallery images if provided
    if ($request->has('gallery_images')) {
        $this->finalizeListing($request, $id);
    }

    // Return a successful response
    return response()->json([
        'message' => 'Business listing updated successfully.',
        'listing' => $listing
    ]);
}



    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'listing_title' => 'required|string|max:255',
    //         'listing_description' => 'required|string',
    //         'category_id' => 'required|exists:categories,id',
    //         'tagline' => 'nullable|string',
    //         // 'price_range' => 'nullable|numeric',
    //         // 'price_from' => 'nullable|numeric',
    //         // 'price_to' => 'nullable|numeric',
    //         'features_information' => 'nullable|string',
    //         'location' => 'required|string',
    //         'address' => 'required|string',
    //         'map_lat' => 'nullable|numeric',
    //         'map_long' => 'nullable|numeric',
    //         'email' => 'nullable|email',
    //         'website' => 'required',
    //         'phone' => 'nullable|string',
    //         'facebook' => 'nullable',
    //         // 'twitter' => 'nullable',
    //         // 'google_plus' => 'nullable',
    //         'instagram' => 'nullable|url',
    //         'featured_image' => 'nullable|image|mimes:jpeg,png,jpg|max:5000',
    //         'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:5000',
    //         'status' => 'required',
    //         'gallery_images.0' => 'required',

    //         'business_open_hours' => 'required|array',
    //         // 'business_open_hours.Monday' => 'required|array',
    //         // 'business_open_hours.Tuesday' => 'required|array',
    //         // // Add validation rules for other days as needed
    //         // 'business_open_hours.*.isOpen' => 'required|boolean',
    //         // 'business_open_hours.*.open_at' => 'required|string',
    //         // 'business_open_hours.*.close_at' => 'required|string',





    //     ]);

    //     $listing = BusinessListing::find($id);

    //     if (!$listing) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Business listing not found.',
    //         ], 404);
    //     }

    //     $data = $request->except(['featured_image', 'logo']);

    //     if ($request->hasFile('featured_image')) {
    //         // Delete old feature image if it exists
    //         if ($listing->featured_image && Storage::exists($listing->featured_image)) {
    //             Storage::delete($listing->featured_image);
    //         }
    //         $file = $request->file('featured_image');
    //         $data['featured_image'] = $file->store('listing_images', 'public');
    //     }

    //     if ($request->hasFile('logo')) {
    //         // Delete old logo if it exists
    //         if ($listing->logo && Storage::exists($listing->logo)) {
    //             Storage::delete($listing->logo);
    //         }
    //         $file = $request->file('logo');
    //         $data['logo'] = $file->store('listing_logos', 'public');
    //     }
    //     $data['business_open_hours'] =  $request->business_open_hours;

    //     $listing->update($data);

    //     $listingId = $id;

    //     if ($request->has('gallery_images')) {
    //         $this->finalizeListing($request, $listingId);
    //     }

    //     return response()->json([
    //         'message' => 'Business listing updated successfully.',
    //         'listing' => $listing
    //     ]);
    // }

    /**
     * Remove the specified business listing from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $listing = BusinessListing::find($id);

        if (!$listing) {
            return response()->json([
                'status' => false,
                'message' => 'Business listing not found.',
            ], 404);
        }

        // Delete feature image and logo if they exist
        if ($listing->featured_image && Storage::exists($listing->featured_image)) {
            Storage::delete($listing->featured_image);
        }
        if ($listing->logo && Storage::exists($listing->logo)) {
            Storage::delete($listing->logo);
        }

        $listing->delete();

        return response()->json([
            'message' => 'Business listing deleted successfully.'
        ]);
    }

    public function TopTenTrendingBusinessListing()
    {
        // Order by 'created_at' in descending order and limit to 10 results
        $topTrending = BusinessListing::orderBy('created_at', 'desc') // Order by creation date (most recent first)
            ->take(10) // Limit to top 10
            ->get(); // Execute the query and fetch the results

        return response()->json([
            'message' => 'Business listing fetched successfully.',
            'data' => $topTrending
        ]);
    }


    public function SearchBusinessListing(Request $request)
    {
        // $query = BusinessListing::query();
        $query = BusinessListing::with('categories');

        // Filter by listing_title
        if ($request->filled('listing_title')) {
            $query->where('listing_title', 'like', '%' . $request->input('listing_title') . '%');
        }

        // Filter by listing_description
        if ($request->filled('listing_description')) {
            $query->where('listing_description', 'like', '%' . $request->input('listing_description') . '%');
        }

        // Filter by category_id
        // if ($request->filled('category_id')) {
        //     $query->where('category_id', $request->input('category_id'));
        // }

         // Filter by multiple categories (many-to-many relationship)
        if ($request->filled('category_id')) {
            // Retrieve all category_ids passed as query parameters (they will be in an array)
          //  $categoryIds = $request->input('category_id');  // Automatically returns an array
            $categoryIds = (array) $request->input('category_id'); // This forces category_id to be an array


            // Filter BusinessListings that have the given categories
            $query->whereHas('categories', function ($q) use ($categoryIds) {
                $q->whereIn('category_id', $categoryIds);
            });
        }

        // Filter by added_by
        if ($request->filled('added_by')) {
            $query->where('added_by', $request->input('added_by'));
        }

        // Filter by tagline
        if ($request->filled('tagline')) {
            $query->where('tagline', 'like', '%' . $request->input('tagline') . '%');
        }

        // Filter by price_range (assuming you want to filter based on a range)
        if ($request->filled('price_from') && $request->filled('price_to')) {
            $query->whereBetween('price_range', [$request->input('price_from'), $request->input('price_to')]);
        }

        // Filter by location
        if ($request->filled('location')) {
            $query->where('address', 'like', '%' . $request->input('location') . '%');
           // $query->where('location', 'like', '%' . $request->input('location') . '%');
        }

        // Filter by address
        if ($request->filled('address')) {
            $query->where('address', 'like', '%' . $request->input('address') . '%');
        }

        // Filter by email
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->input('email') . '%');
        }

        // Filter by website
        if ($request->filled('website')) {
            $query->where('website', 'like', '%' . $request->input('website') . '%');
        }

        // Filter by phone
        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%' . $request->input('phone') . '%');
        }

        // Filter by social media fields
        if ($request->filled('facebook')) {
            $query->where('facebook', 'like', '%' . $request->input('facebook') . '%');
        }
        if ($request->filled('twitter')) {
            $query->where('twitter', 'like', '%' . $request->input('twitter') . '%');
        }
        if ($request->filled('google_plus')) {
            $query->where('google_plus', 'like', '%' . $request->input('google_plus') . '%');
        }
        if ($request->filled('instagram')) {
            $query->where('instagram', 'like', '%' . $request->input('instagram') . '%');
        }

        // Apply additional filtering if needed
        // For example, filtering by whether the listing has a featured image
        if ($request->has('has_featured_image')) {
            $query->whereNotNull('featured_image');
        }

        // Get the results
        $businessListings = $query->get();

        return response()->json([
            'message' => 'Business listing fetched successfully.',
            'data' => $businessListings
        ]);
    }

    public function incrementPageViews($id, Request $request)
    {
        $listing = BusinessListing::with(
            'categories'
        )->find($id);

        if (!$listing) {
            return response()->json(['message' => 'Listing not found'], 404);
        }

        $ipAddress = $request->ip(); // Get the user's IP address

        // Check if the IP address has already viewed this listing
        $hasViewed = ListingView::where('business_listing_id', $id)
            ->where('ip_address', $ipAddress)
            ->exists();

        if (!$hasViewed) {
            // Record the view and increment page views
            ListingView::create([
                'business_listing_id' => $id,
                'ip_address' => $ipAddress,
            ]);

            $listing->increment('page_views');
        }

        return response()->json(['message' => 'Page views updated successfully'], 200);
    }

    // Increment total shares
    public function incrementShares($id)
    {
        $listing = BusinessListing::find($id);

        if (!$listing) {
            return response()->json(['message' => 'Listing not found'], 404);
        }

        $listing->increment('total_shares');

        return response()->json(['message' => 'Shares updated successfully'], 200);
    }

    // Get stats for a business listing
    public function getListingStats($id)
    {
        $listing = BusinessListing::find($id);

        if (!$listing) {
            return response()->json(['message' => 'Listing not found'], 404);
        }

        $reviewCount = $listing->feedbacks()->count();

        return response()->json([
            'page_views' => $listing->page_views,
            'total_shares' => $listing->total_shares,
            'total_reviews' => $reviewCount,
        ], 200);
    }


    // public function SearchBussinessListingSugesstions(Request $request)
    // {
    //     $query = Language::query();
    //     if ($request->filled('language')) {
    //         $language = $request->input('language');
    //         $query->where('name', 'LIKE', "%$language%");
    //     }
    //     $languages = $query->get();
    //     return response()->json(['message' => 'languages suggestion list fetched successfully.', 'data' => $languages, 'status' => true], 200);
    // }
}
