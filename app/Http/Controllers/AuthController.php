<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Place;
use App\Models\Employee;
use App\Models\PasswordReset;
use Carbon\Carbon; 
use App\Models\UserToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Http\Controllers\MailController;
use App\Http\Controllers\SMSController;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function phoneLogin(Request $request){
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }

        $user = User::where([
            'phone' => $request->phone,
        ])->first();

        if (!$user) {
            $user = User::create([
                'name' => null,
                'email' => null,
                'country_id' => null,
                'phone' => $request->phone,
                'region_id' => null,
                'district_id' => null
            ]);

            $otp = rand( 1000 , 9999 );

            UserToken::create([
                'user_id' => $user->id,
                'otp' => $otp,
                'activated' => false
            ]);

            $smsController = new SMSController();
            $smsController->sendMessage(null, "Your TAYARI OTP is $otp", $user->phone);

            return response()->json([
                'exists' => false,
            ], 201);
        }

        $otp = rand( 1000 , 9999 );

        UserToken::create([
            'user_id' => $user->id,
            'otp' => $otp,
            'activated' => false
        ]);

        $smsController = new SMSController();
        $smsController->sendMessage(null, "Your TAYARI OTP is $otp", $user->phone);

        if($user->name == null || $user->email == null){
            return response()->json([
                'exists' => false
            ], 200);
        }

        return response()->json([
            'exists' => true
        ], 200);
    } 
    
    public function existingUserLogin(Request $request){
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }

        $user = User::where('phone', $request->phone)->first();

        $token = UserToken::where([
            'user_id' => $user->id,
            'otp' => (int)$request->otp,
            'activated' => false
            ])->first();

        if(!$token){
            return response()->json('Invalid token', 400);
        }

        $user->update([
            'fcm' => $request->fcm
        ]);

        $token->update([
            'activated' => true
        ]);

        return \response()->json([
            'user' => $user,
            'token' => $user->createToken(time())->plainTextToken
        ],200);
    }

    //current in use
    public function userPhoneLoginUpdate(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'country_id' => 'required',
            'otp' => 'required',
            'fcm' => 'required'
        ]);

        return \response()->json($request->fcm,200);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }

        $user = User::where('phone', $request->phone)->first();

        $token = UserToken::where([
            'user_id' => $user->id,
            'otp' => (int)$request->otp,
            'activated' => false
            ])->first();

        if(!$token){
            return response()->json('Invalid token', 400);
        }

        $user->update([
            'fcm' => $request->fcm
        ]);

        $token->update([
            'activated' => true
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'country_id' => $request->country_id,
        ]);

        return \response()->json([
            'user' => $user,
            'token' => $user->createToken(time())->plainTextToken
        ],200);
    }
    
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

    public function waiterLogin(Request $request){

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

        $employee = Employee::where('user_id', $user->id)->with('place')->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return \response()->json('Wrong credentials', 400);
        }

        return response()->json([
            'user' => $user,
            'place_id' => $employee->place_id,
            'place' => $employee->place,
            'status' => $employee->status,
            'token' => $user->createToken(time())->plainTextToken
        ], 200);             
    }

    public function waiterRegistration(Request $request){
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

        $checkIfUserExists = User::where([
            'email' => $request->email,
        ])->exists();

        if($checkIfUserExists){
            return \response()->json('User already exists',400);
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

        $employee = Employee::create([
            'place_id' => $request->place_id, 
            'user_id' => $user->id, 
            'role' => 1
        ]);

        return response()->json([
            'user' => $user,
            'token' => $user->createToken(time())->plainTextToken,
            'place_id' => $employee->place_id,
            'message' => "Waiter created"
        ],201);
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

        if($user->role != 4){
            return \response()->json('You are not a restaurant owner',400);
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

        $mailController = new MailController();
        $mailController->ownerRegisterMail(null, $user);

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

    public function resetPassword(Request $request){
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'password' => 'required'
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }


        $updatePassword  = DB::table('password_resets')->where([
            'token' => $request->token
            ])->whereNull('created_at')
            ->first();

        if(!$updatePassword ){
            return response()->json('Invalid token',400);
        }

        $time = Carbon::now();

        DB::table('password_resets')->where([
            'token' => $request->token
            ])->whereNull('created_at')
            ->update([
            'created_at' => $time
        ]);

        $details = PasswordReset::where([
            'token' => $request->token,
            'created_at' => $time
            ])->first();

        User::where('email', $details->email)->update([
            'password' => Hash::make($request->password)
        ]);

        return \response()->json('Password reset', 200);
    }

    public function passwordResetRequest(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }

        $checkIfUserExists = User::where('email', $request->email)->exists();

        if(!$checkIfUserExists){
            return response()->json('No user with this email address exists',200);
        }

        $token = Str::random(64);

        DB::table('password_resets')->insert([
            'email' => $request->email, 
            'token' => $token, 
          ]);

        $mailController = new MailController();
        $mailController->passwordResetMail(null, $request->email, $token);

        return response()->json('Mail sent',200);

    }

    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return response()->json('User logged out', 200);
    }
}
