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
        
   }
}
