<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class recruitment_applicant extends Model
{
    protected $table = 'recruitment_applicant';

    protected $fillable = [
        'recruitment_demand_id',
        'name',
        'position',
        'department',
        'email',
        'phone_number',
        'ID_number',
        'birth_date',
        'birth_place',
        'religion',
        'gender',
        'ID_address',
        'domicile_address',
        'height',
        'weight',
        'blood_type',
        'bpjs_employment',
        'bpjs_health',
        'photo_profile_path',
        'cv_path',
        'ID_card_path',
        'achievement_path',
        'sim',
        'sim_number',
        'expected_salary',
        'expected_facility',
        'expected_benefit',
        'status_applicant',
        'interview_date',
        'interview_note',
        'exchange_note',
        'status_note',
        'created_at',
        'updated_at'
    ];
}
