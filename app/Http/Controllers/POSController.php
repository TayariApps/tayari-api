<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\{ Menu, User, Sale, Place, Table, Drink, DrinkOrder, DrinkStock, OrderItem, Order, UserCoupon, SystemConstant};

class POSController extends Controller
{
    public function store(Request $request){

        $somedata = $request->input();
        $somedata = file_get_contents("php://input");
        $cont = json_decode($somedata);
        $place = Place::where('id', $request->place_id)->first();
        $now = Carbon::now();

        $cost = 0.00;
        $productTotal = 0;

        $order = Order::create([
            'table_id' => $cont->table_id,
            'place_id' => $request->place_id,
            'executed_time' => $cont->executed_time,
            'waiting_time' => $cont->waiting_time,
            'order_created_by' => $request->user()->id,
            'completed_time' => $now->addMinutes($cont->waiting_time)->toDateTimeString(),
            'type' => $request->type,
            'payment_method' => $request->method //method of payment
        ]);

        $constant = SystemConstant::where('id', 1)->first();
        $totalCost = 0.0;

        if($request->has('drinks')){

            foreach ($cont->drinks as $drink) { 
                
                $drinkstock = DrinkStock::where([
                    'drink_id' => $drink->id, 
                    'place_id' => $request->place_id
                ])->first();

                $drinkstock->update([
                    'quantity' => $drinkstock->quantity - $drink->quantity
                ]);

                DrinkOrder::create([
                    'drink_id' => $drink->id,
                    'order_id' => $order->id,
                    'quantity' => $drink->quantity,
                    'price' => $drinkstock->selling_price
                ]);
    
                $cost += $drinkstock->selling_price;
                $totalCost += $drinkstock->selling_price;
                $productTotal += $drink->quantity;
            }

        }


        if($request->has('foods')){

            foreach ($cont->foods as $item) {

                $menu = Menu::where('id', $item->id)->first();

                $orderItem = OrderItem::create([
                    'menu_id' => $item->id, 
                    'order_id' => $order->id, 
                    'quantity' => $item->quantity, 
                    'cost' => ($item->price - ($item->price * $menu->discount)) * $item->quantity 
                ]);
                
                $cost += $orderItem->cost;
                $totalCost += $constant->discount_active ? $item->price * $constant->discount : 0;
                $productTotal += $orderItem->quantity;
            }

        }

        $order->update([
            'cost' => $cost,
            'total_cost' => $cost + $totalCost,
            'order_number' => "TYR-".$order->id,
            'product_total' => $productTotal
        ]);

        $newOrder = Order::where('id', $order->id)->with(['food','drinks','table','place','customer'])->first();

        return response()->json($newOrder, 201);
    }
}
