<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicantStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $applicant;
    public $messageContent;

    public function __construct($applicant, $messageContent)
    {
        $this->applicant = $applicant;
        $this->messageContent = $messageContent;
    }

    public function build()
    {
        return $this->subject('Interview Status Update')
                    ->view('emails.applicant_status')
                    ->with([
                        'name' => $this->applicant->name,
                        'messageContent' => $this->messageContent,
                    ]);
    }
}
