<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        }
    }
}
