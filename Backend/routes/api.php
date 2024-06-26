<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CreateUserController;
use App\Http\Controllers\PlantsController;
use App\Http\Controllers\MessagerieController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API !
|
*/
Route::post('/register', [CreateUserController::class, 'register']);
Route::post('/plants/createplant', [PlantsController::class, 'createPlant']);
Route::get('/plants/get-user-plants', [PlantsController::class, 'getUserPlants']);
Route::get('/plants/get-all-plants', [PlantsController::class, 'allPlants']);
Route::patch('/plants/post-plant/{plante}', [PlantsController::class, 'postPlant']);
Route::patch('/plants/remove-plant/{plante}', [PlantsController::class, 'removePlant']);
Route::delete('/plants/delete-plant/{plante}', [PlantsController::class, 'deletePlant']);
Route::get('/messages/{id_user}', [MessagerieController::class, 'getUserMessage']);

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
