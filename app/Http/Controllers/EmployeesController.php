<?php

namespace App\Http\Controllers;

use App\Exceptions\ForbiddenException;
use App\Exceptions\InvariantException;
use App\Exceptions\NotFoundException;
use App\Helpers\EmployeesHelper;
use App\Models\Employees;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EmployeesController extends Controller
{
    public function getEmployees(): JsonResponse
    {
        try {
            $employees = Employees::all();

            $response = [
                'status' => 'success',
                'data' => $employees
            ];
            return response()->json($response);
        } catch (Exception $e) {
            $response = [
                'status' => 'fail',
                'message' => 'There is something error on our server',
            ];
            return response()->json($response, 500);
        }
    }

    public function postEmployee(Request $request): JsonResponse
    {
        try {
            EmployeesHelper::verifyUserAdmin($request->userData);  // ForbiddenException

            $requestBodyData = EmployeesHelper::verifyRequestBodyPost($request);  // ValidationError

            $employee = Employees::create($requestBodyData); // QueryException if email already been taken code 23000

            $response = [
                'status' => 'success',
                'message' => 'new employee added successfully',
                'data' => $employee,
            ];
            return response()->json($response, 201);
        } catch (Exception $e) {
            if ($e instanceof ValidationException) {
                $response = [
                    'status' => 'fail',
                    'message' => $e->validator->errors()->first()
                ];
                return response()->json($response, 400);
            }

            // if ($e instanceof QueryException) {
            //     /**
            //      *  Check for unique constraint violation                 
            //      *  if ($e->getCode() === '23000') {} // SQLSTATE code for integrity constraint violation
            //      */
            //     return response()->json([
            //         'status' => 'fail',
            //         'message' => $e->getMessage(),
            //     ], 400);
            // }

            if ($e instanceof ForbiddenException) {
                $response = [
                    'status' => 'fail',
                    'message' => $e->getMessage(),
                ];
                return response()->json($response, 403);
            }

            $response = [
                'status' => 'fail',
                'message' => 'There is something error on our server',
            ];
            return response()->json($response, 500);
        }
    }

    public function getEmployeeByName(Request $request): JsonResponse
    {
        try {
            $name = $request->query('name'); // null or string

            $employees = EmployeesHelper::getEmployeesByName($name); // NotFoundException

            $response = [
                'status' => 'success',
                'data' => $employees,
            ];
            return response()->json($response);
        } catch (Exception $e) {
            if ($e instanceof NotFoundException) {
                $response = [
                    'status' => 'fail',
                    'message' => $e->getMessage(),
                ];
                return response()->json($response, 404);
            }

            $response = [
                'status' => 'fail',
                'message' => 'There is something error on our server',
            ];
            return response()->json($response, 500);
        }
    }

    public function updateEmployeeById(Request $request, string $id): JsonResponse
    {
        try {
            EmployeesHelper::verifyUserAdmin($request->userData);  // ForbiddenException

            $employee = EmployeesHelper::getEmployeeById($id); // NotFoundException

            $requestBodyData = EmployeesHelper::verifyRequestBodyPut($request);  // InvariantException

            $employee->update($requestBodyData); // fitur ORM dari method update akan memperbarui column updated_at

            $response = [
                'status' => 'success',
                'message' => 'employee updated successfully',
                'data' => $employee,
            ];
            return response()->json($response);
        } catch (Exception $e) {
            if ($e instanceof ValidationException) {
                $response = [
                    'status' => 'fail',
                    'message' => $e->validator->errors()->first(),
                ];
                return response()->json($response, 400);
            }

            if ($e instanceof InvariantException) {
                $response = [
                    'status' => 'fail',
                    'message' => $e->getMessage(),
                ];
                return response()->json($response, 400);
            }

            if ($e instanceof ForbiddenException) {
                $response = [
                    'status' => 'fail',
                    'message' => $e->getMessage(),
                ];
                return response()->json($response, 403);
            }

            if ($e instanceof NotFoundException) {
                $response = [
                    'status' => 'fail',
                    'message' => $e->getMessage(),
                ];
                return response()->json($response, 404);
            }

            $response = [
                'status' => 'fail',
                'message' => 'There is something error on our server',
            ];
            return response()->json($response, 500);
        }
    }

    public function deleteEmployeeById(Request $request, string $id): JsonResponse
    {
        try {
            EmployeesHelper::verifyUserAdmin($request->userData);  // ForbiddenException
            EmployeesHelper::deleteEmployeeById($id); // NotFoundException

            $response = [
                'status' => 'success',
                'message' => 'employee deleted successfully',
            ];
            return response()->json($response);
        } catch (Exception $e) {
            if ($e instanceof ForbiddenException) {
                $response = [
                    'status' => 'fail',
                    'message' => $e->getMessage(),
                ];
                return response()->json($response, 403);
            }

            if ($e instanceof NotFoundException) {
                $response = [
                    'status' => 'fail',
                    'message' => $e->getMessage(),
                ];
                return response()->json($response, 404);
            };

            $response = [
                'status' => 'fail',
                'message' => 'There is something error on our server',
            ];
            return response()->json($response, 500);
        }
    }
}
