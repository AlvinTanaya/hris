<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuleDisciplineGrade extends Model
{
    use HasFactory;

    protected $table = 'rule_discipline_grades';

    protected $fillable = [
        'grade',
        'min_score',
        'max_score',
        'description'
    ];
}
