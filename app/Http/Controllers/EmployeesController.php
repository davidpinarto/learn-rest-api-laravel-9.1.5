<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmployeesController extends Controller
{
    private $employees;

    public function __construct()
    {
        $this->employees = [
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
        ];
    }

    public function getEmployees(Request $request): JsonResponse
    {
        return response()->json($this->employees);
    }

    // public function getEmployees(Request $request): Response
    // {
    //     return response(json_encode($this->employees), 200)
    //       ->header('Content-Type', 'application/json');
    // }
}
