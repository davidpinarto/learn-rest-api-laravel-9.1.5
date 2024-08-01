<?php

namespace Tests\Feature;

use Tests\TestCase;

class EmployeesControllerTest extends TestCase
{
    public function testGetEmployees()
    {
        $response = $this->get('/api/employees');
        dump($response->getContent());

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'data' => [
                [
                    "id" => 1,
                    "name" => "Eko",
                    "position" => "Frontend Developer",
                    "salary" => "10000000",
                ],
                [
                    "id" => 2,
                    "name" => "David",
                    "position" => "Backend Developer",
                    "salary" => "10000000",
                ],
                [
                    "id" => 3,
                    "name" => "Bryan",
                    "position" => "Architect",
                    "salary" => "15000000",
                ],
            ]
        ]);
    }

    public function testPostEmployeeSuccess()
    {
        $response = $this->post('/api/employees', [
            'name' => 'Iswandi',
            'position' => 'doctor',
            'salary' => '50000000'
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'status' => 'success',
            'message' => 'karyawan berhasil di tambahkan',
            'data' => [
                [
                    "id" => 1,
                    "name" => "Eko",
                    "position" => "Frontend Developer",
                    "salary" => "10000000",
                ],
                [
                    "id" => 2,
                    "name" => "David",
                    "position" => "Backend Developer",
                    "salary" => "10000000",
                ],
                [
                    "id" => 3,
                    "name" => "Bryan",
                    "position" => "Architect",
                    "salary" => "15000000",
                ],
                [
                    "id" => 4,
                    'name' => 'Iswandi',
                    'position' => 'doctor',
                    'salary' => '50000000'
                ],
            ]
        ]);
    }

    public function testPostEmployeeFailedName()
    {
        $response = $this->post('/api/employees', [
            'position' => 'doctor',
            'salary' => '50000000'
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'status' => 'fail',
            'message' => 'The name field is required.',
        ]);
    }

    public function testPostEmployeeFailedPosition()
    {
        $response = $this->post('/api/employees', [
            'name' => 'Iswandi',
            'salary' => '50000000'
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'status' => 'fail',
            'message' => 'The position field is required.',
        ]);
    }

    public function testPostEmployeeFailedSalary()
    {
        $response = $this->post('/api/employees', [
            'name' => 'Iswandi',
            'position' => 'doctor',
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'status' => 'fail',
            'message' => 'The salary field is required.',
        ]);
    }

    public function testGetEmployeeByNameSuccess()
    {
        $response = $this->get('/api/employees/search?name=David');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'karyawan berhasil ditemukan',
            'data' => [
                "id" => 2,
                "name" => "David",
                "position" => "Backend Developer",
                "salary" => "10000000",
            ]
        ]);
    }


    public function testGetEmployeeByNameFailed()
    {
        $response = $this->get('/api/employees/search?name=ups');

        $response->assertStatus(404);
        $response->assertJson([
            'status' => 'fail',
            'message' => 'karyawan tidak ditemukan',
        ]);
    }
}
