<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Availablitities};
use Illuminate\Support\Facades\Validator;
class AvailablititiesController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'listing_id' => 'required|exists:business_listings,id',
            'availability' => 'required|array',
            'availability.*.is_enabled' => 'required|boolean',
            // 'availability.*.times' => 'array', // Ensure 'times' is an array
            // 'availability.*.times.*.start_time' => 'required|date_format:H:i',
            // 'availability.*.times.*.end_time' => 'required|date_format:H:i|after:availability.*.times.*.start_time',
        ]);


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Delete any existing availability for this translator
        Availablitities::where('listing_id', $request->listing_id)->delete();

        // Save new availability data
        foreach ($request->availability as $day => $data) {
            $isEnabled = $data['is_enabled']; // Get the `is_enabled` status for the day

            foreach ($data['times'] as $timeSlot) {
                Availablitities::updateOrInsert(
                    [
                        'listing_id' => $request->listing_id,
                        'day' => $day,
                        'start_time' => ($timeSlot['start_time']) ?  $timeSlot['start_time'] : null
                    ],
                    [
                        'end_time' => ($timeSlot['end_time']) ? $timeSlot['end_time'] : null,
                        'is_enabled' => $isEnabled, // Add `is_enabled` value
                        'updated_at' => now(),
                    ]
                );
            }
        }

        return response()->json(['message' => 'Availability added successfully'], 201);
    }

    public function index($translatorId)
    {
        $availability = Availablitities::where('listing_id', $translatorId)->get();
        return response()->json(['data' => $availability]);
    }

    public function getSlots( Request $request )
    {
        $validator = validator::make($request->all(), [
            'listing_id' => 'required',
            'day' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $listing_id =  $request->input('listing_id');
        $day =  $request->input('day');
        $availability = Availablitities::where(['translator_id' => $listing_id ,'day' => $day])->get();
        return response()->json(['data' => $availability]);
    }
}
