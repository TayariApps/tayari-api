<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\DrinkStock;
use App\Models\DrinkOrder;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index(){
        return response()->json(Order::all(),200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'table_id' => 'required', 
            'executed_time' => 'required', 
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

        $order = Order::create([
            'executed_time' => $request->executed_time,
            'customer_id' => $request->customer_id,
            'waiting_time' => $request->waiting_time,
            'order_created_by' => $request->user()->id
        ]);

        $cost = 0.00;

        foreach ($request->drinks as $key => $drink) {
            $drinkstock = DrinkStock::where(['drink_id' => $drink->drink_id, 'place_id' => $request->place_id])->first();

            DrinkOrder::create([
                'drink_id' => $drink->drink_id,
                'order_id' => $order->id,
                'quantity' => $drink->quantity,
                'price' => $drinkstock->selling_price
            ]);

            $cost += $drinkstock->selling_price;
        }

        foreach ($request->items as $key => $item) {
            OrderItem::create([
                'menu_id' => $item->menu_id, 
                'order_id' => $order->id, 
                'quantity' => $item->quantity, 
                'cost' => $item->cost
            ]);

            $cost += $item->cost;
        }

        $order->update([
            'cost' => $cost,
            'total_cost' => $cost,
            'product_total' => count($request->items)
        ]);

        return response()->json('Order created', 200);

    }
}
