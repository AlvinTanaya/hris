<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PositionExchangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $applicant;
    public $position;
    public $department;

    public function __construct($applicant, $position, $department)
    {
        $this->applicant = $applicant;
        $this->position = $position;
        $this->department = $department;
    }

    public function build()
    {
        return $this->subject('Position Exchange Notification')
                    ->view('emails.position_exchanged')
                    ->with([
                        'name' => $this->applicant->name,
                        'position' => $this->position,
                        'department' => $this->department,
                    ]);
    }
}
