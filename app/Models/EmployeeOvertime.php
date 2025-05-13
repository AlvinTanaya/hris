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
        'approval_status',
        'declined_reason',
        'created_at',
        'updated_at',
        'dept_approval_status',
        'dept_approval_user_id',
        'admin_approval_status',
        'admin_approval_user_id',
    ];

    // Relationship to the User who created the overtime request
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship to the Department Approver user
    public function deptApprovalUser()
    {
        return $this->belongsTo(User::class, 'dept_approval_user_id');
    }

    // Relationship to the Admin Approver user
    public function adminApprovalUser()
    {
        return $this->belongsTo(User::class, 'admin_approval_user_id');
    }

    public function isDeptDeclined()
    {
        return $this->dept_approval_status === 'Declined';
    }

    public function isAdminDeclined()
    {
        return $this->admin_approval_status === 'Declined';
    }

    public function getDeclinedByUser()
    {
        if ($this->isAdminDeclined()) {
            return $this->adminApprovalUser;
        } elseif ($this->isDeptDeclined()) {
            return $this->deptApprovalUser;
        }

        return null;
    }
}
