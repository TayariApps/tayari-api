<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Place;
use App\Models\User;
use App\Models\Cuisine;
use App\Models\Drink;
use App\Models\Country;

class DashboardController extends Controller
{
    public function getCardCount(){
        return \response()->json([
            'places' => Place::where('active', true)->count(),
            'customers' => User::where('role',3)->count() 
        ]);
    }   

    public function getPlaces(){
        return \response()->json(Place::where('active', true)->withAvg('reviewed as reviewAverage', 'rating')->get(), 200);
    }

    public function customers(){
        return \response()->json(User::where('role',3)->get(),200);
    }

    public function waiters(){
        return \response()->json(User::where('role',2)->get(),200);
    }

    public function owners(){
        return \response()->json(User::where('role',4)->get(),200);
    }

    public function cuisines(){
        return \response()->json(Cuisine::get(),200);
    }

    public function drinks(){
        return \response()->json(Drink::get(),200);
    }

    public function countries(){
        return \response()->json(Country::with('places')->get(),200);
    }

}
