<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationPerformance extends Model
{
    use HasFactory;
    
    protected $table = 'employee_evaluation_performance';
    
    protected $fillable = [
        'user_id',
        'evaluator_id',
        'date',
        'total_score',
        'total_reduction'
    ];
    
    public function details()
    {
        return $this->hasMany(EvaluationPerformanceDetail::class, 'evaluation_id');
    }
    
    public function messages()
    {
        return $this->hasMany(EvaluationPerformanceMessage::class, 'evaluation_id');
    }
    
    public function reductions()
    {
        return $this->hasMany(EvaluationPerformanceReduction::class, 'evaluation_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }
}