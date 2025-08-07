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
            $isMultiple = is_array($request->input('opt_id')); // détecter si on reçoit un tableau

            if ($isMultiple) {
                // ➤ Insertion multiple
                $opt_ids = $request->input('opt_id');
                $project_id = $request->input('project_id');

                // Validation générale
                $validator = Validator::make($request->all(), [
                    'opt_id'     => 'required|array|min:1',
                    'opt_id.*'   => 'required|exists:options,id',
                    'project_id' => 'required|exists:projects,id',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Validation échouée',
                        'errors'  => $validator->errors()
                    ], 422);
                }

                // Préparation des données
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
                    'message' => 'Caractéristiques ajoutées avec succès',
                    'count'   => count($data)
                ], 201);

            } else {
                // ➤ Insertion simple
                $validator = Validator::make($request->all(), [
                    'opt_id'     => 'required|exists:options,id',
                    'project_id' => 'required|exists:projects,id',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Validation échouée',
                        'errors'  => $validator->errors()
                    ], 422);
                }

                $caracteristique = Caracteristique::create([
                    'opt_id'     => $request->opt_id,
                    'project_id' => $request->project_id,
                ]);

                return response()->json([
                    'status'         => 'success',
                    'message'        => 'Caractéristique ajoutée avec succès',
                    'caracteristique'=> $caracteristique
                ], 201);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Erreur serveur',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
