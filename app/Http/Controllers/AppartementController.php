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
        // ✅ Validation pour plusieurs appartements
        $validator = Validator::make($request->all(), [
            'apartments' => 'required|array',
            'apartments.*.floor' => 'required|integer',
            'apartments.*.surface' => 'required|numeric',
            'apartments.*.price' => 'required|numeric',
            'apartments.*.categoryId' => 'required|exists:category,id',
            'apartments.*.plan' => 'nullable|file|mimetypes:application/pdf,image/jpeg,image/png,image/jpg|max:10240',
            'apartments.*.view.*' => 'nullable|file|image|max:100000000',
            'projectId' => 'required|exists:projects,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $projectId = $request->projectId;
        $apartmentsData = $request->apartments;
        $createdApartments = [];

        foreach ($apartmentsData as $apartment) {
            // 📦 Gestion du fichier plan (PDF ou image)
            $planPath = null;
            if (!empty($apartment['plan'])) {
                $planPath = $apartment['plan']->store('appartements/plans', 'public');
            }

            // 📦 Gestion des fichiers de vue (images)
            $viewPaths = [];
            if (!empty($apartment['view']) && is_array($apartment['view'])) {
                foreach ($apartment['view'] as $img) {
                    $viewPaths[] = $img->store('projects/vues', 'public');
                }
            }

            // 🧱 Création de l'appartement
            $newApartment = Appartement::create([
                "floor" => $apartment['floor'],
                "surface" => $apartment['surface'],
                "price" => $apartment['price'],
                "categoryId" => $apartment['categoryId'],
                "view" => json_encode($viewPaths), // Stockage comme JSON
                "plan" => $planPath,
                "projectId" => $projectId
            ]);

            $createdApartments[] = $newApartment;
        }

        return response()->json([
            'status' => 'success',
            'message' => count($createdApartments) . ' apartments added successfully',
            'apartments' => $createdApartments
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
            $appartements = Appartement::with('projet', 'Category')
                ->where('projectId', $id)
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
                    'floor' => $app->floor,
                    'surface' => $app->surface,
                    'price' => $app->price,
                    'category_id' => $app->categoryId,
                    'view' => $app->view,
                    'plan' => $app->plan,
                    'projectId' => $app->projectId,
                    'category' => $app->Category,
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
