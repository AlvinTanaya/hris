<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class recruitment_applicant_work_experience extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'recruitment_applicant_work_experience';

    // Define which fields can be mass-assigned
    protected $fillable = [
        'id',
        'applicant_id',
        'company_name',
        'position',
        'working_start',
        'working_end',
        'company_address',
        'company_phone',
        'salary',
        'supervisor_name',
        'supervisor_phone',
        'job_desc',
        'reason',
        'benefit',
        'facility',
        'created_at',
        'updated_at',
    ];

    public function applicant()
    {
        return $this->belongsTo(recruitment_applicant::class, 'applicant_id');
    }
}
