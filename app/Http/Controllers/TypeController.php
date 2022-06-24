<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Type;
use App\Models\Menu;
use App\Models\Place;

class TypeController extends Controller
{
    public function index(){
        return \response()->json(Type::get(),200);
    }

    public function place($id){

        $types = Type::where('place_id', $id)->with('menus')->get();
        $food = Menu::where('place_id', $id)->get();

        return \response()->json([
            'food' => $food,
            'types' => $types
        ], 200);
    }
    
    public function store(Request $request){
        Type::create([
            'name' => $request->name,
            'place_id' => $request->place_id
        ]);

        return \response()->json('Type created',201);
    }

    public function update(Request $request,$id){
        Type::where('id', $id)->update([
            'name' => $request->name
        ]);

        return \response()->json('Type updated',200);
    }  

    public function changeStatus($typeID){
        $checkIfExists = Type::where('id', $typeID)->has('menus')->exists();

        if(!$checkIfExists){
            return \response()->json('This food type has no food added to it', 400);
        }

        $type = Type::where('id', $typeID)->has('menus')->first();

        $type->update([
            'status' => !$type->status
        ]);

        foreach ($type->menus as $menu) {
            $menu->update([
                'status' => $type->status
            ]);
        }

        return \response()->json('Status updated',200);
    }

    public function delete($id){
          
        $checkIfTypeHasMenus = Type::where('id', $id)->has('menus')->exists();

        if($checkIfTypeHasMenus){
            return \response()->json('Type has food items attached, remove the food item first',400);
        }

        Type::where('id', $id)->delete();
        return \response()->json('Type deleted',200);
    }
}
