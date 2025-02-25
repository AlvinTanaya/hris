<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AcceptedApplicantMail extends Mailable
{
    use Queueable, SerializesModels;

    public $applicant;
    public $position;
    public $department;
    public $joinDate;

    public function __construct($applicant, $position, $department, $joinDate)
    {
        $this->applicant = $applicant;
        $this->position = $position;
        $this->department = $department;
        $this->joinDate = $joinDate;
    }

    public function build()
    {
        return $this->subject('Congratulations! You are officially accepted')
            ->view('emails.accepted_applicant')
            ->with([
                'name' => $this->applicant->name,
                'position' => $this->position,
                'department' => $this->department,
                'joinDate' => $this->joinDate,
            ]);
    }
}
