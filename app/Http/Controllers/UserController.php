<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function block()
    {
        // Block user logic will be implemented here
    }

    public function display()
    {
        try {
            $users = User::get();

            return response()->json([
                'status' => 'success',
                'message' => 'User list retrieved successfully.',
                'users'   => $users
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error retrieving users.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
