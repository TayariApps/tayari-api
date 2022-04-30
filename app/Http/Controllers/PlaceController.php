<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Place;
use App\Models\Type;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\DrinkType;
use Illuminate\Support\Facades\Validator;

class PlaceController extends Controller
{
    public function index(){
        return \response()->json(Place::all(), 200);
    }

    public function dashboardData($id){
        $orders = Order::where('place_id', $id)->get();
        $reservations = Reservation::where('place_id', $id)->get();

        return response()->json([
            'orders' => $orders,
            'reservations' => $reservations
        ],200);
    }

    public function ownerPlaces(Request $request){
        $places  = Place::where('owner_id', $request->user()->id)->with('country')->get();
        return \response()->json($places,200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required', 
            'country_id' => 'required', 
            'address' => 'required',
            'logo' => 'required',
            'banner' => 'required', 
            'policy_url' => 'required', 
            'phone_number' => 'required',
            'email' => 'required',
            'location' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'description'=> 'required',
            'display_name' => 'required',
            'cuisine_id' => 'required'
        ]);
 
        if ($validator->fails()) {
            return response()->json('Failed to save place', 400);
        }

        if($request->hasFile('logo')){
            $img_ext = $request->file('logo')->getClientOriginalExtension();
            $logoFilename = time() . '.' . $img_ext;
            $logoPath = $request->file('logo')->move(public_path('images/logos'), $logoFilename);//image save public folder
        }

        if($request->hasFile('banner')){
            $img_ext = $request->file('banner')->getClientOriginalExtension();
            $bannerFilename = time() . '.' . $img_ext;
            $bannerPath = $request->file('banner')->move(public_path('images/banners'), $bannerFilename);//image save public folder
        }

        $place = Place::create([
            'name' => $request->name,
            'country_id' => $request->country_id,
            'address' => $request->address,
            'owner_id' => $request->user()->id,
            'policy_url' => $request->policy_url,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'location' => $request->location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'description'=> $request->description,
            'display_name' => $request->display_name,
            'cuisine_id' => $request->cuisine_id,
            'banner_url' => $bannerFilename,
            'logo_url' => $logoFilename
        ]);

        return response()->json('Place created', 201);
    }

    public function update(Request $request, $id){

        if($request->hasFile('logo')){
            $img_ext = $request->file('logo')->getClientOriginalExtension();
            $filename = time() . '.' . $img_ext;
            $logoPath = $request->file('logo')->move(public_path(), $filename);//image save public folder
        }

        if($request->hasFile('banner')){
            $img_ext = $request->file('banner')->getClientOriginalExtension();
            $filename = time() . '.' . $img_ext;
            $bannerPath = $request->file('banner')->move(public_path(), $filename);//image save public folder
        }

        Place::where('id', $id)->update([
            'name' => $request->name,
            'country_id' => $request->country_id,
            'address' => $request->address,
            'owner_id' => $request->owner_id,
            'policy_url' => $request->policy_url,
            'phone_number' => $request->phone,
            'email' => $request->email,
            'location' => $request->location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'description'=> $request->description,
            'display_name' => $request->display_name,
            'cuisine_id' => $request->cuisine_id,
            'banner_url' => $bannerPath,
            'logo_url' => $logoPath
        ]);

        return response()->json('Place updated', 200);
    }

    public function placeMenu(Request $request, $id){

        if(Place::where('id', $id)->with([
            'types.menus'
            ])->exists()){
                $place = Place::where('id', $id)->with([
                    'types.menus'
                    ])->first();

                $food = $place->types;
            } else{
                $food = [];
            }

        if(DrinkType::has('drinks.places','=',$id)->exists()){
            $drinks = DrinkType::has('drinks.places','=',$id)->with('drinks.stocks')->get();
        }else{
            $drinks = [];
        }

        return response()->json([
            'food' => $food,
            'drinks' => $drinks
        ], 200);
    }

    public function delete($id){
        Place::where('id', $id)->delete();
        return response()->json('Place deleted',200);
    }


}
