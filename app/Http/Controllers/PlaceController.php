<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Place;
use App\Models\Type;
use Illuminate\Support\Facades\Validator;

class PlaceController extends Controller
{
    public function index(){
        return \response()->json(Place::all(), 200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required', 
            'country_id' => 'required', 
            'address' => 'required',
            'owner_id' => 'required',
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
            return response()->json('Failed to save place', 200);
        }

        if($request->hasFile('logo')){
            $img_ext = $request->file('logo')->getClientOriginalExtension();
            $filename = time() . '.' . $img_ext;
            $logoPath = $request->file('logo')->move(public_path(), $filename);//image save public folder
        }

        if($request->hasFile('logo')){
            $img_ext = $request->file('logo')->getClientOriginalExtension();
            $filename = time() . '.' . $img_ext;
            $bannerPath = $request->file('logo')->move(public_path(), $filename);//image save public folder
        }

        Place::create([
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
        $place = Place::where('id', $id)->with([
            'types.menus'
            ])->first();
        return response()->json($place->types, 200);
    }

    public function delete($id){
        Place::where('id', $id)->delete();
        return response()->json('Place deleted',200);
    }


}
