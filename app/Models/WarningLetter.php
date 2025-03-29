<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarningLetter extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'employee_warning_letter';

    // Define which fields can be mass-assigned
    protected $fillable = [
        'id',
        'user_id',
        'maker_id',
        'type_id',
        'warning_letter_number',
        'reason_warning',
        'created_at',
        'updated_at',
        'expired_at',
    ];

    /**
     * Get the warning letter rule associated with this warning letter.
     */
    public function rule(): BelongsTo
    {
        return $this->belongsTo(WarningLetterRule::class, 'type_id');
    }

    /**
     * Get the employee who received this warning letter.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the maker (issuer) of this warning letter.
     */
    public function maker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'maker_id');
    }
}