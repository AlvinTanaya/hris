<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestTimeOff extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'request_time_off';

    // Define which fields can be mass-assigned
    protected $fillable = [
        'id',
        'user_id',
        'time_off_id',
        'start_date',
        'end_date',
        'reason',
        'status',
        'reason_declined',
        'approved_by',
        'created_at',
        'updated_at',
    ];
}
