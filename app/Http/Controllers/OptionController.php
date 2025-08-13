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
                    'status'  => 'error',
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors()
                ], 422);
            }

            // Default icon path
            $iconPath = 'options/icons/default_icon.png';

            // Handle upload if an icon is provided
            if ($request->hasFile('icon_opt')) {
                $uploadedIcon = $request->file('icon_opt');

                // Store in storage/app/public/options/icons
                $iconPath = $uploadedIcon->store('options/icons', 'public');
            }

            // Create option
            $option = Option::create([
                'name_opt' => $request->name_opt,
                'icon_opt' => $iconPath
            ]);

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

    public function showOption(Request $request)
    {
        try {
            $options = Option::get();

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
