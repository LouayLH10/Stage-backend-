<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Appartement;

class AppartementController extends Controller
{
    public function addappartement(Request $request){
        try {
            // ✅ Validation
            $validator = Validator::make($request->all(), [
                'etage' => 'required|integer',
                'superfice' => 'required|numeric',
                'prix' => 'required|numeric',
                'plan' => 'file|mimetypes:application/pdf,image/jpeg,image/png,image/jpg|max:10240', // up to 10 MB
                'project_id' => 'required|exists:projects,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // 📦 File upload
            $planPath = null;
            if ($request->hasFile('plan')) {
                $planPath = $request->file('plan')->store('appartements/plans', 'public');
            }      

            // 🧱 Create the apartment
            $project = Appartement::create([
                "etage"=>$request->etage,
                "superfice"=>$request->superfice,
                "prix"=>$request->prix,
                "categorie_id"=>$request->categorie_id,
                "vue"=>$request->vue,
                "plan"=>$planPath,
                "project_id"=>$request->project_id
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Apartment added successfully',
                'project' => $project
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getAppartmentbyProject($id){
        try {
            $appartements = Appartement::with('projet', 'Categorie')
                ->where('project_id', $id)
                ->get();

            if($appartements->isEmpty()){
                return response()->json([
                    'status' => 'error',
                    'message' => 'No apartments found in this residence.',
                ], 404);
            }

            // Transform collection keys to English
            $result = $appartements->map(function($app) {
                return [
                    'floor' => $app->etage,
                    'surface' => $app->superfice,
                    'price' => $app->prix,
                    'category_id' => $app->categorie_id,
                    'view' => $app->vue,
                    'plan' => $app->plan,
                    'project_id' => $app->project_id,
                    'category' => $app->Categorie,
                    'project' => $app->projet,
                ];
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Apartments retrieved successfully.',
                'appartments' => $result
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error while retrieving apartments.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
