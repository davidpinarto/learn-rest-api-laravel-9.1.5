<?php

namespace App\Http\Controllers;

use App\Helpers\AuthHelper;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function postAuthentication(Request $request): JsonResponse
    {
        /**
         * TODO
         * - get the email and password from req body
         * - find the user by email ? get the password : email or password wrong
         * - decrypt the password ? password match then create access token : email or password wrong
         * - create access token and include the user id or email on the payload // expired every 60 seconds
         * - create refresh token and include the user id or email on the payload
         * - return 201 with JSON access token and refresh token
         */
        try {
            $validatedData = $request->validate([
                'email' => 'required|string|max:255',
                'password' => 'required|string|min:8|max:50',
            ]);

            $user = User::where('email', $validatedData['email'])->first(); // null or class model
            // dump($user);
            // dump(json_encode($user));
            // dump($user[0]->getOriginal()['password']);
            // dump($validatedData['email']);

            if ($user) {
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

            throw new Exception('wrong email or password');
        } catch (ValidationException $e) {
            $response = [
                'status' => 'fail',
                'message' => $e->validator->errors()->first(),
            ];
            return response()->json($response, 400);
        } catch (Exception $e) {
            $response = [
                'status' => 'fail',
                'message' => $e->getMessage(),
            ];
            return response()->json($response, 401);
        }
    }

    public function putAuthentication(Request $request): JsonResponse
    {
        /**
         * TODO
         * - verify refresh token in the database ? next : invalid refresh token
         * - verify the refresh token secret, match ? create new acces token : invalid refresh token
         * - create new access token
         * - return new access token
         */
        try {
            $validatedData = $request->validate([
                'refreshToken' => 'required|string',
            ]);
            $refreshToken = $validatedData['refreshToken'];

            AuthHelper::verifyRefreshTokenFromDB($refreshToken);
            $decoded = AuthHelper::verifyRefreshTokenSecret($refreshToken);

            $newAccessToken = AuthHelper::generateAccessToken($decoded->id, $decoded->email);

            $response = [
                'status' => 'success',
                'message' => 'Create new access token success',
                'data' => [
                    'access_token' => $newAccessToken
                ]
            ];
            return response()->json($response);
        } catch (ValidationException $e) {
            $response = [
                'status' => 'fail',
                'message' => $e->validator->errors()->first(),
            ];
            return response()->json($response, 400);
        } catch (Exception $e) {
            $response = [
                'status' => 'fail',
                'message' => $e->getMessage(),
            ];
            return response()->json($response, 400);
        }
    }

    public function deleteAuthentication(Request $request): JsonResponse
    {
        /**
         * TODO
         * - verify refresh token in the database ? next : invalid refresh token
         * - delete refresh token in the database
         */
        try {
            $validatedData = $request->validate([
                'refreshToken' => 'required|string',
            ]);
            $refreshToken = $validatedData['refreshToken'];

            AuthHelper::verifyAndDeleteRefreshTokenFromDB($refreshToken);

            $response = [
                'status' => 'success',
                'message' => 'Token deleted successfully'
            ];
            return response()->json($response);
        } catch (ValidationException $e) {
            $response = [
                'status' => 'fail',
                'message' => $e->validator->errors()->first(),
            ];
            return response()->json($response, 400);
        } catch (Exception $e) {
            $response = [
                'status' => 'fail',
                'message' => $e->getMessage(),
            ];
            return response()->json($response, 400);
        }
    }
}
