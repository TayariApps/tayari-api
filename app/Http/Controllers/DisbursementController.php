<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Place;
use Carbon\Carbon;
use App\Http\Controllers\SMSController;

class DisbursementController extends Controller
{
    public function index(){
        $places = Place::has('disbursements')->withSum('disbursements','amount');
        return response()->json($places,200);
    }

    public function makeDisbursement($phone, $amount, $refID, $placeID){

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->post('http://3.136.115.91/api/makeTransfer', [
            'phone' => $phone,
            'amount' => $amount,
            'referenceid' => $refID
        ]);

        if($response->ok()){

            $place = Place::where('id', $placeID)->first();
            $date = Carbon::now()->toDateTimeString();

            $txtBody = "Successful disbursement of TZS $amount has been made to $place->name from TAYARI PAYMENTS.";
            $txtBody .= "\n REF: $refID";
            $txtBody .= "\n MOB: $phone";
            $txtBody .= "\n DATE: $date"; 

            $smsController = new SMSController();
            $smsController->sendMessage(null, $txtBody, $phone);
            $smsController->sendMessage(null, $txtBody, "255714779397");

            return \response()->json('Disbursement made',200);
        }

    }
}
