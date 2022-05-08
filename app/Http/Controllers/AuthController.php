<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

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
                'country_id' => null,
                'phone' => null,
                'region_id' => null,
                'district_id' => null
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
        $user = User::where('id', $request->user()->id)->first();
        
        $user->update([
            'country_id' => $request->country_id,
            'phone' => $request->phone,
            // 'email' => $request->email,
            // 'name' => $request->name,
            'gender' => $request->gender,
            'dob' => $request->dob
        ]);

        return \response()->json('User updated', 200);
    }

    public function waiterRegistration(Request $request){
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'password' => 'required',
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }

        $user = User::where([
            'phone' => $request->phone,
            ])->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'country_id' => null,
                    'phone' => null,
                    'role' => 2,
                    'region_id' => null,
                    'district_id' => null
                ]);
             }

        if (!$user) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'country_id' => null,
                'phone' => null,
                'region_id' => null,
                'district_id' => null
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

    public function updateProfileImage(Request $request){
        if($request->hasFile('image')){
            $img_ext = $request->file('image')->getClientOriginalExtension();
            $filename = time() . '.' . $img_ext;
            $imagePath = $request->file('image')->move(public_path('images/profile'), $filename);//image save public folder
        }

        $user = User::where('id', $request->user()->id)->first();

        $user->update([
            'user_image' => $filename
        ]);

        return \response()->json('Profile image updated',200);
    }

    public function ownerLogin(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
           return \response()->json('The credentials are incorrect',400);
        }
     
        return \response()->json([
            'token' => $user->createToken(time())->plainTextToken,
            'user' => $user
        ], 200);
    }

    
    public function ownerRegister(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'password' => 'required'
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }

        $user = User::create([
            'country_id' => $request->country_id,
            'phone' => $request->phone,
            'email' => $request->email,
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'role' => 4
        ]);

        return response()->json([
            'user' => $user,
            'token' => $user->createToken(time())->plainTextToken
        ],201);
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
