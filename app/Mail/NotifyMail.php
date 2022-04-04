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
        return $this->subject('Email verification for ChatApp')
            ->view('emails.verifyMail', [
                'email' => $this->details['email'],
                'first_name' => $this->details['first_name'],
                'last_name' => $this->details['last_name'],
                'url' => $this->details['url'],
            ]);
    }
}

