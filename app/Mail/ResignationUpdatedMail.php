<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResignationUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $resignRequest;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $resignRequest)
    {
        $this->user = $user;
        $this->resignRequest = $resignRequest;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Resignation Request has been Updated')
            ->view('emails.resignation_updated')
            ->with([
                'user' => $this->user,
                'resignRequest' => $this->resignRequest
            ]);
    }
}
