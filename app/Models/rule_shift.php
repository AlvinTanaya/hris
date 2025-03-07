<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class rule_shift extends Model
{
    use HasFactory;
    protected $table = 'rule_shift';

    protected $fillable = [
        'id',
        'type',
        'hour_start',
        'hour_end',
        'days',
        'created_at',
        'updated_at',
    ];
}
