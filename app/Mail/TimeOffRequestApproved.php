<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\RequestTimeOff;
use App\Models\TimeOffPolicy;
use App\Models\User;

class TimeOffRequestApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $timeOffRequest;
    public $policy;


    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(RequestTimeOff $timeOffRequest,  User $user, TimeOffPolicy $policy)
    {
        $this->timeOffRequest = $timeOffRequest;
        $this->user = $user;
        $this->policy = $policy;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
      
        return $this->subject('Time Off Request Approved')
            ->view('emails.time_off_request_approved')
            ->with([
                'timeOffRequest' => $this->timeOffRequest,
                'policy' => $this->policy,
                'user' => $this->user,
            ]);
    }
}
