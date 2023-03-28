<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;
use App\Models\SmsCode;

class SMSController extends Controller
{
    public function sendAuthSms(Request $request) {
        $salt       = "23456789ABCDEFHJKLMNPRTVWXYZ";
        $len        = strlen($salt);
        $makecode   = '';
        mt_srand(10000000*(double)microtime());
        for ($i = 0; $i < 6; $i++) {
            $makecode .= $salt[mt_rand(0,$len - 1)];
        }

        SmsCode::create([
            'ip_address' => $request->ip(),
            'code' => $makecode
        ]);
        $receiver = $request->get('phone_number');

        $sid = env('TWILIO_ACOUNT_SID'); // Your Account SID from www.twilio.com/console
        $token = env('TWILIO_API_KEY'); // Your Auth Token from www.twilio.com/console

        $client = new Client($sid, $token);
        $message = $client->messages->create(
            $receiver, // Text this number
            [
                'from' => env('TWILIO_FROM_NAME'), // From a valid Twilio number
                'body' => 'Votre code d\'authentification pour Placetta : '. $makecode
            ]
        );


        return $message;

    }

    public function confirmSmsCode(Request $request) {
        $code = SmsCode::where('ip_address', $request->ip())->orderBy('id','desc')->first();
        if($code){
            $inputCode = $request->get('code');
                    if ($inputCode === $code->code) {
                        return response()->json([
                            'message' => 'Confirmed successfully!'], 200);
                    } else {
                        return response()->json([
                            'message' => 'Code doesn\'t match!'], 401);
                    }
                    
        }
        else {
            return response()->json([
                'message' => 'Ask for code first'], 401);
        }
    }
}
