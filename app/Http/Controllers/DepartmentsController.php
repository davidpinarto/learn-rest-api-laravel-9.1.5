<?php

namespace App\Http\Controllers;

use App\Models\Departments;
use Exception;
use Illuminate\Http\JsonResponse;

class DepartmentsController extends Controller
{
    public function getDepartments(): JsonResponse
    {
        try {
            $departments = Departments::all();

            $response = [
                'status' => 'success',
                'data' => $departments
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
}
