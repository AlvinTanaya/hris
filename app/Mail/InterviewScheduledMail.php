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
        // Format the date and time separately
        $interviewDateTime = \Carbon\Carbon::parse($this->applicant->interview_date);
        $interviewDate = $interviewDateTime->format('Y-m-d');
        $interviewTime = $interviewDateTime->format('h:i A'); // This will give "06:28 PM"
        
        return $this->subject('Interview Schedule Notification')
                    ->view('emails.interview_scheduled')
                    ->with([
                        'name' => $this->applicant->name,
                        'interview_date' => $interviewDate,
                        'interview_time' => $interviewTime,
                        'interview_note' => $this->applicant->interview_note,
                    ]);
    }
}
