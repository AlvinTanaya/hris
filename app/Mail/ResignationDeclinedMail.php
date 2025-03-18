<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResignationDeclinedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $employee;
    public $request;
    public $decliner;
    public $reason;

    public function __construct($employee, $request, $decliner, $reason)
    {
        $this->employee = $employee;
        $this->request = $request;
        $this->decliner = $decliner;
        $this->reason = $reason;
    }

    public function build()
    {
        return $this->subject('Resignation Request Declined')
            ->view('emails.resignation_declined')
            ->with([
                'employee' => $this->employee,
                'request' => $this->request,
                'decliner' => $this->decliner,
                'reason' => $this->reason
            ]);
    }
}
