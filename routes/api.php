<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\AppartementController;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\CaracteristiqueController;
use  App\Http\Controllers\CategorieController;
use  App\Http\Controllers\VilleController;
use  App\Http\Controllers\UserController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::put('/update-project/{id}', [ProjectController::class, 'update']);
Route::delete('/delete-project/{id}', [ProjectController::class, 'destroy']);

Route::get('/projects', [ProjectController::class, 'display']);
Route::get('/categories', [CategorieController::class, 'getCat']);
Route::get('/users', [UserController::class, 'display']);

Route::post('/add-projects', [ProjectController::class, 'store']);
Route::post('/add-option', [OptionController::class, 'addOptions']);
Route::get('/options', [OptionController::class, 'showOption']);
Route::post('/add_caracteristique',[CaracteristiqueController::class,'addCaracteristique']);
Route::get('/projects/{id}', [ProjectController::class, 'get_projectById']);
Route::get('/search-projects', [ProjectController::class, 'getProjectbyCity']);
Route::get('/appartements/{id}',[AppartementController::class,'getAppartmentbyProject']);
Route::get('/google-profile', [GoogleController::class, 'getProfile']);
Route::post('/add_appartement',[AppartementController::class,'addappartement']);
Route::post('/add_categorie',[CategorieController::class,'addCat']);
Route::get('/villes',[VilleController::class,'get_villes']);

// ✅ Toutes les routes protégées par Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    // Tu peux ajouter d'autres routes protégées ici
});
