<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthCountroller extends Controller
{
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'name' => 'required',
            'phone' => 'required',
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }

        $user = User::where([
            'email' => $request->email,
            'name' => $request->name,
            'phone' => $request->phone,
            ])->first();

        if (!$user) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'country_id' => (int)$request->country_id
            ]);

            return $user->createToken(time())->plainTextToken;
        }
     
        return $user->createToken(time())->plainTextToken;
    }

    public function clientRegister(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'country_id' => 'required',
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'country_id' => (int)$request->country_id
        ]);

        return response()->json($user->createToken(time())->plainTextToken(), 201);
    }

    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return response()->json('User logged out', 200);
    }
}
