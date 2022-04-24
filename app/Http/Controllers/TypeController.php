<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Type;

class TypeController extends Controller
{
    public function index(){
        return \response()->json(Type::get(),200);
    }

    public function store(Request $request){
        Type::create([
            'name' => $request->name,
            'place_id' => $request->place_id
        ]);

        return \response()->json('Type created',201);
    }
}
