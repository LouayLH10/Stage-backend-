<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Caracteristique;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class CaracteristiqueController extends Controller
{
    public function addCaracteristique(Request $request)
    {
        try {
            $isMultiple = is_array($request->input('opt_id')); // Detect if we receive an array

            if ($isMultiple) {
                // ➤ Multiple insert
                $opt_ids = $request->input('opt_id');
                $project_id = $request->input('project_id');

                // General validation
                $validator = Validator::make($request->all(), [
                    'opt_id'     => 'required|array|min:1',
                    'opt_id.*'   => 'required|exists:options,id',
                    'project_id' => 'required|exists:projects,id',
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

                foreach ($opt_ids as $opt_id) {
                    $data[] = [
                        'opt_id'     => $opt_id,
                        'project_id' => $project_id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                Caracteristique::insert($data);

                return response()->json([
                    'status'  => 'success',
                    'message' => 'Features added successfully',
                    'count'   => count($data)
                ], 201);

            } else {
                // ➤ Single insert
                $validator = Validator::make($request->all(), [
                    'opt_id'     => 'required|exists:options,id',
                    'project_id' => 'required|exists:projects,id',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Validation failed',
                        'errors'  => $validator->errors()
                    ], 422);
                }

                $caracteristique = Caracteristique::create([
                    'opt_id'     => $request->opt_id,
                    'project_id' => $request->project_id,
                ]);

                return response()->json([
                    'status'          => 'success',
                    'message'         => 'Feature added successfully',
                    'features' => $caracteristique
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
    public function getCaracteristiques($project_id)
{
    try {
   
        $validator = Validator::make(
            ['project_id' => $project_id],
            ['project_id' => 'required|exists:projects,id']
        );

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Récupérer toutes les caractéristiques liées au projet
        $caracteristiques = Caracteristique::with('option') // suppose que tu as une relation option()
            ->where('project_id', $project_id)
            ->get();

        return response()->json([
            'status' => 'success',
            'count'  => $caracteristiques->count(),
            'data'   => $caracteristiques
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
