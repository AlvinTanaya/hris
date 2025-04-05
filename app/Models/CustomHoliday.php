<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class CustomHoliday extends Model
{

    use HasFactory;

    protected $table = 'custom_holiday';

    protected $fillable = ['name', 'description', 'date'];

}