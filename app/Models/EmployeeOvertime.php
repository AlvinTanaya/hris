<?php
// EmployeeOvertime Model Update
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeOvertime extends Model
{
    use HasFactory;

    protected $table = 'employee_overtime';

    protected $fillable = [
        'id',
        'user_id',
        'date',
        'start_time',
        'end_time',
        'total_hours',
        'reason',
        'overtime_type',
        'approval_status',
        'declined_reason',
        'created_at',
        'updated_at',
        'dept_approval_status',
        'dept_approval_user_id',
        'admin_approval_status',
        'admin_approval_user_id',
  
    ];
}
