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
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\DashboardController;

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
     Route::post('loginOwner',[AuthController::class,'ownerLogin']);
     Route::post('registerOwner',[AuthController::class,'ownerRegister']);

    Route::prefix('countries')->group(function(){
        Route::get('/',[CountryController::class,'countries']);
        Route::post('store',[CountryController::class,'store']);
        Route::patch('update/{id}',[CountryController::class,'update']);
        Route::delete('delete/{id}',[CountryController::class,'delete']);
    });

    Route::prefix('admin')->group(function(){
        Route::get('cardCount',[DashboardController::class,'getCardCount']);
        Route::get('places',[DashboardController::class,'getPlaces']);
        Route::get('customers',[DashboardController::class,'customers']);
        Route::get('owners',[DashboardController::class,'owners']);
        Route::get('waiters',[DashboardController::class,'waiters']);
        Route::get('cuisines',[DashboardController::class,'cuisines']);
        Route::get('drinks',[DashboardController::class,'drinks']);
        Route::get('countries',[DashboardController::class,'countries']);
        Route::get('sales',[DashboardController::class,'sales']);
        Route::get('placeTransactions',[DashboardController::class,'placesTransactionAmounts']);
    });

    Route::post('sale/mobileCallback',[SaleController::class,'mobileCallback']);
    Route::post('drink/store',[DrinkController::class,'store']);
    Route::post('drinktype/store',[DrinkController::class,'storeDrinkType']);
    Route::post('place/status',[PlaceController::class,'changeStatus']);

    Route::post('employeeRegister',[EmployeeController::class,'store']);

    Route::prefix('waiter')->group(function(){
        Route::post('register',[AuthController::class,'waiterRegistration']);
        Route::post('login',[AuthController::class,'waiterLogin']);
        Route::post('statusUpdate',[UserController::class,'updateWaiterStatus']);
    });

    Route::middleware('auth:sanctum')->group(function(){

        Route::post('updateUser',[AuthController::class,'updateUser']);
        Route::post('updateUserImage',[AuthController::class,'updateProfileImage']);
        Route::post('logout',[AuthController::class,'logout']);

        Route::prefix('employee')->group(function(){
            Route::get('/',[EmployeeController::class,'index']);
            // Route::post('store',[EmployeeController::class,'store']);
            Route::get('{place_id}',[EmployeeController::class,'getEmployeesByPlace']);
            Route::post('update/{id}',[EmployeeController::class,'update']);
            Route::delete('delete/{id}',[EmployeeController::class,'delete']);
        });

        Route::prefix('review')->group(function(){
            Route::get('places', [ReviewController::class,'placeReviews']);
            Route::get('topreviews',[ReviewController::class,'bestReviewedPlaces']);
            Route::get('menus',[ReviewController::class,'menuReviews']);
            Route::post('place/store',[ReviewController::class,'storePlaceReview']);
            Route::post('menu/store',[ReviewController::class,'storeFoodReview']);
            Route::get('place/{placeID}',[ReviewController::class,'getPlaceReview']);
            Route::get('menu/{menuID}',[ReviewController::class,'getMenuReview']);
        });
    
        Route::prefix('help')->group(function(){
            Route::post('store',[HelpController::class, 'store']);
        });

        Route::prefix('place')->group(function(){
            Route::get('/',[PlaceController::class,'index']);
            Route::get('{id}',[PlaceController::class,'getPlace']);
            Route::post('store',[PlaceController::class,'store']);
            Route::patch('update/{id}',[PlaceController::class,'update']);
            Route::delete('delete/{id}',[PlaceController::class,'delete']);
            Route::post('owner',[PlaceController::class,'ownerPlaces']);
            Route::get('menu/{id}',[PlaceController::class,'placeMenu']);


            Route::get('restaurantData/{id}',[PlaceController::class,'dashboardData']);
        });

        Route::prefix('reservation')->group(function(){
            Route::get('/',[ReservationController::class,'index']);
            Route::get('{id}',[ReservationController::class,'getReservationData']);
            Route::get('place/{id}',[ReservationController::class,'getPlaceReservations']);
            Route::post('restaurantStore',[ReservationController::class,'restaurantStore']);
            Route::post('addItemsToReservation',[ReservationController::class,'addItemsToReservation']);
            Route::post('store',[ReservationController::class,'store']);
            Route::post('user',[ReservationController::class,'getUserReservation']);
            Route::post('mobile/store',[ReservationController::class,'mobileStore']);
            Route::patch('update/{id}',[ReservationController::class,'update']);
            Route::delete('delete/{id}',[ReservationController::class,'delete']);

            Route::post('deleteFoodFromReservation',[ReservationController::class,'deleteFoodFromReservation']);
            Route::post('deleteDrinkFromReservation',[ReservationController::class,'deleteDrinkFromReservation']);
        });

        Route::prefix('table')->group(function(){
            Route::get('/',[TableController::class,'index']);
            Route::get('places/{placeID}',[TableController::class,'placeTables']);
            Route::post('store',[TableController::class,'store']);
            Route::patch('update/{id}',[TableController::class,'update']);
            Route::delete('delete/{id}',[TableController::class,'delete']);

            Route::get('qrcode/{id}',[TableController::class,'generateQRCode']);
        });

        Route::prefix('menu')->group(function(){
            Route::get('/',[MenuController::class,'index']);
            Route::get('place/{id}',[MenuController::class,'place']);
            Route::post('store',[MenuController::class, 'store']);
            Route::delete('delete/{id}',[MenuController::class,'delete']);
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
            Route::get('items',[DrinkController::class,'getDrinks']);
            
            Route::get('place/{id}',[DrinkController::class,'place']);
            Route::patch('update',[DrinkController::class,'update']);
            Route::patch('update/stock',[DrinkController::class,'updateStock']);
            Route::patch('addStock',[DrinkController::class,'addStock']);
            Route::post('createStock',[DrinkController::class,'createStock']);
            Route::delete('delete/{id}',[DrinkController::class,'delete']);
        });

        Route::prefix('order')->group(function(){
            Route::get('/',[OrderController::class,'index']);
            Route::post('user',[OrderController::class,'userOrders']);
            Route::get('place/{id}',[OrderController::class,'placeOrders']);
            Route::post('store',[OrderController::class,'store']);
            Route::get('restaurantConfirmPayment/{id}',[OrderController::class,'restaurantConfirmPayment']);
            Route::post('updateStatus',[OrderController::class,'changeStatus']);
        });

        Route::prefix('delivery')->group(function(){
            Route::get('/',[DeliveryController::class,'index']);
            Route::post('store',[DeliveryController::class,'store']);
        });

        Route::prefix('cuisine')->group(function(){
            Route::get('/',[CuisineController::class,'index']);
            Route::get('place/{id}',[CuisineController::class,'getPlacesFromCuisine']);
            Route::post('store',[CuisineController::class,'store']);
            Route::patch('update/{id}',[CuisineController::class,'update']);
            Route::delete('delete/{id}',[CuisineController::class,'delete']);
        });

        Route::prefix('type')->group(function(){
            Route::get('/',[TypeController::class,'index']);
            Route::post('store',[TypeController::class,'store']);
            Route::get('place/{id}',[TypeController::class,'place']);
            Route::post('update/{id}',[TypeController::class,'update']);
            Route::delete('delete/{id}',[TypeController::class,'delete']);
        });

        Route::prefix('customers')->group(function(){
            Route::get('/',[UserController::class,'getCustomers']);
            Route::get('place/{id}',[UserController::class,'getPlaceCustomers']);
        });

        Route::prefix('sale')->group(function(){
            Route::get('/',[SaleController::class,'index']);
            Route::get('place/{placeID}',[SaleController::class,'place']);
            Route::post('mobilePayment', [SaleController::class,'mobilePayment']);
            Route::get('checkOrder/{orderID}',[SaleController::class,'checkOrder']);
        });
        

    });

});