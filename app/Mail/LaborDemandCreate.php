<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\recruitment_demand;

class LaborDemandCreate extends Mailable
{
    use Queueable, SerializesModels;


    public $demand;

    public $positionName;
    public $departmentName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(recruitment_demand $demand)
    {
        $this->demand = $demand;
        $this->positionName = $demand->positionRelation->position ?? 'Unknown Position';
        $this->departmentName = $demand->departmentRelation->department ?? 'Unknown Department';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Labor Demand Request: ' . $this->demand->labor_demand_id)
                   ->markdown('emails.labor-demand-create')
                   ->with([
                    'demand' => $this->demand,
                    'positionName' => $this->positionName,
                    'departmentName' => $this->departmentName,
                    'url' => route('welcome')
                ]);
    }
}