<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeesPositions extends Model
{
    use HasFactory;

    protected $table = 'employee_positions';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'employee_id', 'position', 'salary', 'start_date', 'end_date', 'created_at', 'updated_at'
    ];

    public function employee()
    {
        return $this->belongsTo(Employees::class);
    }
}
