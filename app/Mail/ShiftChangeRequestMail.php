<?php

namespace App\Mail; // ✅ Tambahkan namespace

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable; // ✅ Pastikan ini digunakan
use Illuminate\Queue\SerializesModels;

class ShiftChangeRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $shiftChange;

    public function __construct($shiftChange)
    {
        $this->shiftChange = $shiftChange;
    }

    public function build()
    {
        return $this->subject('New Shift Change Request')
            ->view('emails.shift_change_request')
            ->with([
                'name' => $this->shiftChange->user->name ?? 'System', // ✅ Hindari error jika null
                'start_date' => $this->shiftChange->date_change_start ?? 'N/A',
                'end_date' => $this->shiftChange->date_change_end ?? 'N/A',
                'reason' => $this->shiftChange->reason_change ?? 'No reason provided'
            ]);
    }
}
