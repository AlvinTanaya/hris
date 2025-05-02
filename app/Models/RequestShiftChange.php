<?php

// RequestShiftChange Model Update
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestShiftChange extends Model
{
    use HasFactory;

    protected $table = 'request_shift_change';

    protected $fillable = [
        'user_id',
        'rule_user_id_before',
        'rule_user_id_after',
        'user_exchange_id',
        'rule_user_exchange_id_before',
        'rule_user_exchange_id_after',
        'reason_change',
        'status_change',
        'date_change_start',
        'date_change_end',
        'declined_reason',
        'dept_approval_status',
        'dept_approval_user_id',
        'admin_approval_status',
        'admin_approval_user_id',
    ];

    // Relationship with user who requested the change
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship with exchange user
    public function exchangeUser()
    {
        return $this->belongsTo(User::class, 'user_exchange_id');
    }

    // Relationship with user's current shift rule
    public function ruleShiftBefore()
    {
        return $this->belongsTo(rule_shift::class, 'rule_user_id_before');
    }

    // Relationship with user's requested shift rule
    public function ruleShiftAfter()
    {
        return $this->belongsTo(rule_shift::class, 'rule_user_id_after');
    }

    // Relationship with exchange user's current shift rule
    public function ruleExchangeBefore()
    {
        return $this->belongsTo(rule_shift::class, 'rule_user_exchange_id_before');
    }

    // Relationship with exchange user's new shift rule
    public function ruleExchangeAfter()
    {
        return $this->belongsTo(rule_shift::class, 'rule_user_exchange_id_after');
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
