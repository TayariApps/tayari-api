<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Help;

class HelpController extends Controller
{
    public function store(Request $request){
        Help::create([
            'user_id' => $request->user()->id, 
            'place_id' => $request->place_id, 
            'message' => $request->message
        ]);

        return \response()->json('Message sent',200);
    }
}
