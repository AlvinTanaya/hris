<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeShiftExchanged extends Mailable
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
        // dd($this->shift);
        // Get the rule data with proper relationship
        $rule = \App\Models\rule_shift::find($this->shift->rule_id);

        // Format schedule details as a structured array
        $scheduleDetails = [];
        if ($rule) {
            // Parse days from the rule_shift table
            $days = json_decode($rule->days);
            $hourStart = json_decode($rule->hour_start);
            $hourEnd = json_decode($rule->hour_end);

            // Create schedule array
            foreach ($days as $index => $day) {
                $scheduleDetails[] = [
                    'day' => $day,
                    'hour_in' => isset($hourStart[$index]) ? $hourStart[$index] : 'No time',
                    'hour_out' => isset($hourEnd[$index]) ? $hourEnd[$index] : 'No time'
                ];
            }
        }

        // dd($scheduleDetails, $rule->type);

        return $this->subject('New Shift Assignment')
            ->view('emails.employee_shift_exchanged')
            ->with([

                'name' => $this->user->employee_name ?? $this->user->name,
                'type' => $this->shift->type ?? ($rule->type ?? 'Unknown'),
                'start_date' => $this->shift->start_date,
                'end_date' => $this->shift->end_date ?? 'Until Further Notice',
                'scheduleDetails' => $scheduleDetails,
            ]);
    }
}
