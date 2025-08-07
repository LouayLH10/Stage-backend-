<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;  // Import correct
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens; // Si vous utilisez Sanctum

use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
  public function login(Request $request)
{
    try {
        // 1. Validation des données
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        // 2. Tentative d'authentification
        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants sont incorrects.'],
            ]);
        }

        // 3. Récupérer l'utilisateur authentifié
        $user = Auth::user();

        // 4. Supprimer les anciens tokens (optionnel)
        $user->tokens()->delete();

        // 5. Créer un nouveau token
        $token = $user->createToken('auth-token')->plainTextToken;

        // 6. Réponse JSON
        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'message' => 'Connexion réussie'
        ], 200);

    } catch (ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur de validation',
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        // Log l'erreur pour le débogage
        \Log::error('Login error: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Erreur interne du serveur',
            'error' => $e->getMessage() // À désactiver en production
        ], 500);
    }
}
    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user->tokens()->delete();
            return response()->json(['message' => 'Logged out successfully']);
        }

        return response()->json(['message' => 'No user logged in'], 401);
    }
public function user(Request $request)
{
    try {
        // Récupère l'utilisateur authentifié
        $user = Auth::user();

        // Si aucun utilisateur n'est connecté
        if (!$user) {
            return response()->json([
                'success' => false,
                
                'message' => 'Non authentifié - Aucun utilisateur connecté'
            ], 401);
        }

        // Retourne seulement les informations nécessaires de l'utilisateur
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                // Ajoutez d'autres champs publics si nécessaire
            ]
        ]);

    } catch (\Exception $e) {
        // Journalisation de l'erreur
        \Log::error('User profile error: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            
            'message' => 'Erreur lors de la récupération du profil utilisateur',
            'error' => env('APP_DEBUG') ? $e->getMessage() : null // Ne montre l'erreur qu'en mode debug
        ], 500);
    }
}
public function register(Request $request)
{
    try {
        // Validation des données
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Hashage du mot de passe et préparation des données
        $userData = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
            'email_verified_at' => now(), // Vous pouvez mettre une date si vous vérifiez l'email immédiatement
            'remember_token' => Str::random(10), // Génère un token aléatoire
        ];

        // Création de l'utilisateur
        $user = User::create($userData);

        // Création du token d'API
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'message' => 'User registered successfully'
        ], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'errors' => $e->errors(),
            'message' => 'Validation failed'
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Registration failed',
            'error' => $e->getMessage()
        ], 500);
    }
}
}
