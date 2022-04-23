<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Delivery;
use Illuminate\Support\Facades\Validator;
use App\Models\DrinkStock;
use App\Models\DrinkDelivery;

class DeliveryController extends Controller
{
    public function index(){
        return \response()->json(Delivery::get(),200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'excecuted_time' => 'required', 
            'customer_id' => 'required',
            'items' => 'required'
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }

        if($request->has('drinks')){
            foreach ($request->drinks as $key => $drink) {
                $drinkstock = DrinkStock::where(['drink_id' => $drink->drink_id, 'place_id' => $request->place_id])->first();
                $drinkstock->update([
                    'quantity' => $drinkstock->quantity - $drink->quantity
                ]);
            }
        }

        $delivery = Delivery::create([
            'executed_time' => $request->executed_time,
            'customer_id' => $request->customer_id,
            'waiting_time' => $request->waiting_time,
            'order_created_by' => $request->user()->id
        ]);

        foreach ($request->drinks as $key => $drink) {
            $drinkstock = DrinkStock::where(['drink_id' => $drink->drink_id, 'place_id' => $request->place_id])->first();

            DrinkDelivery::create([
                'drink_id' => $drink->drink_id,
                'delivery_id' => $delivery->id,
                'quantity' => $drink->quantity,
                'price' => $drinkstock->selling_price
            ]);

            $cost += $drinkstock->selling_price;
        }

        foreach ($request->items as $key => $item) {
            DeliveryItem::create([
                'menu_id' => $item->menu_id, 
                'delivery_id' => $delivery->id, 
                'quantity' => $item->quantity, 
                'cost' => $item->cost
            ]);

            $cost += $item->cost;
        }

        $delivery->update([
            'cost' => $cost,
            'total_cost' => $cost,
            'product_total' => count($request->items)
        ]);

        return response()->json('Delivery created', 200);


    }
}
