<?php

namespace App\Http\Controllers;

use App\Models\BusinessListing;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class BusinessListingController extends Controller
{
    /**
     * Display a listing of the business listings.
     *
     * @return JsonResponse
     */
    public function index($id)
    {
        $listings = BusinessListing::with('category')->get();
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
        $request->validate([
            'listing_title' => 'required|string|max:255',
            'listing_description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'tagline' => 'nullable|string',
            'price_range' => 'nullable|numeric',
            'price_from' => 'nullable|numeric',
            'price_to' => 'nullable|numeric',
            'features_information' => 'nullable|string',
            'location' => 'required|string',
            'address' => 'required|string',
            'map_lat' => 'nullable|numeric',
            'map_long' => 'nullable|numeric',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
            'phone' => 'nullable|string',
            'facebook' => 'nullable|url',
            'twitter' => 'nullable|url',
            'google_plus' => 'nullable|url',
            'instagram' => 'nullable|url',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'added_by' => 'required'
        ]);

        $data = $request->except(['featured_image', 'logo']);

        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $data['featured_image'] = $file->store('listing_images', 'public');
        }

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $data['logo'] = $file->store('listing_logos', 'public');
        }

        $listing = BusinessListing::create($data);

        return response()->json([
            'message' => 'Business listing created successfully.',
            'listing' => $listing
        ]);
    }

    /**
     * Display the specified business listing.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $listing = BusinessListing::with('category', 'meta')->find($id);

        if (!$listing) {
            return response()->json([
                'status' => false,
                'message' => 'Business listing not found.',
            ], 404);
        }

        return response()->json(['status' => true,'data' => $listing ]);
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
        $request->validate([
            'listing_title' => 'required|string|max:255',
            'listing_description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'tagline' => 'nullable|string',
            'price_range' => 'nullable|numeric',
            'price_from' => 'nullable|numeric',
            'price_to' => 'nullable|numeric',
            'features_information' => 'nullable|string',
            'location' => 'required|string',
            'address' => 'required|string',
            'map_lat' => 'nullable|numeric',
            'map_long' => 'nullable|numeric',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
            'phone' => 'nullable|string',
            'facebook' => 'nullable|url',
            'twitter' => 'nullable|url',
            'google_plus' => 'nullable|url',
            'instagram' => 'nullable|url',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $listing = BusinessListing::find($id);

        if (!$listing) {
            return response()->json([
                'status' => false,
                'message' => 'Business listing not found.',
            ], 404);
        }

        $data = $request->except(['featured_image', 'logo']);

        if ($request->hasFile('featured_image')) {
            // Delete old feature image if it exists
            if ($listing->featured_image && Storage::exists($listing->featured_image)) {
                Storage::delete($listing->featured_image);
            }
            $file = $request->file('featured_image');
            $data['featured_image'] = $file->store('listing_images', 'public');
        }

        if ($request->hasFile('logo')) {
            // Delete old logo if it exists
            if ($listing->logo && Storage::exists($listing->logo)) {
                Storage::delete($listing->logo);
            }
            $file = $request->file('logo');
            $data['logo'] = $file->store('listing_logos', 'public');
        }

        $listing->update($data);

        return response()->json([
            'message' => 'Business listing updated successfully.',
            'listing' => $listing
        ]);
    }

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
}
