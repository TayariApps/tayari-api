<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Place;
use App\Models\User;
use App\Models\Cuisine;
use App\Models\Drink;
use App\Models\Order;
use App\Models\DrinkType;
use App\Models\Country;
use App\Models\Sale;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function adminLogin(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return \response()->json('The credentials are incorrect',400);
         }
 
         if($user->role != 1){
             return \response()->json('You are not an admin',400);
         }
      
         return \response()->json([
             'token' => $user->createToken(time())->plainTextToken,
             'user' => $user
         ], 200);
    }
    
    public function getCardCount(){

        $totalOrders = Order::where('payment_status', true)->count();

        $sumOfSalesPaidWithCash = Order::where([
            'payment_status' => true,
            'payment_method' => 1
            ])->sum('total_cost');

        $sumOfSalesPaidWithMobile = Order::where([
            'payment_status' => true,
            'payment_method' => 2
        ])->sum('total_cost');

        $tayariAmount =  $sumOfSalesPaidWithMobile * 0.02;

        $restaurantMobileOnlyRevenue = $sumOfSalesPaidWithMobile - $tayariAmount;

        $restaurantAmount = $sumOfSalesPaidWithCash + $restaurantMobileOnlyRevenue; 

        return \response()->json([
            'places' => Place::where('active', true)->count(),
            'customers' => User::where('role',3)->count(),
            'sales' => Order::where('payment_status', true)->sum('total_cost'),
            'totalOrders' => $totalOrders,
            'tayariAmount' => $tayariAmount,
            'restaurantAmount' => $restaurantAmount
        ]);
    }   

    public function getPlaces(){
        return \response()->json(
            DB::table('places')
                ->leftJoin('users','places.owner_id','=','users.id')
                ->leftJoin('countries','places.country_id','=','countries.id')
                ->select(
                    'places.id as id',
                    'places.name as name',
                    'places.active as active',
                    'countries.name as country',
                    'users.name as owner'
                )->get(),
            200);
    }

    public function customers(){
        return \response()->json(
            User::where('role',3)->select('id','name','phone','email','created_at')->get()
            ,200);
    }

    public function waiters(){
        return \response()->json(Employee::where('role',1)->with('user')->get(),200);
    }

    public function orders(){
        return \response()->json(Order::with([
            'customer','place', 'drinks', 'food','table'
            ])->orderBy('id', 'desc')->get(),200);
    }

    public function owners(){
        return \response()->json(
            DB::table('users')->where('role','=',4)
            ->leftJoin('places','users.id','=','places.owner_id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.phone',
                'places.name as restaurant'
                )->get()
            ,200);
    }

    public function deleteOwner($id){

        $user = \App\Models\User::where('id', $id)->first();

        if(\App\Models\User::where('id', $id)->has('places')->exists()){

             $place = \App\Models\Place::where('owner_id', $id)->first();

             \App\Models\Menu::where('place_id', $place->id)->delete();

             \App\Models\DrinkStock::where('place_id', $place->id)->delete();
     
             \App\Models\Order::where('place_id', $place->id)->delete();
     
             $place->delete();
        }

        $user->delete();

        return \response()->json('User deleted',200);
    }

    public function cuisines(){
        return \response()->json(Cuisine::get(),200);
    }

    public function drinks(){
        return \response()->json([
            'drinks' => Drink::get(),
            'types' => DrinkType::get()
        ],200);
    }

    public function countries(){
        return \response()->json(Country::withCount('places')->get(),200);
    }

    public function sales(){
        return response()->json(
            Sale::where('paid',true)->with('place')->get()
        ,200);
    }

    public function cashSales(){
        return \response()->json(
            $sales = Order::where([
                'payment_status' => true,
                'payment_method' => 1
            ])->with(['customer','place'])->orderBy('id', 'desc')->get(), 200
            );
    }

    public function mobileSales(){

        $sales = Order::where([
            'payment_status' => true,
            'payment_method' => 2
        ])->with(['customer','place'])->orderBy('id', 'desc')->get();

        return \response()->json($sales, 200);
    }

    public function drinkOrders(){
        $drinks = Drink::has('orders')->withSum('orders as total_orders','quantity')->get();

        return \response()->json($drinks, 200);
    }

    public function placesTransactionAmounts(){
        $sales = Place::withSum([
            'sales as paid_sum' => function($query){
                $query->where('sales.paid','=',true);
            }
        ], 'amount')->get();

        return response()->json($sales,200);
    }

}
