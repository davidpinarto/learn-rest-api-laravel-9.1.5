<?php

use App\Http\Controllers\AuthController;
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

Route::get('/employees', [EmployeesController::class, 'getEmployees']);
Route::post('/employees', [EmployeesController::class, 'postEmployee']);
Route::get('/employees/search', [EmployeesController::class, 'getEmployeeByName']);
Route::put('/employees/{id}', [EmployeesController::class, 'updateEmployeeById']);
Route::delete('/employees/{id}', [EmployeesController::class, 'deleteEmployeeById']);

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'login']);

// Route::middleware('auth')->get('/employees', [EmployeesController::class, 'getEmployees']);
// Route::middleware('auth')->post('/employees', [EmployeesController::class, 'postEmployee']);
// Route::middleware('auth')->get('/employees/search', [EmployeesController::class, 'getEmployeeByName']);
// Route::middleware('auth')->put('/employees/{id}', [EmployeesController::class, 'updateEmployeeById']);
// Route::middleware('auth')->delete('/employees/{id}', [EmployeesController::class, 'deleteEmployeeById']);

// Route::group([

//     'middleware' => 'api',
//     'prefix' => 'auth'

// ], function ($router) {

//     /**
//      * AuthController::class will return this as string: 'App\Http\Controllers\AuthController'
//      * so we can use it like this to shortcut the syntax
//      * AuthController@login
//      * this is same with [AuthController::class, 'login']
//      */
//     // Route::post('login', 'AuthController@login'); 
//     // Route::post('logout', 'AuthController@logout');
//     // Route::post('refresh', 'AuthController@refresh');
//     // Route::post('me', 'AuthController@me');
//     Route::post('login', [AuthController::class, 'login']);
//     Route::post('logout', [AuthController::class, 'logout']);
//     Route::post('refresh', [AuthController::class, 'refresh']);
//     // Route::post('me', [AuthController::class, 'me']);
// });
