<?php

namespace App\Http\Controllers;

use App\Helpers\UserHelper;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function postUser(Request $request): JsonResponse
    {
        try {
            $userData = UserHelper::validateHashCreateUserData($request);

            $newUser = User::create($userData);

            $response = [
                'status' => 'success',
                'message' => 'registrasi sukses!',
                'data' => $newUser,
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

            if ($e instanceof QueryException) {
                /**
                 * SQLSTATE[23505]: Unique violation: 7 ERROR:  duplicate key value violates unique constraint 
                 * \"users_email_unique\"\nDETAIL:  Key (email)=(davidpinarto90@gmail.com) already exists. (SQL: insert into \"users\" 
                 * (\"name\", \"email\", \"password\", \"id\", \"updated_at\", \"created_at\") values (david, davidpinarto90@gmail.com, 
                 * $2y$10$TdrTt0lYUDvOYuBKPEucf.kPUkZUUjw3I4CLvulhjN7aFORPQDWgm, user-x48ZtAipvcYnQIiQ, 2024-08-07 07:01:02, 2024-08-07 
                 * 07:01:02))
                 * 
                 * if ($e->getCode() == '23505') { // Unique constraint violation code }
                 */
                $response = [
                    'status' => 'fail',
                    'message' => $e->getMessage(),
                ];
                return response()->json($response, 400);
            }

            $response = [
                'status' => 'fail',
                'message' => 'There is something error on our server',
            ];
            return response()->json($response, 500);
        }
    }
}
