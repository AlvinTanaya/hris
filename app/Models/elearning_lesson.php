<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class elearning_lesson extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'elearning_lesson';

    // Define which fields can be mass-assigned
    protected $fillable = [
        'id',
        'name',
        'duration',
        'passing_grade',
        'lesson_file',
        'created_at',
        'updated_at'
    ];
}
