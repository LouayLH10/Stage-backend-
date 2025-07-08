<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\GoogleController;
use Laravel\Socialite\Facades\Socialite;

// 🔐 Déconnexion avec révocation des tokens Sanctum
Route::post('/logout', function () {
    if (Auth::check()) {
        Auth::user()->tokens()->delete(); // Révoque les tokens (Sanctum)
    }

    Auth::logout();                      // Déconnexion
    request()->session()->invalidate(); // Invalide session
    request()->session()->regenerateToken(); // Regénère CSRF

    return redirect('/login');
})->name('logout');

// 🔗 Google OAuth
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');

// 📤 API pour React (récupérer données de session)
Route::get('/api/user-from-session', function () {
    return response()->json([
        'name' => session('name'),
        'email' => session('email'),
        'token' => session('token'),
    ]);
})->name('session.user');

// Pages classiques Laravel (si besoin)
Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/', function () {
    return view('welcome');
});
