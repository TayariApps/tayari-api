<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuisine;

class CuisineController extends Controller
{
    public function index(){
        return \response()->json(Cuisine::all(), 200);
    }

    public function store(Request $request){

        if($request->hasFile('image')){
            $img_ext = $request->file('image')->getClientOriginalExtension();
            $filename = time() . '.' . $img_ext;
            $path = $request->file('image')->move(public_path(), $filename);//image save public folder
        }

        Cuisine::create([
            'name' => $request->name,
            'image' => $path
        ]);

        return \response()->json('Cuisine created', 201);
    }

    public function update(Request $request, $id){

        if($request->hasFile('image')){
            $img_ext = $request->file('image')->getClientOriginalExtension();
            $filename = time() . '.' . $img_ext;
            $path = $request->file('image')->move(public_path(), $filename);//image save public folder
        }

        Cuisine::where('id', $id)->create([
            'name' => $request->name,
            'image' => $path
        ]);

        return \response()->json('Cuisine updated', 200);
    }

    public function delete($id){
        Cuisine::where('id', $id)->delete();
        return \response()->json('Delete cuisine', 200);
    }
}