<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class recruitment_applicant_education extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'recruitment_applicant_education';

    // Define which fields can be mass-assigned
    protected $fillable = [
        'id',
        'applicant_id',
        'degree',
        'educational_place',
        'educational_city',
        'educational_province',
        'transcript_file_path',
        'start_education',
        'end_education',
        'grade',
        'major',
        'created_at',
        'updated_at',
    ];

    public function applicant()
    {
        return $this->belongsTo(recruitment_applicant::class, 'applicant_id');
    }
}
