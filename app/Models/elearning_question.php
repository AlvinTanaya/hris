<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class elearning_question extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'elearning_question';

    // Define which fields can be mass-assigned
    protected $fillable = [
        'id',
        'lesson_id',
        'question',
        'multiple_choice',
        'answer_key',
        'grade',
        'created_at',
        'updated_at'
    ];

}
