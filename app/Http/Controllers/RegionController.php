<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Region;
 
class RegionController extends Controller
{
    public function display(Request $request){
        try {
            $villeId = $request->ville_id;

            $regions = Region::with('ville')
                ->where('ville_id', $villeId)
                ->get();

            if ($regions->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No project found in this city.',
                    'ville_id' => $villeId
                ], 404);
            }
   $regions = $regions->map(function ($item) {
    return [
        "id"=>$item->id,
        "region_name"=>$item->nom_region,
        "city_id"=>$item->ville_id,
        "city"=>[
            "id"=>$item->ville->id,
            "city_name"=>$item->ville->nom_ville,
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
            'nom_region' => 'required|string|max:255',
            'ville_id'   => 'required|exists:ville,id'
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
