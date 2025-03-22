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
        'position',
        'department',
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

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
