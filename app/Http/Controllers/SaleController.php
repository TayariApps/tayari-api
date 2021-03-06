<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Sale, Order, Disbursement, Revenue, SystemConstant, Place};
use Carbon\Carbon;
use Illuminate\Support\Facades\{Validator, Http};
use App\Http\Controllers\{InvoiceController, DisburementController, SMSController};

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
            'amount' => 'required',
            'phone' => 'required'
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }

        if(strlen($request->phone) !== 12){
            return response()->json('Phone number should be in the format of 255XXX...', 400);
        }

        $constant = SystemConstant::where('id', 1)->first();

        $tayariCut = ( $constant->payment_cut / (1 - $constant->payment_cut) ) * $request->amount;

        $refID = "TYR". rand( 10000000 , 99999999);

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->post('http://3.136.115.91/api/makeTransfer', [
            'phone' => $request->phone,
            'amount' => $request->amount,
            'referenceid' => $refID
        ]);

        $txnstatus = json_decode($response->body(), true)['TXNSTATUS'];

        if($txnstatus == "200"){

            $place = Place::where('id', $request->place_id)->first();
            $date = Carbon::now()->toDateTimeString();

            $txtBody = "Successful disbursement of TZS $request->amount has been made to $place->name from TAYARI PAYMENTS.";
            $txtBody .= "\n REF: $refID";
            $txtBody .= "\n MOB: $request->phone";
            $txtBody .= "\n DATE: $date"; 

            $smsController = new SMSController();
            $smsController->sendMessage(null, $txtBody, $request->phone);
            $smsController->sendMessage(null, $txtBody, "255714779397");

            $disbursement = Disbursement::create([
                'place_id' => $request->place_id,
                'amount' => $request->amount,
                'ref_id' => $refID
            ]);
    
            Revenue::create([
                'place_id' => $request->place_id,
                'amount' => $tayariCut,
                'disbursement_id' => $disbursement->id
            ]);

            // $disbursementController = new DisbursementController();
            // $disbursementController->makeDisbursement($request->phone, $request->amount, $refID,  $request->place_id);

            $invoiceController = new InvoiceController();
            $invoiceController->storeInvoice($request->place_id, $disbursement->id, $request->amount);

            return \response()->json('Disbursement complete',200);
        } else{
            return \response()->json('Disbursement failed',400);
        }
       
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
            'phone_number' => $request->CustomerMSISDN, 
            'place_id' => $order->place_id
        ]);

        return \response()->json('Response saved', 200);
    }

    public function mobileCallback(Request $request){
        date_default_timezone_set('Africa/Dar_es_Salaam');

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

            //successful sms text
            $date = Carbon::now()->toDateTimeString();
            $place = Place::where('id', $sale->place_id)->first();

            $txt = "Successful payment to $place->name via TAYARI PAYMENTS.";
            $txt .= "\n REF: $sale->reference_id";
            $txt .= "\n AMOUNT: TZS $sale->amount";
            $txt .= "\n MOB: $sale->phone_number";
            $txt .= "\n DATE: $date";

            $smsController = new SMSController();
            $smsController->sendMessage(null, $txt, $sale->phone_number);

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
