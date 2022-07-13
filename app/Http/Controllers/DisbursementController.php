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

    public function makeDisbursement($phone, $amount, $refID){

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->post('http://3.136.115.91/api/makeTransfer', [
            'phone' => $phone,
            'amount' => $amount,
            'referenceid' => $refID
        ]);

        if($response->ok()){
            return \response()->json('Disbursement made',200);
        }

    }
}
