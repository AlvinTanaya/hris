<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class history_transfer_employee extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'users_transfer_history';

    // Define which fields can be mass-assigned
    protected $fillable = [
        'id',
        'users_id',
        'old_position',
        'old_department',
        'new_position',
        'new_department',
        'transfer_type',
        'reason',
        'created_at',
        'updated_at',
    ];
}
