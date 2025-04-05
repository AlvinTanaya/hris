<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisciplineRule extends Model
{
    use HasFactory;


    protected $table = 'discipline_rules';


    protected $fillable = [
        'rule_type',
        'min_value',
        'max_value',
        'occurrence',
        'score_value',
        'operation',
    ];
}
