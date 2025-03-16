<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\RequestTimeOff;
use App\Models\User;

class TimeOffRequestSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $timeOffRequest;
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(RequestTimeOff $timeOffRequest, User $user)
    {
        $this->timeOffRequest = $timeOffRequest;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Time Off Request Submitted')
                    ->view('emails.time_off_request_submitted')
                    ->with([
                        'timeOffRequest' => $this->timeOffRequest,
                        'user' => $this->user,
                    ]);
    }
}
