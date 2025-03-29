<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class notification extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'notification';

    // Define which fields can be mass-assigned
    protected $fillable = [
        'id',
        'message',
        'type',
        'maker_id',
        'users_id',
        'status',
        'created_at',
        'updated_at',
    ];

    public function maker()
    {
        return $this->belongsTo(User::class, 'maker_id');
    }

    // Relasi ke User sebagai penerima (penerima pengumuman)
    public function users()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

}
