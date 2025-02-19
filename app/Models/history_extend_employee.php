<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class history_extend_employee extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'users_extend_history';

    // Define which fields can be mass-assigned
    protected $fillable = [
        'id',
        'users_id',
        'position',
        'department',
        'reason',
        'start_date',
        'end_date',
        'created_at',
        'updated_at',
    ];
}
