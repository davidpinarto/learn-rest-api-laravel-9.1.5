<?php

namespace App\Helpers;

use App\Exceptions\ForbiddenException;
use App\Exceptions\InvariantException;
use App\Exceptions\NotFoundException;
use App\Models\Employees;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EmployeesHelper
{
  public static function verifyRequestBodyPost(Request $request): array
  {
    /**
     * In Laravel, when you use the validate method with the unique rule, Laravel automatically checks the database for the 
     * existence of the value you are validating. 
     */
    $validatedData = $request->validate([
      'first_name' => 'required|string|max:20',
      'last_name' => 'required|string|max:20',
      'gender' => 'required|string|in:male,female',
      'email' => 'required|email|max:50|unique:employees',  // unique and throw ValidationError if already in DB
      'phone_number' => 'nullable|string|max:50',
      'hire_date' => 'required|string',
      'job_title' => 'required|string',
      'department_id' => 'required|string|exists:departments,id',
    ]);

    $validatedData['id'] = 'employee-' . Str::random(16);

    return $validatedData;
  }

  public static function verifyUserAdmin(array $userData): void
  {
    $user = User::where('id', $userData['id'])->first();

    if (!$user->is_admin) {
      throw new ForbiddenException('only admin can add new employee data');
    }
  }

  public static function getEmployeesByName(string $name): Collection
  {
    $employees = Employees::where('first_name', 'ilike', "%$name%")
      ->orWhere('last_name', 'ilike', "%$name%")
      ->get(); // will get the data and return an collection, check with count() or isEmpty() if the model find the data

    if ($employees->isEmpty()) {
      throw new NotFoundException("employee not found");
    }

    return $employees;
  }

  public static function getEmployeeById(string $id): Employees
  {
    $employee = Employees::find($id); // null or object

    if (!$employee) {
      throw new NotFoundException('employee not found');
    }

    return $employee;
  }

  public static function verifyRequestBodyPut(Request $request): array
  {
    $validatedData = $request->validate([
      'first_name' => 'string|max:20',
      'last_name' => 'string|max:20',
      'gender' => 'string|in:male,female',
      'email' => 'email|max:50|unique:employees',
      'phone_number' => 'nullable|string|max:50',
      'hire_date' => 'string',
      'job_title' => 'string',
      'department_id' => 'string|exists:departments,id',
    ]);

    if (!count($validatedData)) {
      throw new InvariantException('Must include min 1 employee data on request body');
    };

    return $validatedData;
  }

  public static function deleteEmployeeById(string $id): void
  {
    $employee = Employees::find($id)->delete(); // int(0) or int(1)

    if (!$employee) {
      throw new NotFoundException('employee not found');
    }
  }
}
