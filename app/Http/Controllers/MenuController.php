<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Place;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    public function index(){
        return response()->json(Menu::all(), 200);
    }

    public function place($id){
        return \response()->json(
            Menu::where('place_id', $id)->get()
            ,200);
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

        // $imageValidator = Validator::make($request->all(), [
        //     'banner' => 'image|mimes:jpeg,jpg,png,gif|size:7000',
        // ]);

        // if($imageValidator->fails()){
        //     return response()->json('Image should be lower than 7 mbs', 400);
        // }

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
            'ingredients' => $request->ingredients,
            'kilos' => $request->kilos
        ]);

        return \response()->json('Menu created',200);
    }

    public function changeStatus($menuID){

        $menu = Menu::where('id', $menuID)->first();

        $menu->update([
            'status' => !$menu->status
        ]);

        return \response()->json('Status updated',200);

    }

    public function update(Request $request){

        $menu = Menu::where('id', $request->menu_id)->first();

        if($request->hasFile('banner')){

            // $imageValidator = Validator::make($request->all(), [
            //     'banner' => 'image|mimes:jpeg,jpg,png,gif|size:7000',
            // ]);
    
            // if($imageValidator->fails()){
            //     return response()->json('Image should be lower than 7 mbs', 400);
            // }

            $img_ext = $request->file('banner')->getClientOriginalExtension();
            $bannerFilename = time() . '.' . $img_ext;
            $bannerPath = $request->file('banner')->move(public_path('images/food'), $bannerFilename);//image save public folder

            $menu->update([
                'menu_name' => $request->name, 
                'description' => $request->description, 
                // 'size' => $request->size, 
                'banner' => $bannerFilename,
                'price' => $request->price, 
                'time_takes_to_make' => $request->time, 
                'type_id' => $request->type,
                'ingredients' => $request->ingredients,
                'kilos' => $request->kilos
            ]);
    
            return \response()->json('Menu updated',200);
        }

        $menu->update([
            'menu_name' => $request->name, 
            'description' => $request->description, 
            // 'size' => $request->size, 
            'price' => $request->price, 
            'time_takes_to_make' => $request->time, 
            'type_id' => $request->type,
            'ingredients' => $request->ingredients
        ]);

        return \response()->json('Menu updated',200);

    }

    public function delete($id){
        Menu::where('id',$id)->delete();
        return \response()->json('Food deleted',200);
    }

}
