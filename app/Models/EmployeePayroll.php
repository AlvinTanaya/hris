<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeePayroll extends Model
{
    use HasFactory;

    protected $table = 'employee_payroll';
    
    protected $fillable = [
        'users_id',
        'basic_salary',
        'overtime_hours',
        'overtime_salary',
        'file_path',
        'reduction_salary',
        'allowance',
        'created_at',
        'updated_at',
        'bonus'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}