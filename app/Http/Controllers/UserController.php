<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;

class UserController extends Controller
{
    public function getCustomers(){
        $customers = User::where('role',3)->get();
        return \response()->json($customers,200);
    }

    public function getPlaceCustomers($id){
        $check = User::whereRelation('orders', 'place_id', $id)->exists();

        if($check){
            $users = User::whereRelation('orders', 'place_id', $id)->get();
        } else{
            $users = [];
        }

        return \response()->json($users,200);
    }
}
