<?php

namespace App\Http\Controllers;

use App\Models\RefreshToken;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\JsonResponse;
use stdClass;

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
                $this->_verifyMatchPassword($inputPassword, $hashedPassword);

                $accessToken = $this->_generateAccessToken($user->id, $user->email);
                $refreshToken = $this->_generateAndPutRefreshTokenOnDB($user->id, $user->email);

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

            $this->_verifyRefreshTokenFromDB($refreshToken);
            $decoded = $this->_verifyRefreshTokenSecret($refreshToken);

            $newAccessToken = $this->_generateAccessToken($decoded->id, $decoded->email);

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
            return response()->json($response, 401);
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

            $this->_verifyAndDeleteRefreshTokenFromDB($refreshToken);

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

    private function _verifyAndDeleteRefreshTokenFromDB(string $refreshToken): void
    {
        // verify refresh token in db
        $refreshTokenFromDB = RefreshToken::where('token', $refreshToken)->delete(); // int(0) or int(1)
        // var_dump('okay from db');
        // var_dump($refreshTokenFromDB);
        if (!$refreshTokenFromDB) {
            throw new Exception('Token is not valid');
        }
    }

    private function _verifyRefreshTokenSecret(string $refreshToken): stdClass
    {
        // if the secret wrong it will throw "Signature verification failed"
        $decoded = JWT::decode($refreshToken,  new Key(env('JWT_SECRET'), 'HS256'));  // object(stdClass)
        return $decoded;
    }

    private function _verifyRefreshTokenFromDB(string $refreshToken): void
    {
        $refreshTokenFromDB = RefreshToken::where('token', $refreshToken)->first(); // null or class model
        if (!$refreshTokenFromDB) {
            throw new Exception('Token is not valid');
        }
    }

    private function _generateAccessToken(string $id, string $email): string
    {
        $accessTokenClaims = [
            'id' => $id,
            'email' => $email,
            'exp' => time() + '360000' // seharusnya 60 detik, namun untuk memperlancar proses developemnt saya set 360000
            // 'exp' => time() + 30
        ];
        $accessToken = JWT::encode($accessTokenClaims, env('JWT_SECRET'), 'HS256');
        return $accessToken;
    }

    private function _generateAndPutRefreshTokenOnDB(string $id, string $email): string
    {
        $refreshTokenClaims = [
            'id' => $id,
            'email' => $email,
            'unique' => uniqid(), // agar token berubah setiap dibuat
        ];
        $refreshToken = JWT::encode($refreshTokenClaims, env('JWT_SECRET'), 'HS256');

        RefreshToken::create(['token' => $refreshToken]);

        return $refreshToken;
    }

    private function _verifyMatchPassword($inputPassword, $hashedPassword): void
    {
        // var_dump(Hash::check($inputPassword, $hashedPassword));
        if (!Hash::check($inputPassword, $hashedPassword)) {
            throw new Exception('wrong email or password');
        }
    }
}
