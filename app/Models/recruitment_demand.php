<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class recruitment_demand extends Model
{
    use HasFactory;
    // Define the table name if it's not the plural of the model name
    protected $table = 'recruitment_demand';

    // Define which fields can be mass-assigned
    protected $fillable = [
        'id',
        'recruitment_demand_id',
        'maker_id',
        'status_demand',
        'department_id',
        'position_id',
        'opening_date',
        'closing_date',
        'status_job',
        'reason',
        'qty_needed',
        'qty_fullfil',
        'gender',
        'job_goal',
        'education',
        'major',
        'experience',
        'length_of_working',
        'time_work_experience',
        'response_reason',
        'response_id',
        'skills',
        'created_at',
        'updated_at',
    ];


    // In your recruitment_demand model// In RecruitmentDemand model
    public function departmentRelation()
    {
        return $this->belongsTo(EmployeeDepartment::class, 'department_id');
    }

    public function positionRelation()
    {
        return $this->belongsTo(EmployeePosition::class, 'position_id');
    }

    public function maker()
    {
        return $this->belongsTo(User::class, 'maker_id');
    }

    public function responder()
    {
        return $this->belongsTo(User::class, 'response_id');
    }
}
