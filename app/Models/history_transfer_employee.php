<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class history_transfer_employee extends Model
{
    use HasFactory;

    protected $table = 'users_transfer_history';

    protected $fillable = [
        'id',
        'users_id',
        'old_position_id',
        'old_department_id',
        'new_position_id',
        'new_department_id',
        'transfer_type',
        'reason',
        'created_at',
        'updated_at',
    ];

    // Relationships remain the same as before
    public function oldPosition()
    {
        return $this->belongsTo(EmployeePosition::class, 'old_position_id');
    }

    public function newPosition()
    {
        return $this->belongsTo(EmployeePosition::class, 'new_position_id');
    }

    public function oldDepartment()
    {
        return $this->belongsTo(EmployeeDepartment::class, 'old_department_id');
    }

    public function newDepartment()
    {
        return $this->belongsTo(EmployeeDepartment::class, 'new_department_id');
    }
}
