<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContractExpiringNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The user instance.
     *
     * @var \App\Models\User
     */
    public $user;

    /**
     * The expiry period.
     *
     * @var string
     */
    public $expiryPeriod;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $expiryPeriod)
    {
        $this->user = $user;
        $this->expiryPeriod = $expiryPeriod;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "CONTRACT EXPIRING: {$this->user->name}'s contract expires in {$this->expiryPeriod}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.contract-expiring',
            with: [
                'employeeId' => $this->user->employee_id,
                'name' => $this->user->name,
                'contractEndDate' => $this->user->contract_end_date,
                'expiryPeriod' => $this->expiryPeriod,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}