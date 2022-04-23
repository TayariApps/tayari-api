<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Liquor;
use Illuminate\Support\Facades\Validator;

class LiquorController extends Controller
{
    public function index(){
        return \response()->json(Liquor::get(),200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'place_id' => 'required', 
            'drink_id' => 'required',
            'shots' => 'required', 
            'price_per_shot' => 'required'
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }

        Liquor::create([
            'place_id' => $request->place_id,
            'drink_id' => $request->drink_id,
            'shots' => $request->shots,
            'price_per_shot' => $request->price_per_shot
        ]);

        return response()->json('Liqour added',201);
    }

    public function update(Request $request,$id){
        Liquor::where('id',$id)->update([
            'place_id' => $request->place_id,
            'drink_id' => $request->drink_id,
            'shots' => $request->shots,
            'price_per_shot' => $request->price_per_shot
        ]);

        return \response()->json('Liqour updated',200);
    }

    public function delete(Request $request,$id){
        Liquor::where('id',$id)->delete();
        return \response()->json('Liquor deleted',200);
    }


}
