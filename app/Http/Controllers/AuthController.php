<?php

namespace App\Http\Controllers;

use App\Helpers\AuthHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function postAuthentication(Request $request): JsonResponse
    {
        $rules = [
            'email' => 'required|string|max:255',
            'password' => 'required|string|min:8|max:50',
        ];
        ['email' => $email, 'password' => $inputPassword] = $request->validate($rules);

        $userData = AuthHelper::verifyAndGetUserDataInDB($email);

        $hashedPassword = $userData->password;
        AuthHelper::verifyMatchPassword($inputPassword, $hashedPassword);

        $accessToken = AuthHelper::generateAccessToken($userData->id, $email);
        $refreshToken = AuthHelper::generateAndPutRefreshTokenOnDB($userData->id, $email);

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
        $rules = [
            'refreshToken' => 'required|string',
        ];
        ['refreshToken' => $refreshToken] = $request->validate($rules);

        [
            'userId' => $userId,
            'userEmail' => $userEmail,
        ] = AuthHelper::verifyRefreshTokenSecretAndGetUserData($refreshToken);  // SignatureInvalidException or obj(stdclass)
        
        AuthHelper::verifyRefreshTokenFromDB($refreshToken);  // InvariantException

        $newAccessToken = AuthHelper::generateAccessToken($userId, $userEmail);

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
        $rules = [
            'refreshToken' => 'required|string',
        ];
        ['refreshToken' => $refreshToken] = $request->validate($rules);

        AuthHelper::verifyAndDeleteRefreshTokenFromDB($refreshToken);  // invariant exception

        $response = [
            'status' => 'success',
            'message' => 'Token deleted successfully'
        ];
        return response()->json($response);
    }
}
