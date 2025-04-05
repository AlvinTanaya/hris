<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationPerformanceMessage extends Model
{
    use HasFactory;
    
    protected $table = 'employee_evaluation_performance_message';
    
    protected $fillable = [
        'evaluation_id',
        'message'
    ];
    
    public function evaluation()
    {
        return $this->belongsTo(EvaluationPerformance::class, 'evaluation_id');
    }
}