<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\DrinkStock;
use App\Models\DrinkOrder;
use App\Models\Table;
use App\Models\Place;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index(){
        return response()->json(Order::all(),200);
    }

    public function restaurantConfirmPayment($id){
        Order::where('id', $id)->update([
            'payment_method' => 1,
            'payment_status' => true
        ]);

        return response()->json('Payment complete',200);
    }

    public function placeOrders($id){
        $orders = Order::where('place_id', $id)->with(['customer', 'drinks', 'food','table'])->withCount('food')->get();
        return \response()->json($orders,200);
    }

    public function userOrders(Request $request){
        
        $checkIfOrderExists = Order::where('customer_id', $request->user()->id)->exists();

        if($checkIfOrderExists){
            $orders = Order::where('customer_id', $request->user()->id)->with(['food','drinks','table.place'])->get();
            return \response()->json($orders,200);
        }

        return \response()->json([],200);
       
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

        $now = Carbon::now();
        $table = Table::where('id', $cont->table_id)->first();

        $cost = 0.00;
        $productTotal = 0;

        $order = Order::create([
            'table_id' => $table->id,
            'place_id' => $request->place_id,
            'executed_time' => $cont->executed_time,
            'customer_id' => $cont->customer_id,
            'waiting_time' => $cont->waiting_time,
            'order_created_by' => $request->user()->id,
            'completed_time' => $now->addMinutes($cont->waiting_time)->toDateTimeString(),
            'type' => $request->type
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

                $productTotal += $drink->quantity;
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
            $orderItem = OrderItem::create([
                'menu_id' => $item->id, 
                'order_id' => $order->id, 
                'quantity' => $item->quantity, 
                'cost' => $item->price * $item->quantity
            ]);

            $cost += $orderItem->cost;
            $productTotal += $orderItem->quantity;
        }

        $order->update([
            'cost' => $cost,
            'total_cost' => $cost,
            'product_total' => $productTotal
        ]);

        $newOrder = Order::where('id', $order->id)->with(['food','drinks','table.place'])->first();

        return response()->json($newOrder, 201);
    }
}
