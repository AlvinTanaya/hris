<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuleEvaluationCriteriaPerformance extends Model
{
    use HasFactory;

    protected $table = 'rule_evaluation_criteria_performance';

    protected $fillable = [
        'id',
        'type',
        'created_at',
        'updated_at'
    ];
}
