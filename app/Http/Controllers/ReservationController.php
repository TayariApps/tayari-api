<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;

class ReservationController extends Controller
{
    public function index(){
        \response()->json(Reservation::with(['user', 'table'])->get(),200);
    }

    
}
