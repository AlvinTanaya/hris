<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class recruitment_applicant extends Model
{
    use HasFactory;

    protected $table = 'recruitment_applicant';

    protected $fillable = [
        'recruitment_demand_id',
        'name',
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
        'emergency_contact',
        'expected_salary',
        'expected_facility',
        'expected_benefit',
        'status_applicant',
        'interview_date',
        'interview_note',
        'exchange_note',
        'status_note',
        'distance',
        'created_at',
        'updated_at'
    ];

    /**
     * Get the education records for the applicant.
     */
    public function education()
    {
        return $this->hasMany(recruitment_applicant_education::class, 'applicant_id');
    }

    /**
     * Get the training records for the applicant.
     */
    public function training()
    {
        return $this->hasMany(recruitment_applicant_training::class, 'applicant_id');
    }

    /**
     * Get the work experience records for the applicant.
     */
    public function work_experience()
    {
        return $this->hasMany(recruitment_applicant_work_experience::class, 'applicant_id');
    }

    /**
     * Get the organization records for the applicant.
     */
    public function organization()
    {
        return $this->hasMany(recruitment_applicant_organization::class, 'applicant_id');
    }

    /**
     * Get the recruitment demand that this applicant belongs to.
     */
    public function demand()
    {
        return $this->belongsTo(recruitment_demand::class, 'recruitment_demand_id');
    }
}