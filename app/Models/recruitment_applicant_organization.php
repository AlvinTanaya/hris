<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class recruitment_applicant_organization extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'recruitment_applicant_organization';

    // Define which fields can be mass-assigned
    protected $fillable = [
        'id',
        'applicant_id',
        'organization_name',
        'activity_type',
        'position',
        'city',
        'start_date',
        'end_date',
        'created_at',
        'updated_at',
    ];
}
