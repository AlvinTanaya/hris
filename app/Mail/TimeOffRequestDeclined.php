<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\RequestTimeOff;
use App\Models\User;

class TimeOffRequestDeclined extends Mailable
{
    use Queueable, SerializesModels;

    public $timeOffRequest;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(RequestTimeOff $timeOffRequest)
    {
        $this->timeOffRequest = $timeOffRequest;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Time Off Request Declined')
                    ->view('emails.time_off_request_declined')
                    ->with([
                        'timeOffRequest' => $this->timeOffRequest,
                    ]);
    }
}