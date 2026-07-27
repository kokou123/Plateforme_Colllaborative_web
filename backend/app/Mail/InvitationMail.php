<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class InvitationMail extends Mailable
{
    public function __construct(

        public string $nomEntreprise,

        public string $url

    ){}

    public function build()
    {
        return $this
            ->subject("Invitation")
            ->view("emails.invitation");
    }
}