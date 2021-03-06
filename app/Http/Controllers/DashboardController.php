<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Type;
use App\Models\Place;
use App\Models\User;
use App\Models\Cuisine;
use App\Models\Drink;
use App\Models\Order;
use App\Models\DrinkType;
use App\Models\Country;
use App\Models\Sale;
use App\Models\Menu;
use App\Models\Employee;
use App\Models\SystemConstant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function settings(){
        $settings = SystemConstant::where('id', 1)->first();
        return \response()->json($settings,200);
    }

    public function changeNetwork(){
        $constant = SystemConstant::where('id', 1)->first();

        $types = Type::get();
        $places = Place::get();
        $menus = Menu::get();

        if(!$constant->network_active){

            if($constant->discount_active){
                foreach ($types as $type) {
                    $type->update([
                        'discount' => $type->discount - $constant->discount,
                    ]);
                }
    
                foreach ($places as $place) {
                    $place->update([
                        'discount' => $place->discount - $constant->discount,
                    ]);
                }
    
                foreach ($menus as $menu) {
                    $menu->update([
                        'discount' => $menu->discount - $constant->discount,
                    ]);
                }
            }

        } else{
            if($constant->discount_active){
                foreach ($types as $type) {
                    $type->update([
                        'discount' => $type->discount + $constant->discount,
                    ]);
                }
    
                foreach ($places as $place) {
                    $place->update([
                        'discount' => $place->discount + $constant->discount,
                    ]);
                }
    
                foreach ($menus as $menu) {
                    $menu->update([
                        'discount' => $menu->discount + $constant->discount,
                    ]);
                }
            }
        }

        $constant->update([
            'network_active' => !$constant->network_active
        ]);

        return \response()->json('Settings updated',200);
    }

    public function updateSettings(Request $request){

        $settings = SystemConstant::where('id', 1)->first();

        $discount = $request->discount/100;

        $food = Menu::get();

        foreach ($food as $meal) {
            $meal->update([
                'discount' => $meal->discount - $settings->discount
            ]);
        }

        $settings->update([
            'discount' => (float)$discount,
            'payment_cut' => (float)$request->cut/100
        ]);

        foreach ($food as $meal) {
            $meal->update([
                'discount' => $meal->discount + $settings->discount
            ]);
        }

        $discount == 0 ? 
        $settings->update([
            'discount_active' => false
        ]) :
        $settings->update([
            'discount_active' => true
        ]);

        return response()->json('Settings updated',200);
    }
    
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
                    'users.name as owner',
                    'places.is_open as is_open'
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
        return \response()->json(
            Country::has('places')->withCount('places')
                    ->with('places.user')->get(),200);
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
        $data = Place::has('orders')->with(['country', 'user'])->withSum([
            'orders as paid_sum' => function($query){
                $query->where('orders.payment_status','=',true);
            }
        ], 'total_cost')
        ->withSum(['disbursements as disbursement_sum'], 'amount')
        ->withSum([
            'orders as mobile_paid_sum' => function($query){
                $query->where('orders.payment_status','=',true)->where('payment_method','=',2);
            }
        ], 'total_cost')->get();

        $sales = $data->filter(function ($value) {
            return $value->paid_sum != null;
        })->values()->all(); 

        return response()->json($sales,200);
    }

}
