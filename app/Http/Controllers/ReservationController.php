<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\DrinkStock;
use App\Models\DrinkOrder;
use App\Models\Table;
use App\Models\Place;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function index(){
        \response()->json(Reservation::with(['user', 'table'])->get(),200);
    }

    public function getPlaceReservations($id){
        $reservations = Reservation::where('place_id', $id)->with([
            'user','order','order.food', 'order.drinks'
            ])->get();
        return \response()->json($reservations,200);
    }

    public function getUserReservation(Request $request){
        $reservations = Reservation::where('user_id', $request->user()->id)
                            ->with('place')->get();
        return \response()->json($reservations,200);
    }

    public function mobileStore(Request $request){

        $somedata = $request->input();
        $somedata = file_get_contents("php://input");
        $cont = json_decode($somedata);
        
        $time = $cont->restaurantDate. ' '.$cont->restaurantTime;

        $reservation = Reservation::create([
            'user_id' => $request->user()->id,
            'place_id' => $cont->place_id,
            'note' => $cont->note,
            'people_count' => $cont->people_count,
            'time' => \Carbon\Carbon::parse($time)->toDateTimeString(),
            'table_id' => $cont->table_id
        ]);

        if($request->has('foods') || $request->has('drinks')){

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
                'type' => $request->type,
                'payment_method' => $request->method, //method of payment,
                'reservation_id' => $reservation->id
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
    
            if($request->has('foods')){
    
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
    
            }
    
            $order->update([
                'cost' => $cost,
                'total_cost' => $cost,
                'product_total' => $productTotal
            ]);    

        }

        return \response()->json('Reservation added', 200);
    }

   public function restaurantStore(Request $request){

    $reservation = new Reservation;
    if($request->has('person')){
        $reservation->user_id = $request->person;
    }
    if($request->has('customer_name')){
        $reservation->customer_name = $request->customer_name;
    }
    if($request->has('customer_phone')){
        $reservation->customer_phone !== '' && $request->customer_phone;
    }

    $reservation->place_id = $request->place_id;
    $reservation->time = \Carbon\Carbon::parse($request->time)->toDateTimeString(); 
    $reservation->note = $request->note;
    $reservation->people_count= $request->count;
    $reservation->save();

    return \response()->json('Reservation created',201);
   }
   
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            // 'table_id' => 'required',
            'place_id' => 'required', 
            'time' => 'required', 
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }

        $mil = (int)$request->time;
        $seconds = $mil / 1000000;
        $new = date('Y-m-d H:i:s', $seconds);

        // $checkIfDateIsBooked = Reservation::where([
        //     'table_id' => $request->table_id, 
        //     'arrived' => false, 
        //     ])->whereDate('time',date('Y-m-d', $seconds))->exists();

        // if($checkIfDateIsBooked){
        //     return \response()->json('Table is already booked for today',200);
        // }

        Reservation::create([
            'user_id' => $request->user()->id, 
            // 'table_id' => $request->table_id, 
            'place_id' => $request->place_id,
            'time' => $new, 
            'note' => $request->note 
        ]);

        return \response()->json('Reservation created',201);
    }

    public function update(Request $request,$id){
        Reservation::where('id',$id)->update([
            'user_id' => $request->user_id, 
            // 'table_id' => $request->table_id, 
            'time' => $request->time, 
            'note' => $request->note, 
            'arrived' => $request->arrived
        ]);

        return \response()->json('Reservation updated',200);
    }

    public function delete($id){
        Reservation::where('id',$id)->delete();
        return response()->json('Reservation deleted',200);
    }
}
