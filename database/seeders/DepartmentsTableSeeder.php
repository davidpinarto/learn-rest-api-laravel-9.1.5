<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class DepartmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('departments')->insert([
            [
                'id' => 'department-' . (string) Str::random(16),
                'department_name' => 'Engineer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'department-' . (string) Str::random(16),
                'department_name' => 'Marketing',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
