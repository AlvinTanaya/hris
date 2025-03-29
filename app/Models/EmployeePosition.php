<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeePosition extends Model
{
    protected $table = 'employee_positions';
    protected $fillable = ['position', 'ranking'];

    // In EmployeePosition model
    public function demands()
    {
        return $this->hasMany(recruitment_demand::class, 'position_id');
    }


    public function users()
    {
        return $this->hasMany(User::class, 'position_id');
    }
}
