<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InterviewScheduledMail extends Mailable
{
    use Queueable, SerializesModels;

    public $applicant;

    public function __construct($applicant)
    {
        $this->applicant = $applicant;
    }

    public function build()
    {
        return $this->subject('Interview Schedule Notification')
                    ->view('emails.interview_scheduled')
                    ->with([
                        'name' => $this->applicant->name,
                        'interview_date' => $this->applicant->interview_date,
                        'interview_note' => $this->applicant->interview_note,
                    ]);
    }
}
