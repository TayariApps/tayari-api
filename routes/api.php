<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CuisineController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('v1')->group(function(){
     Route::post('register',[AuthController::class,'clientRegister']);
     Route::post('login',[AuthController::class, 'login']);

    Route::prefix('countries')->group(function(){
        Route::get('/',[CountryController::class,'countries']);
        Route::post('store',[CountryController::class,'store']);
        Route::patch('update/{id}',[CountryController::class,'update']);
        Route::delete('delete/{id}',[CountryController::class,'delete']);
    });

    Route::middleware('auth:sanctum')->group(function(){
        Route::post('updateUser',[AuthController::class,'updateUser']);
        Route::post('logout',[AuthController::class,'logout']);

        Route::prefix('place')->group(function(){
            Route::get('/',[PlaceController::class,'index']);
            Route::post('store',[PlaceController::class,'store']);
            Route::patch('update/{id}',[PlaceController::class,'update']);
            Route::delete('delete/{id}',[PlaceController::class,'delete']);
    
            Route::get('menu/{id}',[PlaceController::class,'placeMenu']);
        });

        Route::prefix('cuisine')->group(function(){
            Route::get('/',[CuisineController::class,'index']);
            Route::post('store',[CuisineController::class,'store']);
            Route::patch('update/{id}',[CuisineController::class,'update']);
            Route::delete('delete/{id}',[CuisineController::class,'delete']);
        });

    });

});