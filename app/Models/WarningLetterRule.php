<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarningLetterRule extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'rule_warning_letter';

    // Define which fields can be mass-assigned
    protected $fillable = [
        'id',
        'name',
        'description',
        'expired_length',
        'created_at',
        'updated_at',
    ];
}
