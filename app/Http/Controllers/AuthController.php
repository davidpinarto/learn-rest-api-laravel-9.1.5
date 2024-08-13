<?php

namespace App\Http\Controllers;

use App\Helpers\AuthHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function postAuthentication(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'email' => 'required|string|max:255',
            'password' => 'required|string|min:8|max:50',
        ]);

        $user = AuthHelper::verifyAndGetUserDataInDB($validatedData['email']);

        $inputPassword = $validatedData['password'];
        $hashedPassword = $user->password;
        AuthHelper::verifyMatchPassword($inputPassword, $hashedPassword);

        $accessToken = AuthHelper::generateAccessToken($user->id, $user->email);
        $refreshToken = AuthHelper::generateAndPutRefreshTokenOnDB($user->id, $user->email);

        $response = [
            'status' => 'success',
            'message' => 'login success',
            'data' => [
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken
            ],
        ];
        return response()->json($response, 201);
    }

    public function putAuthentication(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'refreshToken' => 'required|string',
        ]);
        $refreshToken = $validatedData['refreshToken'];

        $decoded = AuthHelper::verifyRefreshTokenSecret($refreshToken);  // SignatureInvalidException or obj(stdclass)
        AuthHelper::verifyRefreshTokenFromDB($refreshToken);  // InvariantException

        $newAccessToken = AuthHelper::generateAccessToken($decoded->id, $decoded->email);

        $response = [
            'status' => 'success',
            'message' => 'Create new access token success',
            'data' => [
                'access_token' => $newAccessToken
            ]
        ];
        return response()->json($response);
    }

    public function deleteAuthentication(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'refreshToken' => 'required|string',
        ]);  // validation exception
        $refreshToken = $validatedData['refreshToken'];

        AuthHelper::verifyAndDeleteRefreshTokenFromDB($refreshToken);  // invariant exception

        $response = [
            'status' => 'success',
            'message' => 'Token deleted successfully'
        ];
        return response()->json($response);
    }
}
