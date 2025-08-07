<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Categorie;

class CategorieController extends Controller
{
    public function addCat(Request $request)
    {
        try {
            // ✅ Validation
            $validator = Validator::make($request->all(), [
                'nom_cat' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Validation échouée',
                    'errors'  => $validator->errors()
                ], 422);
            }

            // ✅ Création de la catégorie
            $categorie = Categorie::create([
                'nom_cat' => $request->nom_cat,
            ]);

            return response()->json([
                'status'    => 'success',
                'message'   => 'Catégorie ajoutée avec succès',
                'categorie' => $categorie
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Erreur serveur',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    public function getCat(){
        try {
            $categories=Categorie::get();
                        return response()->json([
                'status' => 'success',
                'message' => 'Liste des categories récupérée avec succès',
                'categories' => $categories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la récupération des projets',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
