<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\AppartementController;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\FeaturesController;
use  App\Http\Controllers\CategoryController;
use  App\Http\Controllers\CityController;
use  App\Http\Controllers\UserController;
use  App\Http\Controllers\TypeController;
use  App\Http\Controllers\RegionController;
use App\Http\Controllers\PostController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/projects/{id}', [ProjectController::class, 'update']);
Route::delete('/projects/{id}', [ProjectController::class, 'destroy']);


Route::get('/categories', [CategoryController::class, 'getCat']);
Route::get('/users', [UserController::class, 'display']);
Route::delete('/users/{id}',[UserController::class,'toggleblock']);
Route::post('/projects', [ProjectController::class, 'store']);
Route::post('/options', [OptionController::class, 'addOptions']);
Route::get('/options', [OptionController::class, 'showOption']);
Route::post('/features',[FeaturesController::class,'addFeature']);
Route::get('/features/{id}',[FeaturesController::class,'getFeatures']);
Route::delete('/features/{featureId}', [FeaturesController::class, 'deletefeature']);

Route::get('/projects/{id}', [ProjectController::class, 'get_projectById']);
Route::get('/projects', [ProjectController::class, 'filterProjects']);
Route::get('/types', [TypeController::class, 'get_types']);
Route::post('/types', [TypeController::class, 'add_type']);
Route::get('/appartements/{id}',[AppartementController::class,'getAppartmentbyProject']);
Route::delete('/appartements/{id}',[AppartementController::class,'deleteAppartement']);

Route::get('/google-profile', [GoogleController::class, 'getProfile']);
Route::post('/appartements',[AppartementController::class,'addappartement']);
Route::get('/appartement/{id}',[AppartementController::class,'getAppartmentbyid']);

Route::get('/publish-appartement/{id}', [PostController::class, 'publierAppartement']);

Route::post('/categories',[CategoryController::class,'addCat']);
Route::get('/cities',[CityController::class,'getCities']);
Route::post('/cities',[CityController::class,'addCity']);
Route::post('/regions',[RegionController::class,'add_region']);
Route::get('/regions/{id}',[RegionController::class,'display']);
Route::delete('/options/{id}', [OptionController::class, 'deleteOption']);
Route::delete('/categories/{id}', [CategoryController::class, 'deleteCat']);
Route::delete('/types/{id}', [TypeController::class, 'delete_type']); 
// Dans routes/web.php
Route::get('/appartement/{id}/statut', [PostController::class, 'verifierStatut']);
Route::post('/appartements/{id}', [AppartementController::class, 'updateAppartement']);

// ✅ Toutes les routes protégées par Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    // Tu peux ajouter d'autres routes protégées ici
});
