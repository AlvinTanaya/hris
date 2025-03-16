<?php

namespace App\Mail;

use App\Models\EmployeeOvertime;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OvertimeCancelledMail extends Mailable
{
    use Queueable, SerializesModels;

    public $overtime;
    public $employee;

    public function __construct(EmployeeOvertime $overtime, User $employee)
    {
        $this->overtime = $overtime;
        $this->employee = $employee;
    }

    public function build()
    {
        return $this->subject('Overtime Request Cancelled')
                    ->view('emails.overtime_cancelled')
                    ->with([
                        'overtime' => $this->overtime,
                        'employee' => $this->employee
                    ]);
    }
}