<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class EmployeeShift extends Model
{
    use HasFactory;
    protected $table = 'employee_shift';

    protected $fillable = ['id','user_id', 'rule_id', 'start_date', 'end_date', 'created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ruleShift()
    {
        return $this->belongsTo(rule_shift::class, 'rule_id');
    }
}

