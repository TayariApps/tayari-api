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
use App\Models\ReservationFood;
use App\Models\ReservationDrink;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\SMSController;

class ReservationController extends Controller
{
    public function index(){
        \response()->json(Reservation::with(['user', 'table'])->get(),200);
    }

    public function getPlaceReservations($id){
        $reservations = Reservation::where('place_id', $id)->with([
            'user','food.menu', 'drinks.drink'
            ])->orderBy('id', 'desc')->get();
        return \response()->json($reservations,200);
    }

    public function getReservationData($id){
        $reservation = Reservation::where('id', $id)->with([
            'place', 'food.menu', 'drinks.drink'
        ])->first();

        return \response()->json($reservation,200);
    }

    public function deleteFoodFromReservation(Request $request){
        ReservationFood::where([
            'reservation_id' => $request->reservation_id,
            'menu_id' => $request->menu_id
            ])->delete();

            return \response()->json('Food removed from reservation',200);
    }

    public function deleteDrinkFromReservation(Request $request){
        ReservationDrink::where([
            'reservation_id' => $request->reservation_id,
            'drink_id' => $request->drink_id
        ])->delete();

        return \response()->json('Drink removed from reservation',200);
    }

    public function getUserReservation(Request $request){
        $reservations = Reservation::where('user_id', $request->user()->id)
                            ->with(['place','food.menu','drinks.drink'])->get();
        return \response()->json($reservations,200);
    }

    public function addItemsToReservation(Request $request){

        $somedata = $request->input();
        $somedata = file_get_contents("php://input");
        $cont = json_decode($somedata);

        $reservation = Reservation::where('id', $request->reservation_id)->first();

        if($request->has('food')){

            foreach ($cont->food as $item) {
                ReservationFood::create([
                    'menu_id' => $item->id, 
                    'quantity' => $item->quantity, 
                    'cost' => $item->price, 
                    'reservation_id' => $reservation->id
                ]);
            } 
        }

        if($request->has('drinks')){
                
            foreach ($cont->drinks as $drink) {
                ReservationDrink::create([
                    'drink_id' => $drink->id, 
                    'reservation_id' => $reservation->id, 
                    'quantity' => $drink->quantity, 
                    'cost' => $drink->price
                ]);
            }

        }

        return \response()->json('Reservation items added', 200);
    }
    
    public function mobileStore(Request $request){

        $somedata = $request->input();
        $somedata = file_get_contents("php://input");
        $cont = json_decode($somedata);
        
        $time = $cont->reservationDate.' '.$cont->reservationTime;

        $reservation = Reservation::create([
            'user_id' => $request->user()->id,
            'place_id' => $cont->place_id,
            'note' => $cont->note,
            'people_count' => $cont->people_count,
            'time' => \Carbon\Carbon::parse($time)->toDateTimeString(),
            'table_id' => $cont->table_id
        ]);

        if($request->has('foods')){

            foreach ($cont->foods as $item) {
                ReservationFood::create([
                    'menu_id' => $item->id, 
                    'quantity' => $item->quantity, 
                    'cost' => $item->price, 
                    'reservation_id' => $reservation->id
                ]);
            } 
        }

        if($request->has('drinks')){
                
            foreach ($cont->drinks as $drink) {
                ReservationDrink::create([
                    'drink_id' => $drink->id, 
                    'reservation_id' => $reservation->id, 
                    'quantity' => $drink->quantity, 
                    'cost' => $drink->price
                ]);
            }

        }

        if($reservation->place->cashier_number !== null){
            $smsController = new SMSController();
            $smsController->sendMessage(null, "A reservation has been made. Please check the restuarant dashboard.", $reservation->place->cashier_number);
        }

        return \response()->json('Reservation added', 200);
    }

   public function restaurantStore(Request $request){
    date_default_timezone_set('Africa/Dar_es_Salaam');

    $validator = Validator::make($request->all(), [
        'people_count' => 'required', 
        'time' => 'required', 
    ]);

    if($validator->fails()){
        return response()->json('Please enter all details', 400);
    }

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
    $reservation->people_count= $request->people_count;
    $reservation->save();

    return \response()->json('Reservation created',201);
   }
   
    public function store(Request $request){
        date_default_timezone_set('Africa/Dar_es_Salaam');
        
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
