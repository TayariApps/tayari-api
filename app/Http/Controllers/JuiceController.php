<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Juice;
use Illuminate\Support\Facades\Validator;

class JuiceController extends Controller
{
    public function index(){
        return \response()->json(Juice::all(),200);
    }

    public function placeJuices(Request $request, $placeID){
        return \response()->json(Juice::where('place_id',$placeID)->get(),200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'place_id' => 'required', 
            'name' => 'required', 
            'volume' => 'required', 
            'price' => 'required',
            'description' => 'required', 
            'image' => 'required'
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }

        if($request->hasFile('image')){
            $img_ext = $request->file('image')->getClientOriginalExtension();
            $filename = time() . '.' . $img_ext;
            $path = $request->file('image')->move(public_path(), $filename);//image save public folder
        }

        Juice::create([
            'place_id' => $request->place_id, 
            'name' => $request->name, 
            'volume' => $request->volume, 
            'price' => $request->price,
            'description' => $request->description, 
            'image' => $path
        ]);

        return \response()->json('Juice created',201);
    }

    public function update(Request $request, $id){
        Juice::where('id', $id)->update([
            'place_id' => $request->place_id, 
            'name' => $request->name, 
            'volume' => $request->volume, 
            'price' => $request->price,
            'description' => $request->description, 
        ]);

        return \response()->json('Juice updated',200);
    }

    public function delete(Request $request,$id){
        Juice::where('id', $request->id)->delete();
        return \response()->json('Juice deleted',200);
    }
}
