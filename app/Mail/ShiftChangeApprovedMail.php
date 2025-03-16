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
        return $this->subject('Shift Change Approved')
            ->view('emails.shift_approved')
            ->with([
                'userName' => $this->request->user->name,
                'startDate' => $this->request->date_change_start,
                'endDate' => $this->request->date_change_end,
                'newShift' => $this->request->ruleShiftAfter->name,
            ]);
    }
}
