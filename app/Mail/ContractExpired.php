<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContractExpired extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The user instance.
     *
     * @var \App\Models\User
     */
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('CONTRACT EXPIRED: Action Required - Contract Renewal for ' . $this->user->name)
            ->view('emails.contract-expired')
            ->with([
                'employeeId' => $this->user->employee_id,
                'name' => $this->user->name,
                'contractEndDate' => $this->user->contract_end_date,
            ]);
    }
}
