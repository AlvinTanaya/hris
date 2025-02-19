<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class elearning_invitation extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'elearning_invitation';

    // Define which fields can be mass-assigned
    protected $fillable = [
        'id',
        'lesson_id',
        'schedule_id',
        'users_id',
        'grade',
        'created_at',
        'updated_at',
    ];
}
