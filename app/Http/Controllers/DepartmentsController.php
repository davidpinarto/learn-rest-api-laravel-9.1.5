<?php

namespace App\Http\Controllers;

use App\Models\Departments;
use Illuminate\Http\JsonResponse;

class DepartmentsController extends Controller
{
    public function getDepartments(): JsonResponse
    {
        $departments = Departments::all();

        $response = [
            'status' => 'success',
            'data' => $departments
        ];
        return response()->json($response);
    }
}
