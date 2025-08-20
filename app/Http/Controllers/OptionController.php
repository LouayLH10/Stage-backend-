<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Option;

class OptionController extends Controller
{
    // Ajouter une option
    public function addOptions(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'option' => 'required|string|max:255',
                'icon' => 'nullable|file|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors()
                ], 422);
            }

            // Chemin par défaut de l'icône
            $iconPath = 'options/default_icons/default_icon.png';

            // Gestion du téléchargement de l'icône si fournie
            if ($request->hasFile('icon')) {
                $uploadedIcon = $request->file('icon');

                // Stockage dans storage/app/public/options/icons
                $iconPath = $uploadedIcon->store('options/icons', 'public');
            }

            // Création de l'option
            $option = Option::create([
                'option' => $request->option,
                'icon' => $iconPath
            ]);

            // Pour retourner un lien accessible depuis le frontend
            $option->icon_url = asset('storage/' . $option->icon);

            return response()->json([
                'status'  => 'success',
                'message' => 'Option created successfully',
                'data'    => $option
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Internal server error',
                'error'   => $th->getMessage()
            ], 500);
        }
    }

    // Récupérer toutes les options
    public function showOption(Request $request)
    {
        try {
            $options = Option::all();

            // Ajouter l'URL publique de chaque icône
            $options->map(function ($opt) {
                $opt->icon_url = asset('storage/' . $opt->icon);
                return $opt;
            });

            return response()->json([
                'status'  => 'success',
                'message' => 'Options list retrieved successfully',
                'options' => $options
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error retrieving options',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
