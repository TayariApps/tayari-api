<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;

class TableController extends Controller
{
    public function index(){
        return \response()->json(Table::get(),200);
    }

    public function generateQRCode($id){

        $table = Table::where('id', $id)->with('place')->first();

        if($table->qr_code == null){
            \QrCode::size(100)
                ->format('svg')
                ->generate("https://tayari.co.tz|$table->place_id|$table->id", 'images/tables/'.$table->id.$table->place_id.'.svg');

            $table->update([
                'qr_code' => 'images/tables/'.$table->id.$table->place_id.'.svg'
            ]);

            return \response()->json('QR Code generated',200);
        }
        return response()->json('QR Code already exists', 200);
    } 

    public function placeTables($placeID){

        $checkIfTablesExists = Table::where('place_id', $placeID)->exists();

        if($checkIfTablesExists){
            return \response()->json(Table::where('place_id', $placeID)->get(),200);
        }

        return \response()->json([],200);
    }

    public function getTableOrders($placeID){
        $tables = Table::where('place_id', $placeID)->with(['orders.customer','orders.food','orders.drinks'])->get();
                // ->with(['orders' => function($q){
                //     $q->whereDate('created_at', Carbon::today());
                // }])->get();

        return \response()->json($tables,200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'place_id' => 'required', 
            'table_name' => 'required'
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }

        $table = Table::firstOrCreate([
            'place_id' => $request->place_id, 
            'table_name' => $request->table_name
        ]);

        \QrCode::format('svg')->size(200)
            ->generate("https://tayari.co.tz-".$table->place_id."-".$table->id, "images/tables/".$table->place_id."-".$table->id.".svg");

        $table->update([
            'qr_code' => "images/tables/".$table->place_id."-".$table->id.".svg"
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

    public function script(){
        $tables = Table::all();

        foreach ($tables as $table) {

            \QrCode::format('svg')->size(200)
            ->generate("https://tayari.co.tz-".$table->place_id."-".$table->id, "images/tables/".$table->place_id."-".$table->id.".svg");

            $table->update([
                'qr_code' => "images/tables/".$table->place_id."-".$table->id.".svg"
            ]);
            
            }

            return \response()->json('Complete',200);
    }

    public function delete($id){
        
        DB::beginTransaction();

        try {
            $table = Table::where('id',$id)->delete();   
            DB::commit();

            return \response()->json('Table deleted',200);

        } catch (\Throwable $e) {
            DB::rollback();
            return \response()->json('Table cannot be deleted because it has been used in the system',400);
        }
            
    }

}
