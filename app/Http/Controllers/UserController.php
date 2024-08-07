<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function postUser(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:20',
                'email' => 'required|string|max:255',
                'password' => 'required|string|min:8|max:50',
            ]);

            $validatedData['id'] = 'user-' . Str::random(16);

            if (Str::endsWith($validatedData['email'], '@admin.com')) {
                $validatedData['is_admin'] = true;
            }

            $hashedPassword = Hash::make($validatedData['password']);
            $validatedData['password'] = $hashedPassword;
            // dump($validatedData);

            $newUser = User::create($validatedData);

            $response = [
                'status' => 'success',
                'message' => 'registrasi sukses!',
                'data' => $newUser,
            ];
            return response()->json($response, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'fail',
                'message' => $e->validator->errors()->first()
            ], 400);
        } catch (QueryException $e) {
            /**
             * SQLSTATE[23505]: Unique violation: 7 ERROR:  duplicate key value violates unique constraint 
             * \"users_email_unique\"\nDETAIL:  Key (email)=(davidpinarto90@gmail.com) already exists. (SQL: insert into \"users\" 
             * (\"name\", \"email\", \"password\", \"id\", \"updated_at\", \"created_at\") values (david, davidpinarto90@gmail.com, 
             * $2y$10$TdrTt0lYUDvOYuBKPEucf.kPUkZUUjw3I4CLvulhjN7aFORPQDWgm, user-x48ZtAipvcYnQIiQ, 2024-08-07 07:01:02, 2024-08-07 
             * 07:01:02))
             */
            if ($e->getCode() == '23505') { // Unique constraint violation code
                return response()->json([
                    'status' => 'fail',
                    'message' => 'The email address is already in use.'
                ], 400);
            }

            return response()->json([
                'status' => 'fail',
                'message' => 'Database error: ' . $e->getMessage()
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'fail',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
