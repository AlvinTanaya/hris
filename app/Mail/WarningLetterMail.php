<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WarningLetterMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $count;
    public $reason;
    public $maker;

    public function __construct($user, $count, $reason, $maker)
    {
        $this->user = $user;
        $this->count = $count;
        $this->reason = $reason;
        $this->maker = $maker;
    }

    public function build()
    {
        return $this->subject('Warning Letter Notification')
            ->view('emails.warning_letter')
            ->with([
                'user' => $this->user,
                'count' => $this->count,
                'reason' => $this->reason,
                'maker' => $this->maker
            ]);
    }
}
