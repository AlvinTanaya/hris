<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestResign extends Model
{
    use HasFactory;
    protected $table = 'request_resign';

    protected $fillable = [
        'id',
        'user_id',
        'resign_type',
        'resign_date',
        'resign_reason',
        'resign_status',
        'file_path',
        'declined_reason',
        'response_user_id',
        'created_at',
        'updated_at',
    ];
}
