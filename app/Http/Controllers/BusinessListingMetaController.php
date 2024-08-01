<?php

namespace App\Http\Controllers;

use App\Models\BusinessListingMeta;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class BusinessListingMetaController extends Controller
{
    /**
     * Display a listing of the business listing meta.
     *
     * @return JsonResponse
     */
    public function index($id)
    {
        $meta = BusinessListingMeta::with('businessListing')->get();
        return response()->json($meta);
    }

    /**
     * Store a newly created business listing meta in storage.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'business_listing_id' => 'required|exists:business_listings,id',
            'gallery_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['business_listing_id']);

        if ($request->hasFile('gallery_image')) {
            $file = $request->file('gallery_image');
            $data['gallery_image'] = $file->store('gallery_images', 'public');
        }

        $meta = BusinessListingMeta::create($data);

        return response()->json([
            'message' => 'Business listing meta created successfully.',
            'meta' => $meta
        ]);
    }

    /**
     * Display the specified business listing meta.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $meta = BusinessListingMeta::find($id);

        if (!$meta) {
            return response()->json([
                'status' => false,
                'message' => 'Business listing meta not found.',
            ], 404);
        }

        return response()->json($meta);
    }

    /**
     * Update the specified business listing meta in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'business_listing_id' => 'required|exists:business_listings,id',
            'gallery_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $meta = BusinessListingMeta::find($id);

        if (!$meta) {
            return response()->json([
                'status' => false,
                'message' => 'Business listing meta not found.',
            ], 404);
        }

        $data = $request->only(['business_listing_id']);

        if ($request->hasFile('gallery_image')) {
            // Delete old gallery image if it exists
            if ($meta->gallery_image && Storage::exists($meta->gallery_image)) {
                Storage::delete($meta->gallery_image);
            }
            $file = $request->file('gallery_image');
            $data['gallery_image'] = $file->store('gallery_images', 'public');
        }

        $meta->update($data);

        return response()->json([
            'message' => 'Business listing meta updated successfully.',
            'meta' => $meta
        ]);
    }

    /**
     * Remove the specified business listing meta from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $meta = BusinessListingMeta::find($id);

        if (!$meta) {
            return response()->json([
                'status' => false,
                'message' => 'Business listing meta not found.',
            ], 404);
        }

        // Delete gallery image if it exists
        if ($meta->gallery_image && Storage::exists($meta->gallery_image)) {
            Storage::delete($meta->gallery_image);
        }

        $meta->delete();

        return response()->json([
            'message' => 'Business listing meta deleted successfully.'
        ]);
    }
}
