<?php

namespace App\Mail;

use App\Models\EmployeeOvertime;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OvertimeApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $overtime;

    public function __construct(EmployeeOvertime $overtime)
    {
        $this->overtime = $overtime;
    }

    public function build()
    {
        return $this->subject('Overtime Request Approved')
                    ->view('emails.overtime_approved')
                    ->with([
                        'overtime' => $this->overtime
                    ]);
    }
}