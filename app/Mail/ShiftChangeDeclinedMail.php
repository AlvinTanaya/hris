<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShiftChangeDeclinedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function build()
    {
        return $this->subject('Shift Change Declined')
            ->view('emails.shift_declined')
            ->with([
                'userName' => $this->request->user->name,
                'reason' => $this->request->declined_reason,
            ]);
    }
}
