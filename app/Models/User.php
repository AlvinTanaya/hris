<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'employee_id',
        'name',
        'position_id',
        'department_id',
        'email',
        'phone_number',
        'employee_status',
        'contract_start_date',
        'contract_end_date',
        'user_status',
        'join_date',
        'ID_number',
        'birth_date',
        'birth_place',
        'religion',
        'gender',
        'ID_address',
        'domicile_address',
        'height',
        'weight',
        'achievement_path',
        'sim',
        'sim_number',
        'blood_type',
        'bpjs_employment',
        'bpjs_health',
        'photo_profile_path',
        'cv_path',
        'ID_card_path',
        'password',
        'otp',
        'otp_expired_at',
        'remember_token',
        'NPWP',
        'bank_number',
        'bank_name',
        'emergency_contact',
        'status',
        'distance',
        'exit_date',
        'created_at',
        'updated_at'
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'contract_start_date' => 'date',
            'contract_end_date' => 'date',
            'join_date' => 'date',
            'birth_date' => 'date',
            'otp_expired_at' => 'datetime',
        ];
    }


    // Accessor for Position Name
    public function getPositionNameAttribute()
    {
        return $this->position ? $this->position->position : null;
    }

    // Accessor for Department Name
    public function getDepartmentNameAttribute()
    {
        return $this->department ? $this->department->department : null;
    }


    // Relationship with Position
    public function position()
    {
        return $this->belongsTo(EmployeePosition::class, 'position_id');
    }

    public function department()
    {
        return $this->belongsTo(EmployeeDepartment::class, 'department_id');
    }



    // Scope to filter by position
    public function scopeByPosition($query, $position)
    {
        return $query->whereHas('position', function ($q) use ($position) {
            $q->where('position', $position);
        });
    }

    // Scope to filter by department
    public function scopeByDepartment($query, $department)
    {
        return $query->whereHas('department', function ($q) use ($department) {
            $q->where('department', $department);
        });
    }

    // Additional utility methods
    public function isManagerAcptHR()
    {
        return $this->position &&
            $this->position->position === 'Manager' &&
            $this->department &&
            $this->department->department !== 'Human Resources';
    }

    // Cek kalau user adalah SuperAdmin: Director, General Manager, atau Manager di Human Resources
    public function isSuperAdmin()
    {
        if (!$this->position || !$this->department) {
            return false;
        }

        $position = $this->position->position;
        $department = $this->department->department;

        return in_array($position, ['Director', 'General Manager']) ||
            ($position === 'Manager' && $department === 'Human Resources');
    }

    // Cek kalau user adalah Supervisor
    public function isSupervisor()
    {
        return $this->position && $this->position->position === 'Supervisor';
    }

    // Cek kalau user adalah Staff
    public function isStaff()
    {
        return $this->position && $this->position->position === 'Staff';
    }


    public function latestTransfer()
    {
        return $this->hasOne(history_transfer_employee::class, 'users_id')
            ->latest('created_at');
    }

    /**
     * Get all transfer history records for this user.
     */
    public function transferHistory()
    {
        return $this->hasMany(history_transfer_employee::class, 'users_id');
    }


    public function salaryHistory()
    {
        return $this->hasMany(SalaryHistory::class, 'users_id');
    }
}
