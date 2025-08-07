<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ville;

class VilleController extends Controller
{
   function get_villes(){
     try {
        $villes = Ville::get();

        return response()->json([
            'status' => 'success',
            'message' => 'Liste des villes récupérée avec succès',
            'villes' => $villes
        ], 200);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Erreur lors de la récupération des villes',
            'error' => $e->getMessage()
        ], 500);
    }
   }

}