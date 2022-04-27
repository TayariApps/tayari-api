<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\DrinkStock;
use App\Models\DrinkOrder;
use App\Models\Table;
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
            'foods' => 'required'
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }

        $somedata = $request->input();
        $somedata = file_get_contents("php://input");
        $cont = json_decode($somedata);

        $table = Table::where('id', $cont->table_id)->first();

        $cost = 0.00;

        $order = Order::create([
            'table_id' => $table->id,
            'executed_time' => $cont->executed_time,
            'customer_id' => $cont->customer_id,
            'waiting_time' => $cont->waiting_time,
            'order_created_by' => $request->user()->id
        ]);


        if($request->has('drinks')){
            foreach ($cont->drinks as $drink) {
                $drinkstock = DrinkStock::where([
                    'drink_id' => $drink->id, 
                    'place_id' => $table->place_id
                ])->first();

                $drinkstock->update([
                    'quantity' => $drinkstock->quantity - $drink->quantity
                ]);
            }

            foreach ($cont->drinks as $drink) {
                $drinkstock = DrinkStock::where([
                    'drink_id' => $drink->id, 
                    'place_id' => $table->place_id
                ])->first();
    
                DrinkOrder::create([
                    'drink_id' => $drink->id,
                    'order_id' => $order->id,
                    'quantity' => $drink->quantity,
                    'price' => $drinkstock->selling_price
                ]);
    
                $cost += $drinkstock->selling_price;
            }
    
        }
        
        foreach ($cont->foods as $item) {
            OrderItem::create([
                'menu_id' => $item->id, 
                'order_id' => $order->id, 
                'quantity' => $item->quantity, 
                'cost' => $item->price
            ]);

            $cost += $item->price;
        }

        $order->update([
            'cost' => $cost,
            'total_cost' => $cost,
            'product_total' => count($cont->foods)
        ]);

        $newOrder = Order::where('id', $order->id)->with(['food','drinks'])->first();

        return response()->json($newOrder, 200);

    }
}
