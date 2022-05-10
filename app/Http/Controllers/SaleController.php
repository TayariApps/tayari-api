<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Order;

class SaleController extends Controller
{
    public function index(){
        return \response()->json(Sale::get(),200);
    }

    public function place($placeID){
        return \response()->json(Sale::where('place_id',$placeID)->get(),200);
    }

    public function checkOrder($orderID){
        $sale = Sale::where('order_id', $orderID)->first();

        return \response()->json([
            'order' => $sale->order,
            'sale' => $sale
        ],200);
        
    }

    public function mobilePayment(Request $request){

        $order = Order::where('id', $request->orderID)->first();
        
        Sale::create([
            'order_id' => $order->id, 
            'code' => $request->ResponseCode, 
            'amount' => $request->amount, 
            'reference_id' => $request->ReferenceID, 
            'type' => 2, 
            'phone_number' => $request->CustomerMSIDN, 
            'place_id' => $order->place_id
        ]);

        return \response()->json('Response saved', 200);
    }

    public function mobileCallback(Request $request){

        $checkIfReferenceIDexists = Sale::where('reference_id', $request->ReferenceID)->exists();

        if(!$checkIfReferenceIDexists){
            return;
        }
          
        if($request->Status == true){
            $sale = Sale::where('reference_id', $request->ReferenceID)->first();

            $sale->update([
                'paid' => true
            ]);

            $order = Order::where('id', $sale->order_id)->first();
        
            $order->update([
                'payment_method' => 2,
                'payment_status' => true
            ]);

            return \response()->json('Callback success',200);
        }

        return \response()->json('Callback failed',400);
    }
    

}
