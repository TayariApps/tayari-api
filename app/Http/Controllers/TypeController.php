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
        $checkIfTypesExist = Place::where('id', $id)->has('types')->exists();

        if($checkIfTypesExist){

            $place = Place::where('id', $id)->with('types')->get();

           $types = $place->types;
           $food = Menu::where('place_id', $id)->get();
        } else{
            $food = [];
            $types = [];
        }

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

    public function delete($id){
        Type::where('id', $id)->delete();
        return \response()->json('Type deleted',200);
    }
}
