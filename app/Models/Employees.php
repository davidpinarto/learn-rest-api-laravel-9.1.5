<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employees extends Model
{
    use HasFactory;

    protected $table = 'employees';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'first_name', 'last_name', 'gender', 'email', 'phone_number', 'hire_date', 'job_title', 'department_id', 'created_at', 'updated_at'
    ];

    public function department()
    {
        return $this->belongsTo(Departments::class);
    }

    public function positions()
    {
        return $this->hasMany(EmployeesPositions::class);
    }
}
