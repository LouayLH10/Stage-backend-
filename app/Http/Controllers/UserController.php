<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function block(){

    }
public function display(){
            try {
            $users=User::get();
                        return response()->json([
                'status' => 'success',
                'message' => 'Liste des utilisateurs récupérée avec succès',
                'utilisateurs' => $users
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
