<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Option;

class OptionController extends Controller
{
    public function addOptions(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name_opt' => 'required|string|max:255',
                'icon_opt' => 'nullable|file|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Chemin par défaut pour l'icône
            $iconPath = 'options/icons/defaut_icon.png';

            // Gestion de l'upload si une icône est fournie
            if ($request->hasFile('icon_opt')) {
                $uploadedIcon = $request->file('icon_opt');
                
                // Stockage dans storage/app/public/options/icons
                $iconPath = $uploadedIcon->store('options/icons', 'public');
            }

            // Création de l'option
            $option = Option::create([
                'name_opt' => $request->name_opt,
                'icon_opt' => $iconPath
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Option créée avec succès',
                'data' => $option
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur interne du serveur',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    public function showOption(Request $request){
try {
    $options=Option::get();
        return response()->json([
            'status' => 'success',
            'message' => 'Liste des projets récupérée avec succès',
            'Option' => $options
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