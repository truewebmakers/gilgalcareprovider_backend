<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    public function uploadTemporaryImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:10000',
        ]);

        $image = $request->file('image');
        $tempPath = 'temp/' . Str::random(40) . '.' . $image->getClientOriginalExtension();

        $image->storeAs('temp', $tempPath, 'public');

        return response()->json(['path' => $tempPath], 200);
    }
}
