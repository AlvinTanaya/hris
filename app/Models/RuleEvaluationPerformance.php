<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuleEvaluationPerformance extends Model
{
    use HasFactory;

    protected $table = 'rule_evaluation_performance';

    protected $fillable = [
        'id',
        'type',
        'weight',
        'status',
        'created_at',
        'updated_at'
    ];
}
