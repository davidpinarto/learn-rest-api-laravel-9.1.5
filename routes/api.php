<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentsController;
use App\Http\Controllers\EmployeesController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware('jwt.verify')->group(function () {
    Route::get('/employees', [EmployeesController::class, 'getEmployees']);
    Route::post('/employees', [EmployeesController::class, 'postEmployee']);
    Route::get('/employees/search', [EmployeesController::class, 'getEmployeeByName']);
    Route::put('/employees/{id}', [EmployeesController::class, 'putEmployeeById']);
    Route::delete('/employees/{id}', [EmployeesController::class, 'deleteEmployeeById']);

    Route::get('/departments', [DepartmentsController::class, 'getDepartments']);
});

Route::post('/users', [UserController::class, 'postUser']);
Route::post('/authentications', [AuthController::class, 'postAuthentication']);
Route::delete('/authentications', [AuthController::class, 'deleteAuthentication']);
Route::put('/authentications', [AuthController::class, 'putAuthentication']);
