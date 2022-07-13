<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DisbursementController extends Controller
{
    public function index(){
        $places = Place::has('disbursements')->withSum('disbursements','amount');
        return response()->json($places,200);
    }

    public function makeDisbursement(Request $request){

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->post('http://3.136.115.91/api/makeTransfer', [
            'phone' => $request->phone,
            'amount' => $request->amount,
            'referenceid' => $request->referenceID
        ]);

        if($response->ok()){
            return \response()->json('Disbursement made',200);
        }

    }
}
