<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeEvaluationPerformance extends Model
{
    use HasFactory;

    protected $table = 'employee_evaluation_performance';

    protected $fillable = [
        'id',
        'user_id',
        'rule_evaluation_performance_id',
        'value',
        'evaluator_id',
        'created_at',
        'updated_at'
    ];

    // Relasi ke User (Employee)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke RuleEvaluationPerformance
    public function rule()
    {
        return $this->belongsTo(RuleEvaluationPerformance::class, 'rule_evaluation_performance_id');
    }
}