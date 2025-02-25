<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DepartmentUpdateNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $employee_id;
    public $position;
    public $department;

    public function __construct($employee_id, $position, $department)
    {
        $this->employee_id = $employee_id;
        $this->position = $position;
        $this->department = $department;
    }

    public function build()
    {
        return $this->subject('Employee Data Update Notification')
            ->view('emails.department_update_notification');
    }
}
