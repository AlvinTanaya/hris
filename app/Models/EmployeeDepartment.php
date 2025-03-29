<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDepartment extends Model
{
    protected $table = 'employee_departments';
    protected $fillable = ['department'];

    // In EmployeeDepartment model
    public function demands()
    {
        return $this->hasMany(recruitment_demand::class, 'department_id');
    }
    // In EmployeeDepartment model
    public function users()
    {
        return $this->hasMany(User::class, 'department_id');
    }
}
