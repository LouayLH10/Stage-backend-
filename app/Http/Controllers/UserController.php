<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function toggleblock($id)
    {
        try {
  $user=User::find($id);
  if(!$user){
         return response()->json([
                    'status' => 'error',
                    'message' => 'User not found',
                ], 404);
  }
  $user->isblocked=!$user->isblocked;
  $user->save();
         return response()->json([
                    'status' => 'sucess',
                    'message' => 'User blocked/unblocked',
                ], 201);
        } catch (\Exception $e) {
            return response()->json([
                    'status' => 'error',
                    'message' => 'error',
                     'error' => $e->getMessage()
                ], 500);
        }
  


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
