<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
class TransferNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $oldPosition;
    public $oldDepartment;
    public $newPosition;
    public $newDepartment;
    public $transferType;
    public $reason;
    public $isHR;

    public function __construct($user, $oldPosition, $oldDepartment, $newPosition, $newDepartment, $transferType, $reason, $isHR = false)
    {
        $this->user = $user;
        $this->oldPosition = $oldPosition;
        $this->oldDepartment = $oldDepartment;
        $this->newPosition = $newPosition;
        $this->newDepartment = $newDepartment;
        $this->transferType = $transferType;
        $this->reason = $reason;
        $this->isHR = $isHR;
    }

    public function build()
    {
        $subject = $this->isHR 
            ? "Employee Transfer Notification: {$this->user->name} ({$this->user->employee_id})" 
            : "Your Employment Status Update";

        return $this->subject($subject)
                    ->view('emails.transfer-notification');
    }
}