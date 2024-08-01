<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

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
        ];
        return response()->json($response);
    }

    // public function getEmployees(Request $request): Response
    // {
    //     return response(json_encode($this->employees), 200)
    //       ->header('Content-Type', 'application/json');
    // }

    public function postEmployee(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'position' => 'required|string|max:255',
                'salary' => 'required|numeric|min:0',
            ]);

            $employee = [
                'id' => count($this->employees) + 1,
                'name' => $validatedData['name'],
                'position' => $validatedData['position'],
                'salary' => $validatedData['salary'],
            ];
            array_push($this->employees, $employee);

            $response = [
                'status' => 'success',
                'message' => 'karyawan berhasil di tambahkan',
                'data' => $this->employees,
            ];
            return response()->json($response, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'fail',
                'message' => $e->validator->errors()->first()
            ], 400);
        }
    }

    public function getEmployeeByName(Request $request): JsonResponse
    {
        $name = $request->query('name');

        if ($name) {
            try {
                $employee = collect($this->employees)->firstWhere('name', $name);

                if ($employee) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'karyawan berhasil ditemukan',
                        'data' => $employee,
                    ]);
                } else {
                    throw new Exception("karyawan tidak ditemukan");
                }
            } catch (Exception $e) {
                return response()->json([
                    'status' => 'fail',
                    'message' => $e->getMessage(),
                ], 404);
            }
        }

        return response()->json([
            'status' => 'fail',
            'message' => 'karyawan tidak ditemukan',
        ], 404);
    }
}
