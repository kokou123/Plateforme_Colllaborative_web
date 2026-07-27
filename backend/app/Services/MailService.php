<?php

namespace App\Services;

use App\Mail\VerifyOtpMail;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;
use App\Mail\InvitationMail;

class MailService
{
    public static function envoyerOtp(

        string $email,

        string $otp

    ): void
    {

        Mail::to($email)

            ->send(

                new VerifyOtpMail($otp)

            );

    }
    public static function envoyerResetPassword(string $email, string $otp): void
    {

        Mail::to($email)

            ->send(

                new ResetPasswordMail($otp)

            );

    }
    public static function envoyerInvitation(string $email, string $entreprise, string $url): void
    {

        Mail::to($email)

            ->send(

                new InvitationMail(

                    $entreprise,

                    $url

                )

            );

    }
}
