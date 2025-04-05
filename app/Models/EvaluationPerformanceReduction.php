<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationPerformanceReduction extends Model
{
    use HasFactory;
    
    protected $table = 'employee_evaluation_performance_reductions';
    
    protected $fillable = [
        'evaluation_id',
        'warning_letter_id',
        'reduction_amount'
    ];
    
    public function evaluation()
    {
        return $this->belongsTo(EvaluationPerformance::class, 'evaluation_id');
    }
    
    public function warningLetter()
    {
        return $this->belongsTo(WarningLetter::class, 'warning_letter_id');
    }
}