<?php

namespace App\Mail;

use App\Models\EmployeeOvertime;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OvertimeDeclinedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $overtime;

    public $employee;


    public function __construct(EmployeeOvertime $overtime,     $employee)
    {
        $this->overtime = $overtime;
        $this->employee = $employee;
    }

    public function build()
    {
        return $this->subject('Overtime Request Declined')
            ->view('emails.overtime_declined')
            ->with([
                'overtime' => $this->overtime,
                'employee' => $this->employee
            ]);
    }
}
