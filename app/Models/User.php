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
    protected $table = 'users'; // Nama tabel di database Anda

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
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

    // Relationship with Position
    public function position()
    {
        return $this->belongsTo(EmployeePosition::class, 'position_id');
    }
    
    public function department()
    {
        return $this->belongsTo(EmployeeDepartment::class, 'department_id');
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
    public function isManager()
    {
        return $this->position ? in_array($this->position->position, ['Manager', 'General Manager', 'Director']) : false;
    }

    public function isHR()
    {
        return $this->department ? $this->department->department === 'Human Resources' : false;
    }
}
