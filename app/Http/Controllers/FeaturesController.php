<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feature;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class FeaturesController extends Controller
{
    public function addFeature(Request $request)
    {
        try {
            $isMultiple = is_array($request->input('optionId')); // Detect if we receive an array

            if ($isMultiple) {
                // ➤ Multiple insert
                $optionIds = $request->input('optionId');
                $projectId = $request->input('projectId');

                // General validation
                $validator = Validator::make($request->all(), [
                    'optionId'     => 'required|array|min:1',
                    'optionId.*'   => 'required|exists:options,id',
                    'projectId' => 'required|exists:projects,id',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Validation failed',
                        'errors'  => $validator->errors()
                    ], 422);
                }

                // Prepare data
                $now = Carbon::now();
                $data = [];

                foreach ($optionIds as $optionId) {
                    $data[] = [
                        'optionId'     => $optionId,
                        'projectId' => $projectId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                Feature::insert($data);

                return response()->json([
                    'status'  => 'success',
                    'message' => 'Features added successfully',
                    'count'   => count($data)
                ], 201);

            } else {
                // ➤ Single insert
                $validator = Validator::make($request->all(), [
                    'optionId'     => 'required|exists:options,id',
                    'projectId' => 'required|exists:projects,id',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Validation failed',
                        'errors'  => $validator->errors()
                    ], 422);
                }

                $Feature = Feature::create([
                    'optionId'     => $request->optionId,
                    'projectId' => $request->projectId,
                ]);

                return response()->json([
                    'status'          => 'success',
                    'message'         => 'Feature added successfully',
                    'features' => $Feature
                ], 201);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Server error',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    public function getFeatures($projectId)
{
    try {
   
        $validator = Validator::make(
            ['projectId' => $projectId],
            ['projectId' => 'required|exists:projects,id']
        );

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Récupérer toutes les caractéristiques liées au projet
        $Features = Feature::with('option') // suppose que tu as une relation option()
            ->where('projectId', $projectId)
            ->get();

        return response()->json([
            'status' => 'success',
            'count'  => $Features->count(),
            'data'   => $Features
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Server error',
            'error'   => $e->getMessage()
        ], 500);
    }
}

}
