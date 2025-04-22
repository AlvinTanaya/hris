<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuleElearningGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'grade',
        'min_score',
        'max_score',
        'description'
    ];
}
