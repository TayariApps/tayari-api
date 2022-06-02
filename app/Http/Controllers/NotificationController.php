<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class NotificationController extends Controller
{
    public function sendNotification(Request $request){

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'body' => 'required',
            'userID' => 'required'
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }

        $user = User::where('id', $request->userID)->first();

        if($user->fcm == null){
            return \response()->json('User does not have token',400);
        }

        $response = Http::post('https://fcm.googleapis.com/fcm/send', [
            'to' => $user->fcm,
            'data' => [
                'title' => $request->title,
                'body' => $request->body
            ],
            'notification' => [
                'title' => $request->title,
                'body' => $request->body
            ]
        ]);

        dd($response->body());
        
        if($response->ok()){

            Notification::create([
                'title' => $request->title, 
                'body' => $request->body, 
                'user_id' => $user->id,
                'fcm' => $user->fcm
            ]);

            return \response()->json('Notification sent',200);
        }

        return \response()->json('Notification failed',400);
    }


    public function userNotifications(Request $request){
        $notifications = Notification::where('user_id', $request->user()->id)->get();
        return \response()->json($notifications,200);
    }


}
