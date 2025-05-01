<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeFinalEvaluation extends Model
{
    use HasFactory;

    protected $table = 'employee_final_evaluations';

    protected $fillable = [
        'user_id',
        'year',
        'performance',
        'performance_score',
        'discipline',
        'discipline_score',
        'elearning',
        'elearning_score',
        'final_score',
        'final_grade',
        'file_proposal',
        'proposal_grade',
        'salary_increases',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
