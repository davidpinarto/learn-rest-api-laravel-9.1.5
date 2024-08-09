<?php

namespace App\Http\Controllers;

use App\Models\Departments;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepartmentsController extends Controller
{
    public function getDepartments(Request $request): JsonResponse
    {
        $departments = Departments::all();

        $response = [
            'status' => 'success',
            'data' => $departments
        ];
        return response()->json($response);
    }

}
