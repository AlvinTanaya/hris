<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryHistory extends Model
{
    use HasFactory;

    protected $table = 'salary_history';
    
    protected $fillable = [
        'users_id',
        'old_basic_salary',
        'old_overtime_rate_per_hour',
        'old_allowance',
        'new_basic_salary',
        'new_overtime_rate_per_hour',
        'new_allowance',
        'created_at',
        'updated_at'
        
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}