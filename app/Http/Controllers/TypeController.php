<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Type;
use App\Models\Menu;
use App\Models\Place;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TypeController extends Controller
{
    public function index(){
        return \response()->json(Type::get(),200);
    }

    public function place($id){

        $types = Type::where('place_id', $id)->with('menus')->get();
        $food = Menu::where('place_id', $id)->get();

        return \response()->json([
            'food' => $food,
            'types' => $types
        ], 200);
    }
    
    public function store(Request $request){
        Type::create([
            'name' => $request->name,
            'place_id' => $request->place_id
        ]);

        return \response()->json('Type created',201);
    }

    public function update(Request $request,$id){
        Type::where('id', $id)->update([
            'name' => $request->name
        ]);

        return \response()->json('Type updated',200);
    }

    public function generateQRCode($id){

        $table = Table::where('id', $id)->with('place')->first();

    }   

    public function delete($id){
        Type::where('id', $id)->delete();
        return \response()->json('Type deleted',200);
    }
}
