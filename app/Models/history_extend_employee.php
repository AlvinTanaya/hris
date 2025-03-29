<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class history_extend_employee extends Model
{
    use HasFactory;

    protected $table = 'users_extend_history';
    
    protected $fillable = [
        'id',
        'users_id',
        'position_id',
        'department_id',
        'reason',
        'start_date',
        'end_date',
        'created_at',
        'updated_at',
    ];
    
    // Relationships
    public function position()
    {
        return $this->belongsTo(EmployeePosition::class, 'position_id');
    }
    
    public function department()
    {
        return $this->belongsTo(EmployeeDepartment::class, 'department_id');
    }
}