<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResignationRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $resign_date;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $resign_date)
    {
        $this->user = $user;
        $this->resign_date = $resign_date;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Resignation Request Submitted')
                    ->view('emails.resignation_request')
                    ->with([
                        'userName' => $this->user->name,
                        'resignDate' => $this->resign_date,
                    ]);
    }
}
