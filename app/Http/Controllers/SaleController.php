<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;

class SaleController extends Controller
{
    public function index(){
        return \response()->json(Sale::get(),200);
    }

    public function place($placeID){
        return \response()->json(Sale::where('place_id',$placeID)->get(),200);
    }
    

}
