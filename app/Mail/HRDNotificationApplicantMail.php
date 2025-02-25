<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HRDNotificationApplicantMail extends Mailable
{
    use Queueable, SerializesModels;

    public $query;

    public function __construct($query)
    {
        $this->$query = $query;
    }

    public function build()
    {
        return $this->subject('New Job Applicant Notification')
                    ->view('emails.hrd_notification_applicant');
    }
}
