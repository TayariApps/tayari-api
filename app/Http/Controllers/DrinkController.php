<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Drink;
use App\Models\Place;
use App\Models\DrinkStock;
use App\Models\DrinkType;
use Illuminate\Support\Facades\Validator;

class DrinkController extends Controller
{
    public function index(){

        $drinks = DrinkType::with('drinks')->get();

        return \response()->json($drinks,200);
    }

    public function getDrinks(){
        return \response()->json(Drink::get(),200);
    }

    public function storeDrinkType(Request $request){
        DrinkType::firstOrCreate([
            'name' => $request->name
        ]);

        return \response()->json('Drink type created',200);
    }

    public function place($id){
        $place = Place::where('id', $id)->with('drinks')->first();
        return response()->json($place->drinks,200);
    }

    public function update(Request $request, $id){
        $drink = Drink::where('id', $id)->first();

        if($request->hasFile('image')){
            $img_ext = $request->file('image')->getClientOriginalExtension();
            $filename = time() . '.' . $img_ext;
            $path = $request->file('image')->move(public_path('images/drinks'), $filename);//image save public folder

            $drink->update([
                'name' => $request->name,
                'image' => $filename,
                'volume' => $request->volume, 
                'type_id' => $request->type
            ]);

            return \response()->json('Drink updated',200);
        }

        $drink->update([
            'name' => $request->name,
            'volume' => $request->volume, 
            'type_id' => $request->type
        ]);

        return \response()->json('Drink updated',200);
    }
    
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required', 
            'volume' => 'required', 
            'image' => 'required', 
            'type' => 'required'
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }

        if($request->hasFile('image')){
            $img_ext = $request->file('image')->getClientOriginalExtension();
            $filename = time() . '.' . $img_ext;
            $path = $request->file('image')->move(public_path('images/drinks'), $filename);//image save public folder
        }

        Drink::create([
            'name' => $request->name, 
            'volume' => $request->volume, 
            'image' => $filename, 
            'type_id' => $request->type
        ]);

        return response()->json('Drink added',201);
    }

    public function createStock(Request $request){

        $checkIfStockExists = DrinkStock::where([
            'place_id' => $request->place_id,
            'drink_id' => $request->drink_id
        ])->exists();

        if($checkIfStockExists){
            return \response()->json('Drink stock already exists',400);
        }

        DrinkStock::create([
            'place_id' => $request->place_id,
            'drink_id' => $request->drink_id,
            'quantity' => $request->quantity ?? 0,
            'buying_price' => $request->buying_price,
            'selling_price' => $request->selling_price
        ]);

        return response()->json('Drink stock created', 201);
    }

    public function addStock(Request $request){
        $stock = DrinkStock::where(['place_id' => $request->placeId, 'drink_id' => $request->drinkId])->first();

        $stock->update([
            'quantity' => $stock->quantity + (int)$request->quantity
        ]);

        return \response()->json('Stock added', 200);
    }

    public function updateStock(Request $request){
        DrinkStock::where([
            'place_id' => $request->placeId, 
            'drink_id' => $request->drinkId
            ])->update([
                'quantity' => $request->quantity,
                'buying_price' => $request->buying_price,
                'selling_price' => $request->selling_price
            ]);

        return response()->json('Stock updated', 200);
    }

    public function delete($id){
        Drink::where('id', $id)->delete();
        return response()->json('Delete drink', 200);
    }
}
