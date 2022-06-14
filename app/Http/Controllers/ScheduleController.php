<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{ Schedule, Place };
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
{
    public function getSchedule($placeID){
        $schedule = Schedule::where('place_id', $placeID)->get();

        return \response()->json($schedule,200);
    }

    public function update(Request $request){

        $validator = Validator::make($request->all(), [
            'monday' => 'required',
            'tuesday' => 'required',
            'wednesday' => 'required',
            'thursday' => 'required',
            'friday' => 'required',
            'saturday' => 'required',
            'sunday' => 'required',
            'place' => 'required'
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }

        Schedule::updateOrCreate([
            'place_id' => $request->place,
            'day_id' => 1,
        ],['open' => $request->monday]);

        Schedule::updateOrCreate([
            'place_id' => $request->place,
            'day_id' => 2,
        ],['open' => $request->tuesday]);

        Schedule::updateOrCreate([
            'place_id' => $request->place,
            'day_id' => 3,
        ],['open' => $request->wednesday]);

        Schedule::updateOrCreate([
            'place_id' => $request->place,
            'day_id' => 4,
        ],['open' => $request->thursday]);

        Schedule::updateOrCreate([
            'place_id' => $request->place,
            'day_id' => 5,
        ],['open' => $request->friday]);

        Schedule::updateOrCreate([
            'place_id' => $request->place,
            'day_id' => 6,
        ],['open' => $request->saturday]);

        Schedule::updateOrCreate([
            'place_id' => $request->place,
            'day_id' => 7,
        ],['open' => $request->sunday]);

        return \response()->json('Schedule updated',200);

    }
}
