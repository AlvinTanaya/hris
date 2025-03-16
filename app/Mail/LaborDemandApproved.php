<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\recruitment_demand;

class LaborDemandApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $demand;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(recruitment_demand $demand)
    {
        $this->demand = $demand;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Labor Demand Request Approved: ' . $this->demand->labor_demand_id)
                   ->markdown('emails.labor-demand-approved')
                   ->with([
                       'demand' => $this->demand,
                       'url' => route('recruitment.labor.demand.show', $this->demand->id)
                   ]);
    }
}