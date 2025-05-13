<?php
// RequestTimeOff Model Update
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestTimeOff extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'request_time_off';

    // Define which fields can be mass-assigned
    protected $fillable = [
        'id',
        'user_id',
        'time_off_id',
        'start_date',
        'end_date',
        'reason',
        'status',
        'declined_reason',
        'file_reason_path',
        'created_at',
        'updated_at',
        'dept_approval_status',
        'dept_approval_user_id',
        'admin_approval_status',
        'admin_approval_user_id',

    ];

    public function timeOffPolicy()
    {
        return $this->belongsTo(TimeOffPolicy::class, 'time_off_id');
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    // Add these relationships to your RequestShiftChange model
    public function deptApprovalUser()
    {
        return $this->belongsTo(User::class, 'dept_approval_user_id');
    }

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
