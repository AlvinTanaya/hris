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
        // Format old interview date and time
        $oldDateTime = \Carbon\Carbon::parse($this->oldInterviewDate);
        $oldDate = $oldDateTime->format('Y-m-d');
        $oldTime = $oldDateTime->format('h:i A');
        
        // Format new interview date and time
        $newDateTime = \Carbon\Carbon::parse($this->applicant->interview_date);
        $newDate = $newDateTime->format('Y-m-d');
        $newTime = $newDateTime->format('h:i A');

        return $this->subject('Interview Reschedule Notification')
                    ->view('emails.interview_rescheduled')
                    ->with([
                        'name' => $this->applicant->name,
                        'old_interview_date' => $oldDate,
                        'old_interview_time' => $oldTime,
                        'new_interview_date' => $newDate,
                        'new_interview_time' => $newTime,
                        'interview_note' => $this->applicant->interview_note,
                    ]);
    }
}