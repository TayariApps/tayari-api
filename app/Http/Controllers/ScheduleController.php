<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{ Schedule, Place };
use Carbon\Carbon;
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

        $dayInWeek = Carbon::now()->format('l');
        $place = Place::where('id', $request->place)->first();

       if($request->has('monday')){  
            $dayInWeek == "Monday" && $place->update([
                    'is_open' => $request->monday
            ]);
       }

       if($request->has('tuesday')){  
            $dayInWeek == "Tuesday" && $place->update([
                    'is_open' => $request->tuesday
            ]);
       }

       if($request->has('wednesday')){  
            $dayInWeek == "Wednesday" && $place->update([
                'is_open' => $request->wednesday
            ]);
        }

        if($request->has('thursday')){  
            $dayInWeek == "Thursday" && $place->update([
                'is_open' => $request->thursday
            ]);
        }

        if($request->has('friday')){  
            $dayInWeek == "Friday" && $place->update([
                'is_open' => $request->friday
            ]);
        }
        if($request->has('saturday')){  
            $dayInWeek == "Saturday" && $place->update([
                'is_open' => $request->saturday
            ]);
        }

        if($request->has('sunday')){  
            $dayInWeek == "Sunday" && $place->update([
                'is_open' => $request->sunday
            ]);
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
