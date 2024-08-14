<?php

namespace App\Http\Controllers;

use App\Helpers\EmployeesHelper;
use App\Jobs\AddNewEmployee;
use App\Models\Employees;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeesController extends Controller
{
    public function getEmployees(): JsonResponse
    {
        $employees = Employees::all();

        $response = [
            'status' => 'success',
            'data' => $employees
        ];
        return response()->json($response);
    }

    public function postEmployee(Request $request): JsonResponse
    {
        EmployeesHelper::verifyUserAdmin($request->userData);  // ForbiddenException

        $requestBodyData = EmployeesHelper::verifyRequestBodyPost($request);  // ValidationError

        AddNewEmployee::dispatch($requestBodyData);

        $response = [
            'status' => 'success',
            'message' => 'new employee added successfully',
        ];
        return response()->json($response, 201);
    }

    public function getEmployeeByName(Request $request): JsonResponse
    {
        $name = $request->query('name'); // null or string

        $employees = EmployeesHelper::getEmployeesByName($name); // NotFoundException

        $response = [
            'status' => 'success',
            'data' => $employees,
        ];
        return response()->json($response);
    }

    public function updateEmployeeById(Request $request, string $id): JsonResponse
    {
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
    }

    public function deleteEmployeeById(Request $request, string $id): JsonResponse
    {
        EmployeesHelper::verifyUserAdmin($request->userData);  // ForbiddenException
        EmployeesHelper::deleteEmployeeById($id); // NotFoundException

        $response = [
            'status' => 'success',
            'message' => 'employee deleted successfully',
        ];
        return response()->json($response);
    }
}
