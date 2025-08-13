<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ville;
use Illuminate\Support\Facades\Validator;

class VilleController extends Controller
{
    public function getCities()
    {
        try {
            $cities = Ville::get();

            return response()->json([
                'status' => 'success',
                'message' => 'City list retrieved successfully.',
                'cities' => $cities
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error retrieving city list.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function addCity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'city_name' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors()
            ], 422);
        }

        $city = Ville::create([
            'nom_ville' => $request->city_name
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'City added successfully.',
            'city' => $city
        ], 201);
    }
}
