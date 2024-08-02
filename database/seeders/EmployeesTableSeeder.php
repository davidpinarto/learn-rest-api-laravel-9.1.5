<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class EmployeesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('employees')->insert([
            [
                'id' => 'employee-' . (string) Str::random(16),
                'first_name' => 'Eko',
                'last_name' => 'Prasetyo',
                'gender' => 'male',
                'email' => 'eko@gmail.com',
                'phone_number' => '1234567890',
                'hire_date' => now(),
                'job_title' => 'Frontend Developer',
                'department_id' => DB::table('departments')->where('department_name', 'Engineer')->value('id'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'employee-' . (string) Str::random(16),
                'first_name' => 'David',
                'last_name' => 'Pinarto',
                'gender' => 'male',
                'email' => 'davidpinarto90@gmail.com',
                'phone_number' => '2983812743',
                'hire_date' => now(),
                'job_title' => 'Backend Developer',
                'department_id' => DB::table('departments')->where('department_name', 'Engineer')->value('id'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'employee-' . (string) Str::random(16),
                'first_name' => 'Sandy',
                'last_name' => 'Santoso',
                'gender' => 'female',
                'email' => 'sandysantoso@gmail.com',
                'phone_number' => null,
                'hire_date' => now(),
                'job_title' => 'Sales',
                'department_id' => DB::table('departments')->where('department_name', 'Marketing')->value('id'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
