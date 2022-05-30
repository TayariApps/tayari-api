<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Place;
use App\Models\Type;
use App\Models\Menu;
use Illuminate\Support\Facades\Validator;

class DiscountController extends Controller
{
    public function addPlaceDiscount(Request $request){
        $validator = Validator::make($request->all(), [
            'place_id' => 'required',
            'discount' => 'required'
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }        

        $place = Place::where('id', $request->place_id)->first();

        $discount = $request->discount/100;

        $place->update([
            'discount' => $discount 
        ]);

        return \response()->json('Discount added',200);
    }

    
    public function addFoodTypeDiscount(Request $request){
        $validator = Validator::make($request->all(), [
            'type_id' => 'required',
            'discount' => 'required'
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }  

        $type = Type::where('id', $request->type_id)->first();

        $place = Place::where('id', $type->type_id)->first();

        if($place->discount > 0){
            return response()->json('This restaurant already has a discount', 400);
        }

        $type->update([
            'discount' => $request->discount/100
        ]);

        return \response()->json('Discount added',200);
    }

    
    public function addFoodDiscount(Request $request){
        $validator = Validator::make($request->all(), [
            'menu_id' => 'required',
            'discount' => 'required'
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }  
        
        $food = Menu::where('id', $request->menu_id)->first();

        $place = Place::where('id', $food->place_id)->first();

        $type = Type::where('place_id', $place->id)->first();

        if($place->discount > 0){
            return response()->json('This restaurant already has a discount', 400);
        }

        if($type->discount > 0){
            return response()->json('This food type already has a discount', 400);
        }

        $food->update([
            'discount' => $request->discount/100
        ]);

        return \response()->json('Discount added',200);
    }
}
