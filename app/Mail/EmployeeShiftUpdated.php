<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeShiftUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $shift;

    public function __construct($user, $shift)
    {
        $this->user = $user;
        $this->shift = $shift;
    }

    public function build()
    {
        return $this->subject('Shift Assignment Update')
            ->view('emails.employee_shift_updated')
            ->with([
                'name' => $this->user->name,
                'type' => $this->shift->rule->type,
                'start_date' => $this->shift->start_date,
                'end_date' => $this->shift->end_date ?? 'Until Further Notice',
                'scheduleDetails' => $this->shift->rule->schedule_details,
            ]);
    }
}
