<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ELearningInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $lessonName;
    public $startDate;
    public $endDate;
    public $invitationType;

    /**
     * Create a new message instance.
     */
    public function __construct($userName, $lessonName, $startDate, $endDate, $invitationType = 'new')
    {
        $this->userName = $userName;
        $this->lessonName = $lessonName;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->invitationType = $invitationType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->invitationType === 'new' 
            ? 'New E-Learning Course Invitation' 
            : 'E-Learning Course Invitation Update';

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.elearning-invitation',
            with: [
                'userName' => $this->userName,
                'lessonName' => $this->lessonName,
                'startDate' => $this->startDate,
                'endDate' => $this->endDate,
                'invitationType' => $this->invitationType,
            ]
        );
    }
}