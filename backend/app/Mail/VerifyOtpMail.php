<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class VerifyOtpMail extends Mailable
{
    public function __construct(

        public string $otp

    ){}

    public function build()
    {
        return $this

            ->subject(

                "Vérification de votre compte"

            )

            ->view(

                'emails.verify-otp'

            );

    }
}