<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Place;
use App\Models\User;
use App\Models\Cuisine;
use App\Models\Drink;
use App\Models\DrinkType;
use App\Models\Country;
use App\Models\Sale;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getCardCount(){
        return \response()->json([
            'places' => Place::where('active', true)->count(),
            'customers' => User::where('role',3)->count(),
            'sales' => Sale::where('paid', true)->sum('amount')
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

    public function cuisines(){
        return \response()->json(Cuisine::select('name','created_at')->get(),200);
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
            Sale::where([
                'paid' => true,
                'type' => 1
            ])->with('order.customer')->get(), 200
            );
    }

    public function mobileSales(){
        return \response()->json(
            Sale::where([
                'paid' => true,
                'type' => 2
            ])->with('order.customer')->get(), 200
            );
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
