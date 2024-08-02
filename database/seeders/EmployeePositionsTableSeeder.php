<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class EmployeePositionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('employees_positions')->insert([
            [
                'id' => 'employee-positions-' . (string) Str::random(16),
                'employee_id' => DB::table('employees')->where('email', 'eko@gmail.com')->value('id'),
                'position' => 'Frontend Developer',
                'salary' => '10000000',
                'start_date' => now(),
                'end_date' => now()->addYear(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'employee-positions-' . (string) Str::random(16),
                'employee_id' => DB::table('employees')->where('email', 'davidpinarto90@gmail.com')->value('id'),
                'position' => 'Backend Developer',
                'salary' => '12000000',
                'start_date' => now(),
                'end_date' => now()->addYear(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'employee-positions-' . (string) Str::random(16),
                'employee_id' => DB::table('employees')->where('email', 'sandysantoso@gmail.com')->value('id'),
                'position' => 'Sales',
                'salary' => '12000000',
                'start_date' => now(),
                'end_date' => now()->addYear(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
