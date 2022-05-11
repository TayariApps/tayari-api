<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DisbursementController extends Controller
{
    public function index(){
        $places = Place::has('disbursements')->withSum('disbursements','amount');
        return response()->json($places,200);
    }
}
