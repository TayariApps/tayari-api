<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Place;
use App\Models\Type;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\DrinkType;
use App\Models\DrinkStock;
use App\Models\Menu;
use App\Models\Sale;
use App\Models\Table;
use Illuminate\Support\Facades\Validator;

class PlaceController extends Controller
{
    public function index(){
        return \response()->json(Place::withAvg('reviewed as reviewAverage', 'rating')->get(), 200);
    }

    public function changeStatus(Request $request){
        $place = Place::where('id', $request->place_id)->first();

        $place->update([
            'active' => !$place->active
        ]);

        return response()->json('Place status updated',200);
    }

    public function dashboardData($id){
        $orders = Order::where('place_id', $id)->withCount('food')->get();
        $reservations = Reservation::where('place_id', $id)->get();
        $menuItems = Menu::where('place_id',$id)->get();
        $sales = Sale::where('place_id',$id)->get();
        $mostSold = [];

        if(Menu::where('place_id', $id)->has('orders')->exists()){
            $mostSold = Menu::where('place_id', $id)->with('orders')->take(3)->get();
        }

        return response()->json([
            'sales' => $sales,
            'types' => Type::where('place_id',$id)->withCount('menus')->get(),
            'orders' => $orders,
            'reservations' => $reservations,
            'menuItemsCount' => count($menuItems),
            'mostSold' => $mostSold,
            'tablesCount' => Table::where('place_id', $id)->count(),
            'typesCount' => Type::where('place_id', $id)->count()
        ],200);
    }

    public function getPlace($id){
        return \response()->json(Place::where('id', $id)->first(),200);
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

        $imageValidator = Validator::make($request->all(), [
            'logo' => 'required|file|max:3000',
            'banner' => 'required|file|max:3000',  
        ]);

        if ($imageValidator->fails()) {
            return response()->json('Logo or banner size should be below 3 MBs', 400);
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

    public function update($id,Request $request){

        $bannerfilename = '';
        $logofilename = '';

        if($request->hasFile('logo')){
            $img_ext = $request->file('logo')->getClientOriginalExtension();
            $logofilename = time() . '.' . $img_ext;
            $logoPath = $request->file('logo')->move(public_path(), $logofilename);//image save public folder
        }

        if($request->hasFile('banner')){
            $img_ext = $request->file('banner')->getClientOriginalExtension();
            $bannerfilename = time() . '.' . $img_ext;
            $bannerPath = $request->file('banner')->move(public_path(), $bannerfilename);//image save public folder
        }

        $place = Place::where('id', $id)->first();
        $place->update([
            'name' => $request->name,
            'country_id' => $request->country_id,
            'address' => $request->address,
            // 'owner_id' => $request->owner_id,
            'policy_url' => $request->policy_url,
            'phone_number' => $request->phone,
            'email' => $request->email,
            'location' => $request->location,
            // 'latitude' => $request->latitude,
            // 'longitude' => $request->longitude,
            'description'=> $request->description,
            'display_name' => $request->display_name,
            'bank_swift_code' => $request->bank_swift_code,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'opening_time' => $request->opening_time,
            'closing_time' => $request->closing_time,
            'reservation_price' => $request->reservation_price,
            'bank_name' => $request->bank_name
        ]);

        return response()->json('Place updated', 200);
    }

    public function placeMenu(Request $request, $id){

        if(DrinkStock::where('place_id','=',$id)->where('quantity', '>', 0)->exists()){

            $drinkTypes = DrinkType::with('drinks.stocks')->with(['drinks' => function($q) use ($id){
                $q->whereHas('stocks', function($query) use ($id){
                    $query->where('place_id', $id);
                });
            }])->get();

            $answer = $drinkTypes->filter(function ($value) {
                return count($value->drinks) > 0;
            })->values()->all();


            $drinks = collect($answer)->map(function($item){

                $newArr = [];
                
                 $newArr['id']= $item->id;
                 $newArr['name']= $item->name;
                 $newArr['drinks'] = $item->drinks->map(function($d){
                    $newArrDrinks = [];

                    $newArrDrinks['name'] = $d->name;
                    $newArrDrinks['image'] = $d->image;
                    $newArrDrinks['buying_price'] = $d->stocks->buying_price;
                    $newArrDrinks['selling_price'] = $d->stocks->selling_price;
                    $newArrDrinks['quantity'] = $d->stocks->quantity;

                    return $newArrDrinks;
                });

                return $newArr;
            });

        }else{
            $drinks = [];
        }

        if(Type::where('place_id', $id)->has('menus')->exists()){
            $food = Type::where('place_id', $id)->with('menus')->get();
        } else{
            $food = [];
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
