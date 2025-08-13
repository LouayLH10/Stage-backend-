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
use  App\Http\Controllers\TypeController;
use  App\Http\Controllers\RegionController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::put('/projects/{id}', [ProjectController::class, 'update']);
Route::delete('/projects/{id}', [ProjectController::class, 'destroy']);


Route::get('/categories', [CategorieController::class, 'getCat']);
Route::get('/users', [UserController::class, 'display']);

Route::post('/projects', [ProjectController::class, 'store']);
Route::post('/options', [OptionController::class, 'addOptions']);
Route::get('/options', [OptionController::class, 'showOption']);
Route::post('/features',[CaracteristiqueController::class,'addCaracteristique']);
Route::get('/projects/{id}', [ProjectController::class, 'get_projectById']);
Route::get('/projects', [ProjectController::class, 'filterProjects']);
Route::get('/types', [TypeController::class, 'get_types']);
Route::post('/types', [TypeController::class, 'add_type']);
Route::get('/appartements/{id}',[AppartementController::class,'getAppartmentbyProject']);
Route::get('/google-profile', [GoogleController::class, 'getProfile']);
Route::post('/appartements',[AppartementController::class,'addappartement']);
Route::post('/categories',[CategorieController::class,'addCat']);
Route::get('/cities',[VilleController::class,'getCities']);
Route::post('/cities',[VilleController::class,'addCity']);
Route::post('/regions',[RegionController::class,'add_region']);
Route::get('/regions',[RegionController::class,'display']);

// ✅ Toutes les routes protégées par Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    // Tu peux ajouter d'autres routes protégées ici
});
