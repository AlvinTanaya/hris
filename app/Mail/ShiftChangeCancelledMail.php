<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShiftChangeCancelledMail extends Mailable
{
    use Queueable, SerializesModels;

    public $requestDetails;
    public function __construct($requestDetails)
    {
        $this->requestDetails = $requestDetails;
    }


    public function build()
    {
        return $this->subject('Shift Change Request Cancelled')
            ->view('emails.shift_change_cancelled')
            ->with('requestDetails', $this->requestDetails);
    }
}
