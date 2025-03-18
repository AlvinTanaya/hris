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
    public $employee;

    public function __construct(EmployeeOvertime $overtime,  $employee)
    {
        $this->overtime = $overtime;
        $this->employee = $employee;
    }

    public function build()
    {
        return $this->subject('Overtime Request Approved')
                    ->view('emails.overtime_approved')
                    ->with([
                        'overtime' => $this->overtime,
                        'employee' => $this->employee
                    ]);
    }
}