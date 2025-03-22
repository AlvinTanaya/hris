<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class users_training extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'users_training';

    // Define which fields can be mass-assigned
    protected $fillable = [
        'id',
        'users_id',
        'training_name',
        'training_city',
        'training_province',
        'start_date',
        'end_date',
        'created_at',
        'updated_at',
    ];
}
