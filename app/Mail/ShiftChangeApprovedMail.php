<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShiftChangeApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function build()
    {
        $rule = $this->request->ruleShiftAfter;

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



        return $this->subject('Shift Change Approved')
            ->view('emails.shift_change_approved')
            ->with([
                'userName' => $this->request->user->name,
                'startDate' => $this->request->date_change_start,
                'endDate' => $this->request->date_change_end,
                'newShift' => $this->request->ruleShiftAfter->type,
                'scheduleDetails' => $scheduleDetails,
            ]);
    }
}
