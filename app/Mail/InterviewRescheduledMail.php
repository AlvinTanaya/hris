<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InterviewRescheduledMail extends Mailable
{
    use Queueable, SerializesModels;

    public $applicant;
    public $oldInterviewDate;

    public function __construct($applicant, $oldInterviewDate)
    {
        $this->applicant = $applicant;
        $this->oldInterviewDate = $oldInterviewDate;
    }

    public function build()
    {
        return $this->subject('Interview Reschedule Notification')
                    ->view('emails.interview_rescheduled')
                    ->with([
                        'name' => $this->applicant->name,
                        'old_interview_date' => $this->oldInterviewDate,
                        'new_interview_date' => $this->applicant->interview_date,
                        'interview_note' => $this->applicant->interview_note,
                    ]);
    }
}
