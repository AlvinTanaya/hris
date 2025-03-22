<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarningLetter extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'employee_warning_letter';

    // Define which fields can be mass-assigned
    protected $fillable = [
        'id',
        'user_id',
        'maker_id',
        'type',
        'reason_warning',
        'created_at',
        'updated_at',
    ];
}
