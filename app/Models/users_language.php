<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class users_language extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'users_language';

    // Define which fields can be mass-assigned
    protected $fillable = [
        'id',
        'users_id',
        'language',
        'verbal',
        'written',
        'created_at',
        'updated_at',
    ];
}
