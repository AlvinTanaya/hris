<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationPerformanceDetail extends Model
{
    use HasFactory;
    
    protected $table = 'employee_evaluation_performance_detail';
    
    protected $fillable = [
        'evaluation_id',
        'weight',
        'weight_performance_id',
        'value',
    ];
    
    public function evaluation()
    {
        return $this->belongsTo(EvaluationPerformance::class, 'evaluation_id');
    }
    
    public function weightPerformance()
    {
        return $this->belongsTo(RuleEvaluationWeightPerformance::class, 'weight_performance_id');
    }
}