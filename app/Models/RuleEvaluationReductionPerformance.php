<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuleEvaluationReductionPerformance extends Model
{
    use HasFactory;

    protected $table = 'rule_evaluation_reduction_performance';

    protected $fillable = [
        'id',
        'type_id', // Changed from 'type' to 'type_id'
        'weight',
        'status',
        'created_at',
        'updated_at'
    ];

    // Add relationship to WarningLetterRule
    public function warningLetterRule()
    {
        return $this->belongsTo(WarningLetterRule::class, 'type_id');
    }
}
