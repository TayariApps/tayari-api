<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\Employee;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function getCustomers(){
        $customers = User::where('role',3)->get();
        return \response()->json($customers,200);
    }

    public function getPlaceCustomers($id){
        $check = User::whereRelation('orders', 'place_id', $id)
                    ->whereRelation('orders', 'payment_status', true)->exists();

        if($check){
            $users = User::whereRelation('orders', 'place_id', $id)->whereRelation('orders', 'payment_status', true)->get();
        } else{
            $users = [];
        }

        return \response()->json($users,200);
    }
    
    public function updateWaiterStatus(Request $request){
        $validator = Validator::make($request->all(), [
            'place_id' => 'required',
            'user_id' => 'required',
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }

        $employee = Employee::where([
            'user_id' => $request->user_id,
            'place_id' => (int)$request->place_id
        ])->first();

        $employee->update([
            'status' => !$employee->status
        ]);

        return \response()->json('Status updated',200);
    }
}
