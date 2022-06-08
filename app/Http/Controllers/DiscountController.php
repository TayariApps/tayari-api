<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Place;
use App\Models\Type;
use App\Models\SystemConstant;
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

        $food = Menu::where('place_id', $place->id)->get();

        if($place->place_discount > 0){

            $place->update([
                'discount' => $place->discount - $place->place_discount
            ]);

            foreach ($food as $meal) {
                $meal->update([
                     'discount' => $meal->discount - $place->place_discount
                ]);
             }     
        }

        $place->update([
            'place_discount' => $request->discount/100,
            'discount' => $request->discount/100 + $place->discount
        ]);

        foreach ($food as $meal) {
            $meal->update([
                 'discount' => $meal->discount + $place->discount
            ]);
         }

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

        $food = Menu::where('type_id', $type->id)->get();

        if($type->discount > 0){

            $type->update([
                'discount' => $type->discount - $type->type_discount
            ]);


            foreach ($food as $meal) {
                $meal->update([
                 'discount' => $meal->discount - $type->discount
                ]);
             }     

        }

        $type->update([
            'type_discount' => $request->discount/100,
            'discount' => $request->discount/100 + $type->discount
        ]);

        foreach ($food as $meal) {
            $meal->update([
             'discount' => $meal->discount + $type->discount
            ]);
         }

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
        
        $food = Menu::where('id', $request->menu_id)->with(['type', 'place'])->first();

       if($food->discount > 0){
        $food->update([
            'discount' => $food->discount - $food->food_discount
        ]);
       }

       $food->update([
        'food_discount' => $request->discount/100,
        'discount' => $request->discount/100 + $food->discount
    ]);

        return \response()->json('Discount added',200);
    }
}
