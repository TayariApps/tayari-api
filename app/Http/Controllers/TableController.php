<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;
use Illuminate\Support\Facades\Validator;

class TableController extends Controller
{
    public function index(){
        return \response()->json(Table::get(),200);
    }

    public function placeTables($placeID){
        return \response()->json(Table::where('place_id', $placeID)->get(),200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'place_id' => 'required', 
            'table_name' => 'required'
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }

        Table::create([
            'place_id' => $request->place_id, 
            'table_name' => $request->table_name
        ]);

        return \response()->json('Table created',201);
    }

    public function update(Request $request,$id){
        Table::where('id', $id)->update([
            'place_id' => $request->place_id, 
            'table_name' => $request->table_name
        ]);

        return \response()->json('Table updated',200);
    }

    public function delete($id){
        Table::where('id',$id)->delete();

        return \response()->json('Table deleted',200);
    }

}
