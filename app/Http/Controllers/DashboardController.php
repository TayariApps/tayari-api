<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Place;
use App\Models\User;

class DashboardController extends Controller
{
    public function getCardCount(){
        return \response()->json([
            'places' => Place::where('active', true)->count(),
            'customers' => User::where('role',3)->count() 
        ]);
    }   

    public function getPlaces(){
        return \response()->json(Place::where('active', true)->get(), 200);
    }
}
