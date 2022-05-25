<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Order;
use App\Models\Disbursement;
use App\Models\Revenue;
use Illuminate\Support\Facades\Validator;

class SaleController extends Controller
{
    public function index(){
        return \response()->json(Sale::get(),200);
    }

    public function place($placeID){
        return \response()->json(Sale::where('place_id',$placeID)->get(),200);
    }

    public function makeDisbursement(Request $request){
        $validator = Validator::make($request->all(), [
            'place_id' => 'required',
            'amount' => 'required'
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }

        // $tayariCut = $request->amount * 0.02;

        // $disbursementAmount = $request->amount - $tayariCut;

        $tayariCut = (0.02/0.98) * $request->amount;

        $disbursement = Disbursement::create([
            'place_id' => $request->place_id,
            'amount' => $request->amount
        ]);

        Revenue::create([
            'place_id' => $request->place_id,
            'amount' => $tayariCut,
            'disbursement_id' => $disbursement->id
        ]);

        return \response()->json('Disbursement complete',200);
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
            return \response()->json([
                "ResponseCode" => "BILLER-18-3020-E",
                "ResponseStatus" => false,
                "ResponseDescription" => "Callback failed",
                "Message" => "Reference ID does not exist"
            ],400);
        }
          
        if($request->Status == true){
            $sale = Sale::where('reference_id', $request->ReferenceID)->first();

            $sale->update([
                'paid' => true
            ]);

            $order = Order::where('id', $sale->order_id)->first();
        
            $order->update([
                'payment_method' => 2,
                'payment_status' => true,
                'status' => 4,
                'paid' => $sale->amount
            ]);

            return \response()->json([
                "ResponseCode" => "BILLER-18-0000-S",
                "ResponseStatus" => true,
                "ResponseDescription" => "Callback successful",
                "ReferenceID" => $sale->reference_id
            ],200);
        }

        return \response()->json([
            "ResponseCode" => "BILLER-18-3020-E",
            "ResponseStatus" => false,
            "ResponseDescription" => "Callback failed",
            "ReferenceID" => $sale->reference_id
        ],400);
    }
    

}
