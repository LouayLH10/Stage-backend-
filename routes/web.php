<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\GoogleController;

// 🔐 Déconnexion avec révocation des tokens Sanctum
Route::post('/logout', function () {
    if (Auth::check()) {
        // Révoquer tous les tokens API de l'utilisateur
        Auth::user()->tokens()->delete();
    }

    Auth::logout();                     // Déconnecter l'utilisateur
    request()->session()->invalidate(); // Invalider la session
    request()->session()->regenerateToken(); // Regénérer le token CSRF

    return redirect('/login'); // Redirection vers la page de connexion
})->name('logout');


// 🔗 Google OAuth
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);


// 📤 Route pour le frontend React (lecture des données session)
Route::get('/api/user-from-session', function () {
    return response()->json([
        'name' => session('name'),
        'email' => session('email'),
        'token' => session('token'),
    ]);
})->name('google.profile');


// Pages classiques (si tu en as besoin)
Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/', function () {
    return view('welcome');
});
