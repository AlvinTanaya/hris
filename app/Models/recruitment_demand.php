<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class recruitment_demand extends Model
{
    use HasFactory;
    // Define the table name if it's not the plural of the model name
    protected $table = 'recruitment_demand';

    // Define which fields can be mass-assigned
    protected $fillable = [
        'id',
        'recruitment_demand_id',
        'maker_id',
        'status_demand',
        'department',
        'position',
        'opening_date',
        'closing_date',
        'status_job',
        'reason',
        'qty_needed',
        'qty_fullfil',
        'gender',
        'job_goal',
        'education',
        'major',
        'experience',
        'length_of_working',
        'time_work_experience',
        'response_reason',
        'skills',
        'created_at',
        'updated_at',
    ];
}
