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
                'plan' => 'file|mimetypes:application/pdf,image/jpeg,image/png,image/jpg|max:10240', // jusqu'à 10 Mo
                'project_id' => 'required|exists:projects,id',


            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // 📦 Upload fichiers
$planPath = null;

if ($request->hasFile('plan')) {
    $planPath = $request->file('plan')->store('appartements/plans', 'public');
}      

            // 🧱 Création du projet
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
                'message' => 'Projet ajouté avec succès',
                'project' => $project
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur serveur',
                'error' => $e->getMessage()
            ], 500);
        }
        


    }
    
public function getAppartmentbyProject($id){
    try {
$Appartements = Appartement::with('Projet','Categorie')
    ->where('project_id', $id)
    ->get();

if($Appartements->isEmpty()){
         return response()->json([
                'status' => 'error',
                'message' => 'Aucun Appartement ajouté dans cette residence.',
                
            ], 404);
}
        return response()->json([
            'status' => 'success',
            'message' => 'Appartements récupérés avec succès.',
            'Appartements' => $Appartements
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Erreur lors de la récupération des projets.',
            'error' => $e->getMessage()
        ], 500);
    }
}
    //
}
