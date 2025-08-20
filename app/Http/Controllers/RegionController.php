<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Region;
 
class RegionController extends Controller
{
    public function display($id){
        try {
            $cityId = $id;

            $regions = Region::with('city')
                ->where('cityId', $cityId)
                ->get();

            if ($regions->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No project found in this city.',
                    'cityId' => $cityId
                ], 404);
            }
   $regions = $regions->map(function ($item) {
    return [
        "id"=>$item->id,
        "region_name"=>$item->region,
        "city_id"=>$item->cityId,
        "city"=>[
            "id"=>$item->city->id,
            "city_name"=>$item->city->city,
        ]

        ];
});
            return response()->json([
                'status' => 'success',
                'message' => 'Projects retrieved successfully.',
                'regions' => $regions
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error retrieving projects.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function add_region(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'region' => 'required|string|max:255',
            'cityId'   => 'required|exists:city,id'
        ]);

        // Create region
        $region = Region::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Region added successfully.',
            'data'    => $region
        ], 201);
    }
}
