<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuleEvaluationGradeSalaryFinal extends Model
{
    use HasFactory;
    
    protected $table = 'rule_evaluation_grade_salary_final';
    protected $fillable = ['grade', 'value_salary'];
}