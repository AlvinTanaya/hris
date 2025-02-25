<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class users_work_experience extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'users_work_experience';

    // Define which fields can be mass-assigned
    protected $fillable = [
        'id',
        'users_id',
        'company_name',
        'position',
        'start_working',
        'end_working',
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
}
