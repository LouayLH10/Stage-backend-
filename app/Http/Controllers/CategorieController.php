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
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors()
                ], 422);
            }

            // ✅ Create category
            $categorie = Categorie::create([
                'nom_cat' => $request->nom_cat,
            ]);

            return response()->json([
                'status'    => 'success',
                'message'   => 'Category added successfully',
                'categorie' => $categorie
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Server error',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function getCat()
    {
        try {
            $categories = Categorie::get();

            return response()->json([
                'status'      => 'success',
                'message'     => 'Category list retrieved successfully',
                'categories'  => $categories
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error retrieving categories',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
