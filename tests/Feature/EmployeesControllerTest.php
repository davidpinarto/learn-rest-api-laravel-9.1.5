<?php

namespace Tests\Feature;

use Tests\TestCase;

class EmployeesControllerTest extends TestCase
{
    public function testGetEmployeesController()
    {
        $response = $this->get('/api/employees');
        dump($response->getContent());

        $response->assertStatus(200);
        $response->assertJson([
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
        ]);
    }
}
