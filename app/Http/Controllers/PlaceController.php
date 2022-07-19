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
use App\Models\RestaurantNumber;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;

class PlaceController extends Controller
{
    public function index(){
        return \response()->json(Place::where('active', true)->withAvg('reviewed as reviewAverage', 'rating')->get(), 200);
    }

    public function getDrinkStock($id){
        if(DrinkStock::where('place_id','=',$id)->where('quantity', '>', 0)->exists()){

            $drinkTypes = DrinkType::with('drinks.stocks')->with(['drinks' => function($q) use ($id){
                $q->whereHas('stocks', function($query) use ($id){
                    $query->where('place_id', $id);
                });
            }])->get();

            $answer = $drinkTypes->filter(function ($value) {
                return count($value->drinks) > 0;
            })->values()->all();

            $drinks = $answer;

        }else{
            $drinks = [];
        }

        return response()->json($drinks, 200);
    }
 
    public function changeOpenStatus(Request $request){
        $place = Place::where('id', $request->place_id)->first();

        $place->update([
            'is_open' => !$place->is_open
        ]);

        return response()->json('Place open status updated',200);
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
        $sales = Order::where([
            'place_id' => $id,
            'payment_status' => true
            ])->get();
        $mostSold = [];
        $place = Place::where('id', $id)->first();

        if(Menu::where('place_id', $id)->has('orders')->exists()){
            $mostSold = Menu::where('place_id', $id)->with('orders')->take(3)->get();
        }

        return response()->json([
            'sales' => $sales,
            'place' => $place,
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
        return \response()->json(Place::where('id', $id)->with('phones')->first(),200);
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
            return response()->json('Some details are missing, enter all details', 400);
        }

        $imageValidator = Validator::make($request->all(), [
            'logo' => 'required|image|mimes:jpeg,jpg,png,gif|max:7000',
            'banner' => 'required|image|mimes:jpeg,jpg,png,gif|max:7000',  
        ]);

        if ($imageValidator->fails()) {
            return response()->json('Logo or banner size should be below 7 MBs', 400);
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
            'logo_url' => $logoFilename,
            'discount' => 0.1
        ]);

        if($request->has('phone1')){
            RestaurantNumber::create([
                'place_id' => $place->id,
                'phone' => $request->phone1,
            ]);
        }

        if($request->has('phone2')){
            RestaurantNumber::create([
                'place_id' => $place->id,
                'phone' => $request->phone2,
            ]);
        }

        return response()->json('Place created', 201);
    }

    public function update($id,Request $request){

        $place = Place::where('id', $id)->first();
        $place->update([
            'name' => $request->name,
            'country_id' => $request->country_id,
            'address' => $request->address,
            // 'owner_id' => $request->owner_id,
            'cuisine_id' => $request->cuisine,
            'policy_url' => $request->policy_url,
            'phone_number' => $request->phone,
            'email' => $request->email,
            'location' =>$request->address,
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
            'bank_name' => $request->bank_name,
            'cashier_number' => $request->cashier_number,
            'payment_name' => $request->payment_name,
            'payment_network' => $request->payment_network,
            'payment_number' => $request->payment_number,
            'en' => $request->en == "true" ? true : false,
            'delivery' => $request->delivery == "true" ? true : false
        ]);

        if($request->has('phone1')){
            RestaurantNumber::updateOrCreate([
                'place_id' => $place->id
            ],[
                'phone' => $request->phone1
            ]);
        }

        if($request->hasFile('logo')){
            $img_ext = $request->file('logo')->getClientOriginalExtension();
            $logofilename = time() . '.' . $img_ext;
            $logoPath = $request->file('logo')->move(public_path('images/logos'), $logofilename);//image save public folder

            $place->update([
                'logo_url' => $logofilename
            ]);
        }

        if($request->hasFile('banner')){
            $img_ext = $request->file('banner')->getClientOriginalExtension();
            $bannerfilename = time() . '.' . $img_ext;
            $bannerPath = $request->file('banner')->move(public_path('images/banners'), $bannerfilename);//image save public folder

            $place->update([
                'banner_url' => $bannerfilename
            ]);
        }

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

            $drinks = $answer;

            // $drinks = collect($answer)->map(function($item){

            //     $newArr = [];
                
            //      $newArr['id']= $item->id;
            //      $newArr['name']= $item->name;
            //      $newArr['drinks'] = $item->drinks->map(function($d){
            //         $newArrDrinks = [];

            //         $newArrDrinks['name'] = $d->name;
            //         $newArrDrinks['image'] = $d->image;
            //         $newArrDrinks['buying_price'] = $d->stocks->buying_price;
            //         $newArrDrinks['selling_price'] = $d->stocks->selling_price;
            //         $newArrDrinks['quantity'] = $d->stocks->quantity;

            //         return $newArrDrinks;
            //     });

            //     return $newArr;
            // });

        }else{
            $drinks = [];
        }

        if(Type::where('place_id', $id)->whereRelation('menus', 'status', true)->exists()){
            $food = Type::where('place_id', $id)->with('menus')->whereRelation('menus', 'status', true)->get();

            //add 10% discount
            // $food = collect($result)->map(function($item){
            //     $newFoodTypeArr = [];

            //     $newFoodTypeArr['id'] = $item->id;
            //     $newFoodTypeArr['name'] = $item->name;
            //     $newFoodTypeArr['place_id'] = $item->place_id;
            //     $newFoodTypeArr['menus'] = $item->menus->map(function($f){

            //         $newMenuArr = [];

            //         $newMenuArr['id'] = $f->id;
            //         $newMenuArr['place_id'] = $f->place_id;
            //         $newMenuArr['type_id'] = $f->type_id;
            //         $newMenuArr['menu_name'] = $f->menu_name;
            //         $newMenuArr['size'] = $f->size;
            //         $newMenuArr['description'] = $f->description;
            //         $newMenuArr['banner'] = $f->banner;
            //         $newMenuArr['time_takes_to_make'] = $f->time_takes_to_make;
            //         $newMenuArr['ingredients'] = $f->ingredients;
            //         $newMenuArr['status'] = $f->status;
            //         $newMenuArr['price'] = $f->price - ($f->price * 0.1);

            //         return $newMenuArr;
            //      });

            //     return $newFoodTypeArr;
            // }); 

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
