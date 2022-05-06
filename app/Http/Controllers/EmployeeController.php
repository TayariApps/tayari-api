<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index(){
        return \response()->json(Employee::all(),200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'place_id' => 'required', 
            'name' => 'required', 
            'phone' => 'required',
            'email' => 'required',
            'password' => 'required'
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 2,
            'country_id' => null,
            'region_id' => null,
            'district_id' => null
        ]);

        Employee::create([
            'place_id' => $request->place_id, 
            'user_id' => $user->id, 
            'role' => 1
        ]);

        return response()->json([
            'user' => $user,
            'token' => $user->createToken(time())->plainTextToken,
            'message' => "Waiter created"
        ],201);
    }

    public function getEmployeesByPlace(Request $request, $placeID){
        $employees = Employee::where('place_id', $placeID)->with('user')->get();
        return response()->json($employees, 200);
    }

    public function update(Request $request, $id){
        $user = User::where('id', $id)->first();
        
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        return response()->json('Employee updated',200);
    }

    public function delete(Request $request,$id){
        Employee::where('user_id', $id)->delete();
        return response()->json('Employee deleted', 200);
    }
}
