<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    public function index(){
        return \response()->json(Employee::all(),200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'place_id' => 'required', 
            'user_id' => 'required', 
            'role' => 'required'
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }

        Employee::create([
            'place_id' => $request->place_id, 
            'user_id' => $request->user_id, 
            'role' => $request->role
        ]);

        return response()->json('Employee added',201);
    }

    public function getEmployeesByPlace(Request $request, $placeID){
        $employees = Employee::where('place_id', $placeID)->get();
        return response()->json($employees, 200);
    }

    public function update(Request $request, $id){
        Employee::where('id', $id)->update([
            'place_id' => $request->place_id, 
            'user_id' => $request->user_id, 
            'role' => $request->role
        ]);

        return response()->json('Employee updated',200);
    }

    public function delete(Request $request,$id){
        Employee::where('user_id', $id)->delete();
        return response()->json('Employee deleted', 200);
    }
}
