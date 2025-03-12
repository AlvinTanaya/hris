<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeOffAssign extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'time_off_assign';

    // Define which fields can be mass-assigned
    protected $fillable = [
        'id',
        'user_id',
        'time_off_id',
        'balance',
        'created_at',
        'updated_at',
    ];
}
