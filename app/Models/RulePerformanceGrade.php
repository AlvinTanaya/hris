<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RulePerformanceGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'grade',
        'min_score',
        'max_score',
        'description'
    ];
}
