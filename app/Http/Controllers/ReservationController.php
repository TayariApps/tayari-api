<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Place;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    public function index(){
        \response()->json(Reservation::with(['user', 'table'])->get(),200);
    }

    public function getPlaceReservations($id){
        $reservations = Reservation::where('place_id', $id)->get();

        return \response()->json($reservations,200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'table_id' => 'required',
            'place_id' => 'required', 
            'time' => 'required', 
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }

        $mil = (int)$request->time;
        $seconds = $mil / 1000000;
        $new = date('Y-m-d H:i:s', $seconds);

        $checkIfDateIsBooked = Reservation::where([
            'table_id' => $request->table_id, 
            'arrived' => false, 
            ])->whereDate('time',date('Y-m-d', $seconds))->exists();

        if($checkIfDateIsBooked){
            return \response()->json('Table is already booked for today',200);
        }

        Reservation::create([
            'user_id' => $request->user()->id, 
            'table_id' => $request->table_id, 
            'place_id' => $request->place_id,
            'time' => $new, 
            'note' => $request->note 
        ]);

        return \response()->json('Reservation created',201);
    }

    public function update(Request $request,$id){
        Reservation::where('id',$id)->update([
            'user_id' => $request->user_id, 
            'table_id' => $request->table_id, 
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