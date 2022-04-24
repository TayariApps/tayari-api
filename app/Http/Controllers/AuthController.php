<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
      public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'name' => 'required',
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }

        $user = User::where([
            'email' => $request->email,
            'name' => $request->name,
            ])->first();

        if (!$user) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            return response()->json([
                'status' => 'New',
                'user' => $user,
                'token' => $user->createToken(time())->plainTextToken
            ], 200);
        }
     
        return response()->json([
            'status' => 'Exists',
            'user' => $user,
            'token' => $user->createToken(time())->plainTextToken
        ], 200);
    }

    public function updateUser(Request $request){
        User::where('id', $request->id)->update([
            'country_id' => $request->country_id,
            'phone' => $request->phone,
            'email' => $request->email,
            'name' => $request->name
        ]);

        return \response()->json('User updated', 200);
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
