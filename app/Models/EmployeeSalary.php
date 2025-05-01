<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSalary extends Model
{
    use HasFactory;

    protected $table = 'employee_salaries';
    
    protected $fillable = [
        'users_id',
        'basic_salary',
        'overtime_rate_per_hour',
        'allowance',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}