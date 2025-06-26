<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

public function handleGoogleCallback()
{
    try {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
                'password' => bcrypt(uniqid()),
            ]
        );

        Auth::login($user);
        $token = $user->createToken('google-login')->plainTextToken;

        // On ne redirige pas directement vers React
        return response()->view('redirect-to-react', [
            'name' => $user->name,
            'email' => $user->email,
            'token' => $token,
        ]);

    } catch (\Exception $e) {
        \Log::error('Erreur Google: ' . $e->getMessage());
        return redirect('/login')->with('error', 'Erreur Google');
    }
}

    // Facultatif : API pour React (récupérer les données depuis la session)
    public function getProfile()
    {
        return response()->json([
            'name' => session('name'),
            'email' => session('email'),
            'token' => session('token'),
        ]);
    }
}
