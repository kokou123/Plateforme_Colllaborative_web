<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class ResetPasswordMail extends Mailable
{
    public function __construct(
        public string $otp
    ){}

    public function build()
    {
        return $this
            ->subject("Réinitialisation du mot de passe")
            ->view("emails.reset-password");
    }
}