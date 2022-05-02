<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\PlaceFoodType;
use App\Models\Place;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    public function index(){
        return response()->json(Menu::all(), 200);
    }

    public function place($id){
        return \response()->json(Menu::where('place_id', $id)->get(),200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'menu_name' => 'required', 
            'description' => 'required', 
            'size' => 'required', 
            'banner' => 'required',
            'price' => 'required', 
            'time_takes_to_make' => 'required', 
            'place_id' => 'required',
            'type_id' => 'required',
            'ingredients' => 'required'
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }

        if($request->hasFile('banner')){
            $img_ext = $request->file('banner')->getClientOriginalExtension();
            $bannerFilename = time() . '.' . $img_ext;
            $bannerPath = $request->file('banner')->move(public_path('images/food'), $bannerFilename);//image save public folder
        }

        $menu = Menu::create([
            'menu_name' => $request->menu_name, 
            'description' => $request->description, 
            'size' => $request->size, 
            'banner' => $bannerFilename,
            'price' => $request->price, 
            'time_takes_to_make' => $request->time_takes_to_make, 
            'place_id' => $request->place_id,
            'type_id' => $request->type_id,
            'ingredients' => $request->ingredients
        ]);

        PlaceFoodType::firstOrCreate([
            'place_id' => $menu->place_id,
            'type_id' => $menu->type_id
        ]);

        return \response()->json('Menu created',200);
    }

    public function delete($id){
        Menu::where('id',$id)->delete();
        return \response()->json('Food deleted',200);
    }

}
