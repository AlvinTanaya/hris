<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeShiftAssigned extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $shift;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $shift)
    {
        $this->user = $user;
        $this->shift = $shift;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $start_date = $this->shift->start_date;
        $end_date = $this->shift->end_date ? $this->shift->end_date : 'indefinitely';
        $type = $this->shift->rule->type;
        $schedule = json_decode($this->shift->rule->days, true);
        $start_times = json_decode($this->shift->rule->hour_start, true);
        $end_times = json_decode($this->shift->rule->hour_end, true);

        $scheduleDetails = '';
        foreach ($schedule as $index => $day) {
            $scheduleDetails .= "$day: " . $start_times[$index] . " - " . $end_times[$index] . "\n";
        }

        return $this->subject('You have been assigned to a shift')
            ->view('emails.employee_shift_assigned')
            ->with([
                'name' => $this->user->name,
                'type' => $type,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'scheduleDetails' => $scheduleDetails,
            ]);
    }
}
