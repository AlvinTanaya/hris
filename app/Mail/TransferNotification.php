<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransferNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $oldPosition;
    public $oldDepartment;
    public $newPosition;
    public $newDepartment;
    public $transferType;

    public function __construct($oldPosition, $oldDepartment, $newPosition, $newDepartment, $transferType)
    {
        $this->oldPosition = $oldPosition;
        $this->oldDepartment = $oldDepartment;
        $this->newPosition = $newPosition;
        $this->newDepartment = $newDepartment;
        $this->transferType = $transferType;
    }

    public function build()
    {
        return $this->view('emails.transfer-notification')
                    ->subject('Pemberitahuan Pemindahan Posisi');
    }
}