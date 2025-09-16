<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Type;

class TypeController extends Controller
{
    public function add_type(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:255|unique:type,type'
        ]);

        $type = Type::create([
            'type' => $request->type
        ]);

        return response()->json([
            'message' => 'Type ajouté avec succès',
            'data' => $type
        ], 201);
    }

    public function get_types()
    {
        $types = Type::all();

        return response()->json([
            'message' => 'Liste des types récupérée avec succès',
            'data' => $types
        ], 200);
    }
     public function delete_type($id)
    {
        try {
            $type = Type::findOrFail($id);
            $type->delete();

            return response()->json([
                'message' => 'Type deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting type',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
