<?php

namespace App\Http\Controllers;

use App\Models\Employees;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class EmployeesController extends Controller
{
    public function getEmployees(Request $request): JsonResponse
    {
        $employees = Employees::all();

        $response = [
            'status' => 'success',
            'data' => $employees
        ];
        return response()->json($response);
    }

    // public function getEmployees(Request $request): Response
    // {
    //     $employees = Employees::all();
    //     return response(json_encode($employees), 200)
    //         ->header('Content-Type', 'application/json');
    // }

    public function postEmployee(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'first_name' => 'required|string|max:20',
                'last_name' => 'required|string|max:20',
                'gender' => 'required|string|in:male,female',
                'email' => 'required|email|max:50|unique:employees',
                'phone_number' => 'nullable|string|max:50',
                'hire_date' => 'required|string',
                'job_title' => 'required|string',
                'department_id' => 'required|string|exists:departments,id',
            ]);

            $validatedData['id'] = 'employee-' . Str::random(16);

            $employee = Employees::create($validatedData);

            $response = [
                'status' => 'success',
                'message' => 'Karyawan berhasil ditambahkan',
                'data' => $employee,
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
        $name = $request->query('name'); // null or string

        if ($name) {
            try {
                $employees = Employees::where('first_name', 'ilike', "%$name%")
                    ->orWhere('last_name', 'ilike', "%$name%")
                    ->get(); // will get the data and return an object, check with count() if the model find the data
                // var_dump(count($employees));
                // dump(count($employees));
                // dump($employees->first());

                if (count($employees)) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Karyawan berhasil ditemukan',
                        'data' => $employees,
                    ]);
                } else {
                    throw new Exception("Karyawan tidak ditemukan");
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
            'message' => 'Karyawan tidak ditemukan',
        ], 404);
    }

    public function updateEmployeeById(Request $request, string $id): JsonResponse
    {
        try {
            $employee = Employees::find($id); // null or object
            // dump($employee);
            // dump(is_object($employee));
            // dump(count($employee));

            if ($employee) {
                $validatedData = $request->validate([
                    'first_name' => 'string|max:20',
                    'last_name' => 'string|max:20',
                    'gender' => 'string|in:male,female',
                    'email' => 'email|max:50|unique:employees',
                    'phone_number' => 'nullable|string|max:50',
                    'hire_date' => 'string', // TODO: tidak boleh di ubah
                    'job_title' => 'string',
                    'department_id' => 'string|exists:departments,id',
                ]);

                // tidak perlu karena fitur ORM dari method update akan memperbarui column updated_at
                // $validatedData['updated_at'] = now();

                $employee->update($validatedData); // fitur ORM dari method update akan memperbarui column updated_at

                return response()->json([
                    'status' => 'success',
                    'message' => 'Data karyawan berhasil diperbarui',
                    'data' => $employee,
                ]);
            } else {
                throw new Exception('Karyawan tidak ditemukan');
            }
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'fail',
                'message' => $e->validator->errors()->first(),
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'fail',
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function deleteEmployeeById(Request $request, string $id): JsonResponse
    {
        try {
            $employee = Employees::find($id); // null or object

            if ($employee) {
                $employee->delete(); 

                return response()->json([
                    'status' => 'success',
                    'message' => 'Data karyawan berhasil dihapus',
                ]);
            } else {
                throw new Exception('Karyawan tidak ditemukan');
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'fail',
                'message' => $e->getMessage(),
            ], 404);
        }
    }
}
