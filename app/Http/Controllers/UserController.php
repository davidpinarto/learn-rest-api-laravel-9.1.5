<?php

namespace App\Http\Controllers;

use App\Helpers\UserHelper;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function postUser(Request $request): JsonResponse
    {
        $userData = UserHelper::validateHashCreateUserData($request);

        $newUser = User::create($userData);

        $response = [
            'status' => 'success',
            'message' => 'registrasi sukses!',
            'data' => $newUser,
        ];
        return response()->json($response, 201);
    }
}
