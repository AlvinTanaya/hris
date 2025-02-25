<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicantSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $applicant;

    /**
     * Create a new message instance.
     */
    public function __construct($applicant)
    {
        $this->applicant = $applicant;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Application Submitted Successfully')
                    ->view('emails.applicant_submitted')
                    ->with([
                        'applicant' => $this->applicant,
                    ]);
    }
}
