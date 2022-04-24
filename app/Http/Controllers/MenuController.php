<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    public function index(){
        return response()->json(Menu::all(), 200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'menu_name' => 'required', 
            'description' => 'required', 
            'size' => 'required', 
            'type' => 'required', 
            'banner' => 'required',
            'price' => 'required', 
            'time_takes_to_make' => 'required', 
            'place_id' => 'required',
            'type_id' => 'required'
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }

    }
}
