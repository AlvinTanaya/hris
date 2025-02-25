<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewEmployeeNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $employee;

    public function __construct($employee)
    {
        $this->employee = $employee;
    }

    public function build()
    {
        return $this->subject('New Employee Joined Our Team!')
            ->view('emails.new_employee_notification')
            ->with([
                'name' => $this->employee->name,
                'position' => $this->employee->position,
                'department' => $this->employee->department,
            ]);
    }
}
