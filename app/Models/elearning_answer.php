<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class elearning_answer extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'elearning_answer';

    // Define which fields can be mass-assigned
    protected $fillable = [
        'id',
        'invitation_id',
        'lesson_id',
        'schedule_id',
        'users_id',
        'question',
        'multiple_choice',
        'answer_key',
        'answer',
        'grade',
        'mark',
        'created_at',
        'updated_at'
    ];
}



