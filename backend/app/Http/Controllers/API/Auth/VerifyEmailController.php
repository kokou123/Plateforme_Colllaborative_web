<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\VerifyOtpRequest;
use App\Models\User;

class VerifyEmailController extends Controller
{
    public function verify(VerifyOtpRequest $request)
    {

        $user = User::where(

            'email',

            $request->email

        )->first();

        if(!$user){

            return response()->json([

                'message'=>'Utilisateur introuvable.'

            ],404);

        }

        if($user->email_verifie){

            return response()->json([

                'message'=>'Email déjà vérifié.'

            ]);

        }

        if($user->otp!=$request->otp){

            return response()->json([

                'message'=>'OTP invalide.'

            ],422);

        }

        if(now()->gt($user->otp_expire_at)){

            return response()->json([

                'message'=>'OTP expiré.'

            ],422);

        }

        $user->update([

            'email_verifie'=>true,

            'otp'=>null,

            'otp_expire_at'=>null

        ]);

        $user->entreprise()->update([

            'active'=>true

        ]);

        return response()->json([

            'message'=>'Compte activé.'

        ]);

    }
    public function resend(Request $request)
    {

        $request->validate([

            'email'=>'required|email'

        ]);

        $user = User::where(

            'email',

            $request->email

        )->firstOrFail();

        $otp = OtpService::generer();

        $user->update([

            'otp'=>$otp,

            'otp_expire_at'=>now()->addMinutes(10)

        ]);

        MailService::envoyerOtp(

            $user->email,

            $otp

        );

        return response()->json([

            'message'=>'OTP renvoyé.'

        ]);

    }
}