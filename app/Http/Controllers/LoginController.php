<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\LoginNeedVerification;

class LoginController extends Controller
{
    public function submit(Request $request)
    {
        // validate the phone number
        $request->validate([
            'phone'=>'required|numeric|min:10'
        ]);

        // find or create a user model
        $user = User::firstOrCreate([
            'phone' => $request->phone
        ]);

        if (!$user) {
            return response()->json(["message" => "could not process a user with that phone number"]);
        }
        
        // send te user a one time use code
        $user->notify(new LoginNeedVerification());

        // return back a response
        return response()->json(["message" => "text Notification sent"]);
    }

    public function verify(Request $request)
    {
        //validate the incoming request
        $request->validate([
            'phone'=>'required|numeric|min:10',
            'login_code'=>'required|numeric|between:111111,999999'
        ]);
        // find the user
        $user = User::where('phone',$request->phone)->where('login_code',$request->login_code)->first();

        // is code provide the same one saved?
        // if so, return back with token
        if ($user) {
            $user->update([
                'login_code'=>null
            ]);
            return $user->createToken($request->login_code)->plainTextToken;
        }

        //if not, return back with message
        return response()->json(["message" => "could not process a user with that phone number"]);

    }
}
