<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;

class OfferController extends Controller
{
    public function index(){
        $menus = Menu::where('food_discount','>', 0)->with('place')->get();
        return response()->json($menus,200);
    }
}
