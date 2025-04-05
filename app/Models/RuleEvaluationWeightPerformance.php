<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuleEvaluationWeightPerformance extends Model
{
    use HasFactory;

    protected $table = 'rule_evaluation_weight_performance';

    protected $fillable = [
        'id',
        'position_id',
        'criteria_id',
        'weight',
        'status',
        'created_at',
        'updated_at'
    ];

    public function position()
    {
        return $this->belongsTo(EmployeePosition::class, 'position_id');
    }

    /**
     * Get the criteria associated with the weight performance.
     */
    public function criteria()
    {
        return $this->belongsTo(RuleEvaluationCriteriaPerformance::class, 'criteria_id');
    }
}
