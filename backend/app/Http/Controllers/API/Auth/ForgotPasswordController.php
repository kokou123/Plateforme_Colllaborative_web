<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use App\Services\MailService;
use App\Services\OtpService;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{

    public function envoyerOtp(ForgotPasswordRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Aucun compte associé à cet email.'
            ], 404);
        }

        $otp = OtpService::generer();

        $user->update([
            'otp' => $otp,
            'otp_expire_at' => now()->addMinutes(10)
        ]);

        MailService::envoyerResetPassword($user->email, $otp);

        return response()->json(['message' => 'OTP envoyé.']);
    }

    public function reset(ResetPasswordRequest $request)
    {

        $user = User::where(

            'email',

            $request->email

        )->first();

        if(

            $user->otp!=$request->otp ||

            now()->gt($user->otp_expire_at)

        ){

            return response()->json([

                'message'=>'OTP invalide.'

            ],422);

        }

        $user->update([

            'password'=>Hash::make(

                $request->password

            ),

            'otp'=>null,

            'otp_expire_at'=>null

        ]);

        return response()->json([

            'message'=>'Mot de passe modifié.'

        ]);

    }

}