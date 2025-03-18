<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResignationApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $employee;
    public $request;
    public $approver;

    public function __construct($employee, $request, $approver)
    {
        $this->employee = $employee;
        $this->request = $request;
        $this->approver = $approver;
    }

    public function build()
    {
        return $this->subject('Resignation Request Approved')
            ->view('emails.resignation_approved')
            ->with([
                'employee' => $this->employee,
                'request' => $this->request,
                'approver' => $this->approver
            ]);
    }
}
