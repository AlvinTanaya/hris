<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeOffPolicy extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'time_off_policy';

    // Define which fields can be mass-assigned
    protected $fillable = [
        'id',
        'time_off_name',
        'time_off_description',
        'requires_time_input',
        'quota',
        'start_date',
        'end_date',
        'created_at',
        'updated_at',
    ];
}
