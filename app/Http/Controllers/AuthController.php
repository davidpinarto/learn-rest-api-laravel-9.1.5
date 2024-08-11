<?php

namespace App\Http\Controllers;

use App\Exceptions\InvariantException;
use App\Exceptions\UnauthorizedException;
use App\Helpers\AuthHelper;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use UnexpectedValueException;

class AuthController extends Controller
{
    public function postAuthentication(Request $request): JsonResponse
    {
        try {
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
        } catch (Exception $e) {
            if ($e instanceof ValidationException) {
                $response = [
                    'status' => 'fail',
                    'message' => $e->validator->errors()->first(),
                ];
                return response()->json($response, 400);
            }

            if ($e instanceof UnauthorizedException) {
                $response = [
                    'status' => 'fail',
                    'message' => $e->getMessage(),
                ];
                return response()->json($response, 401);
            }

            $response = [
                'status' => 'fail',
                'message' => 'There is something error on our server',
            ];
            return response()->json($response, 500);
        }
    }

    public function putAuthentication(Request $request): JsonResponse
    {
        try {
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
        } catch (Exception $e) {
            if ($e instanceof ValidationException) {
                $response = [
                    'status' => 'fail',
                    'message' => $e->validator->errors()->first(),
                ];
                return response()->json($response, 400);
            }

            if (
                $e instanceof InvariantException
                || $e instanceof SignatureInvalidException
                || $e instanceof UnexpectedValueException  // ketika user mengisi nilai sembarang pada requ body refreshToken
            ) {
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

    public function deleteAuthentication(Request $request): JsonResponse
    {
        try {
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
        } catch (Exception $e) {
            if ($e instanceof ValidationException) {
                $response = [
                    'status' => 'fail',
                    'message' => $e->validator->errors()->first(),
                ];
                return response()->json($response, 400);
            }

            if ($e instanceof InvariantException) {
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
