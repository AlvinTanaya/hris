<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\RequestResign;

class ResignationCancelledHRMail extends Mailable
{
    use Queueable, SerializesModels;

    public $resignDetails;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $resignDetails)
    {
        $this->resignDetails = $resignDetails;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Cancelled Resignation Request - ' . $this->resignDetails['user']->name)
                    ->view('emails.resignation_cancelled_hr');
    }
}