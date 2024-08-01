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
        $response = [
            'status' => 'success',
            'data' => $this->employees
        ]
        ;
        return response()->json($response);
    }

    // public function getEmployees(Request $request): Response
    // {
    //     return response(json_encode($this->employees), 200)
    //       ->header('Content-Type', 'application/json');
    // }

    public function postEmployee(Request $request): JsonResponse
    {
        $employee = [
            'id' => count($this->employees) + 1,
            'name' => $request->input('name'),
            'position' => $request->input('position'),
            'salary' => $request->input('salary')
        ];
        // $this->employees[] = $employee;
        array_push($this->employees, $employee);

        $response = [
            'status' => 'success',
            'message' => 'karyawan berhasil di tambahkan',
            'data' => $this->employees,
        ];

        return response()->json($response, 201);
    }
}
