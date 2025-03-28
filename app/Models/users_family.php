<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class users_family extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'users_family';

    // Define which fields can be mass-assigned
    protected $fillable = [
        'id',
        'users_id',
        'name',
        'relation',
        'birth_date',
        'birth_place',
        'ID_number',
        'phone_number',
        'address',
        'gender',
        'job',
        'created_at',
        'updated_at',
    ];
}
