<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class ContractExtensionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $startDate;
    public $endDate;
    public $reason;
    public $isHR;

    public function __construct(User $user, $startDate, $endDate, $reason, $isHR = false)
    {
        $this->user = $user;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->reason = $reason;
        $this->isHR = $isHR;
    }

    public function build()
    {
        $subject = $this->isHR 
            ? "Contract Extension Notification: {$this->user->name} ({$this->user->employee_id})"
            : "Your Contract Extension Update";

        return $this->subject($subject)
                    ->view('emails.contract_extension');
    }
}