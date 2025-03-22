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
    public $type;
    public $typeCount;
    public $reason;
    public $maker;
    public $isUpdate;
    public $oldType;

    public function __construct($user, $type, $typeCount, $reason, $maker, $isUpdate = false, $oldType = null)
    {
        $this->user = $user;
        $this->type = $type;
        $this->typeCount = $typeCount;
        $this->reason = $reason;
        $this->maker = $maker;
        $this->isUpdate = $isUpdate;
        $this->oldType = $oldType;
    }

    public function build()
    {
        // Check if this is an SP3 warning (termination)
        $isTermination = $this->type === 'SP3';
        
        // Determine the subject based on whether this is an update or a new warning
        $subject = $this->isUpdate
            ? "Warning Letter Update: {$this->type} #{$this->typeCount}"
            : ($isTermination 
                ? 'IMPORTANT: Final Warning Letter - Employment Termination' 
                : "Warning Letter Notification: {$this->type} #{$this->typeCount}");
        
        return $this->subject($subject)
            ->view('emails.warning_letter')
            ->with([
                'user' => $this->user,
                'type' => $this->type,
                'typeCount' => $this->typeCount,
                'reason' => $this->reason,
                'maker' => $this->maker,
                'isTermination' => $isTermination,
                'isUpdate' => $this->isUpdate,
                'oldType' => $this->oldType
            ]);
    }
}