<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function getCustomers(){
        $customers = User::where('role',3)->get();
        return \response()->json($customers,200);
    }
}
