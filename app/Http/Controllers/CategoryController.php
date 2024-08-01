<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    /**
     * Store a newly created category in storage.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'feature_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'details' => 'nullable|string',
            'location' => 'nullable|string',
        ]);

        $data = $request->only(['name', 'details', 'location']);

        if ($request->hasFile('feature_image')) {
            $file = $request->file('feature_image');
            $path = $file->store('category_images', 'public');
            $data['feature_image'] = $path;
        }

        $category = Category::create($data);

        return response()->json([
            'message' => 'Category created successfully.',
            'category' => $category
        ]);
    }

    /**
     * Display the specified category.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found.',
            ], 404);
        }

        return response()->json($category);
    }

    /**
     * Update the specified category in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'feature_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'details' => 'nullable|string',
            'location' => 'nullable|string',
        ]);

        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found.',
            ], 404);
        }

        $data = $request->only(['name', 'details', 'location']);

        if ($request->hasFile('feature_image')) {
            // Delete old feature image if it exists
            if ($category->feature_image && Storage::exists($category->feature_image)) {
                Storage::delete($category->feature_image);
            }

            $file = $request->file('feature_image');
            $path = $file->store('category_images', 'public');
            $data['feature_image'] = $path;
        }

        $category->update($data);

        return response()->json([
            'message' => 'Category updated successfully.',
            'category' => $category
        ]);
    }

    /**
     * Remove the specified category from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found.',
            ], 404);
        }

        // Delete feature image if it exists
        if ($category->feature_image && Storage::exists($category->feature_image)) {
            Storage::delete($category->feature_image);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully.'
        ]);
    }
}
