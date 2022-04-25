<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CuisineController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DrinkController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\JuiceController;
use App\Http\Controllers\LiquorController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\DeliveryController;

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

        Route::prefix('employee')->group(function(){
            Route::get('/',[EmployeeController::class,'index']);
            Route::post('store',[EmployeeController::class,'store']);
            Route::get('{place_id}',[EmployeeController::class,'getEmployeesByPlace']);
            Route::patch('update',[EmployeeController::class,'update']);
            Route::delete('delete/{id}',[EmployeeController::class,'delete']);
        });

        Route::prefix('place')->group(function(){
            Route::get('/',[PlaceController::class,'index']);
            Route::post('store',[PlaceController::class,'store']);
            Route::patch('update/{id}',[PlaceController::class,'update']);
            Route::delete('delete/{id}',[PlaceController::class,'delete']);
    
            Route::get('menu/{id}',[PlaceController::class,'placeMenu']);
        });

        Route::prefix('review')->group(function(){
            Route::get('places', [ReviewController::class,'placeReviews']);
            Route::get('menus',[ReviewController::class,'menuReviews']);
            Route::post('place/store',[ReviewController::class,'storePlaceReview']);
            Route::post('menu/store',[ReviewController::class,'storeFoodReview']);
            Route::get('place/review/{placeID}',[ReviewController::class,'getPlaceReview']);
            Route::get('menu/review/{menuID}',[ReviewController::class,'getMenuReview']);
        });

        Route::prefix('table')->group(function(){
            Route::get('/',[TableController::class,'index']);
            Route::get('places/{placeID}',[TableController::class,'placeTables']);
            Route::post('store',[TableController::class,'store']);
            Route::patch('update/{id}',[TableController::class,'update']);
            Route::delete('delete/{id}',[TableController::class,'delete']);
        });

        Route::prefix('menu')->group(function(){
            Route::get('/',[MenuController::class,'index']);
            Route::post('store',[MenuController::class, 'store']);
        });

        Route::prefix('juice')->group(function(){
            Route::get('/',[JuiceController::class,'index']);
            Route::get('{placeID}',[JuiceController::class,'placeJuices']);
            Route::post('store',[JuiceController::class,'store']);
            Route::patch('update/{id}',[JuiceController::class,'update']);
            Route::delete('delete/{id}',[JuiceController::class,'delete']);
        });

        Route::prefix('liquor')->group(function(){
            Route::get('/', [LiquorController::class,'index']);
            Route::post('store',[LiquorController::class,'store']);
            Route::patch('update/{id}',[LiquorController::class,'update']);
            Route::delete('delete/{id}',[LiquorController::class,'delete']);
        });

        Route::prefix('drink')->group(function(){
            Route::get('/', [DrinkController::class,'index']);
            Route::post('store',[DrinkController::class,'store']);
            Route::patch('update',[DrinkController::class,'update']);
            Route::patch('update/stock',[DrinkController::class,'updateStock']);
            Route::patch('addStock',[DrinkController::class,'addStock']);
            Route::delete('delete/{id}',[DrinkController::class,'delete']);
        });

        Route::prefix('order')->group(function(){
            Route::get('/',[OrderController::class,'index']);
            Route::post('store',[OrderController::class,'store']);
        });

        Route::prefix('delivery')->group(function(){
            Route::get('/',[DeliveryController::class,'index']);
            Route::post('store',[DeliveryController::class,'store']);
        });

        Route::prefix('cuisine')->group(function(){
            Route::get('/',[CuisineController::class,'index']);
            Route::post('store',[CuisineController::class,'store']);
            Route::patch('update/{id}',[CuisineController::class,'update']);
            Route::delete('delete/{id}',[CuisineController::class,'delete']);
        });

    });

});