<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;

class CountryController extends Controller
{
   public function countries(){
       return response()->json(Country::all(), 200);
   }

   public function store(Request $request){
        Country::create([
            'name' => $request->name,
            'currency' => $request->currency,
            'rate' => $request->rate //based of USD
        ]);

        return response()->json('Country added', 201);
   }

   public function update(Request $request, $id){
        Country::update([
            'name' => $request->name,
            'currency' => $request->currency,
            'rate' => $request->rate //based of USD
        ]);

        return response()->json('Country updated', 200);
   }

   public function delete($id){
       Country::where('id', $id)->delete();
       return response()->json('Country deleted', 200);
   }


}
