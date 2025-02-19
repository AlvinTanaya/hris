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
        'start_working',
        'end_working',
        'created_at',
        'updated_at',
    ];
}
