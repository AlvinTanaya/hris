<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\RequestResign;
class ResignationUpdatedHRMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $resignation;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, RequestResign $resignation)
    {
        $this->user = $user;
        $this->resignation = $resignation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Updated Resignation Request - ' . $this->user->name)
                    ->view('emails.resignation_updated_hr');
    }
}