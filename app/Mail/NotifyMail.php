<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifyMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    public function build()
    {
        $hashUrl = 'http://chatapp.test/mail-verification/' . $this->details['hashUrl'];

        return $this->subject('Email verification for ChatApp')
            ->view('emails.verifyMail', [
                'email' => $this->details['email'],
                'firstName' => $this->details['firstName'],
                'lastName' => $this->details['lastName'],
                'hashUrl' => $hashUrl,
            ]);
    }
}

