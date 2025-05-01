<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeAbsent extends Model
{
    use HasFactory;
    protected $table = 'employee_absent';

    protected $fillable = [
        'id',
        'user_id',
        'absent_place',
        'date',
        'hour_in',
        'hour_out',
        'status_in',
        'status_out',
        'rule_in',
        'rule_out',
        'rule_type',
        'late_minutes',
        'early_minutes',
        'created_at',
        'updated_at',
    ];
}
