<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class SMSController extends Controller
{
    public function sendMessage(Request $request = null, $message, $phone){
        $response = Http::post('https://secure-gw.fasthub.co.tz/fasthub/messaging/json/api', [
            "channel" => [
                'channel' => 121114,
                'password' => 'ZjdiZTliZTAzZTk4Y2VkZGUxOTU3NmYxOWJmYWM1NWQ3OWRmM2I4OGFiNjFmODJkZTZjYjc0NTg0OTUyYTgyNQ==',
            ], 
            "messages" => [
                [
                    "text" => "$message",
                    "msisdn" => "$phone",
                    "source" => "TAYARI"
                ]
            ]
        ]);

        return $response;
    }

    public function sendTextToAllClients(Request $request){
        $message = $request->message;

        $users = User::where('role',3)->get();

        foreach ($users as $user) {
            $response = Http::post('https://secure-gw.fasthub.co.tz/fasthub/messaging/json/api', [
                "channel" => [
                    'channel' => 121114,
                    'password' => 'ZjdiZTliZTAzZTk4Y2VkZGUxOTU3NmYxOWJmYWM1NWQ3OWRmM2I4OGFiNjFmODJkZTZjYjc0NTg0OTUyYTgyNQ==',
                ], 
                "messages" => [
                    [
                        "text" => "$message",
                        "msisdn" => $user->phone,
                        "source" => "TAYARI"
                    ]
                ]
            ]);
        }

        return \response()->json('Message sent',200);

    }

    public function testMessage(Request $request){

        $message = $request->message;
        $phone = $request->phone;

        $response = Http::post('https://secure-gw.fasthub.co.tz/fasthub/messaging/json/api', [
            "channel" => [
                'channel' => 121114,
                'password' => 'ZjdiZTliZTAzZTk4Y2VkZGUxOTU3NmYxOWJmYWM1NWQ3OWRmM2I4OGFiNjFmODJkZTZjYjc0NTg0OTUyYTgyNQ==',
            ], 
            "messages" => [
                [
                    "text" => "$message",
                    "msisdn" => "$phone",
                    "source" => "TAYARI"
                ]
            ]
        ]);

        dd($response->body());
    }
}
