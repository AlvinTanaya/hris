<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class elearning_schedule extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'elearning_schedule';

    // Define which fields can be mass-assigned
    protected $fillable = [
        'id',
        'lesson_id',
        'start_date',
        'end_date',
        'created_at',
        'updated_at'
    ];
}
