<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $messageContent;
    public $makerName;

    public $userName;

    /**
     * Create a new message instance.
     */
    public function __construct($messageContent, $makerName, $userName)
    {
        $this->messageContent = $messageContent;
        $this->makerName = $makerName;
        $this->userName = $userName;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('New Announcement')
                    ->view('emails.invitation')
                    ->with([
                        'messageContent' => $this->messageContent,
                        'makerName' => $this->makerName,
                        'userName' => $this->userName,
                    ]);
    }
}
