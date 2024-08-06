<?php

namespace App\Http\Controllers;

use App\Models\RefreshToken;
use Illuminate\Http\Request;
use App\Models\User;
// use Illuminate\Support\Facades\Auth;
// use Tymon\JWTAuth\Facades\JWTAuth;
// use Tymon\JWTAuth\Exceptions\JWTException;
// use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Firebase\JWT\JWT;

class AuthController extends Controller
{
    public function login(Request $request)
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

            $user = User::where('email', $validatedData['email'])->get(); // null or array collection
            // dump(json_encode($user));
            // dump($user[0]);
            // dump($user[0]->getOriginal()['password']);
            // dump($validatedData['email']);

            if ($user) {
                $inputPassword = $validatedData['password'];
                $hashedPassword = $user[0]->getOriginal()['password'];

                // var_dump(Hash::check($inputPassword, $hashedPassword));
                if (!Hash::check($inputPassword, $hashedPassword)) {
                    throw new Exception('wrong email or password');
                }

                $accessTokenClaims = [
                    'id' => $user[0]->getOriginal()['id'],
                    'email' => $user[0]->getOriginal()['email'],
                    'exp' => '360000' // seharusnya 60 detik, namun untuk memperlancar proses developemnt saya set 360000
                ];
                $accessToken = JWT::encode($accessTokenClaims, env('JWT_SECRET'), 'HS256');

                $refreshTokenClaims = [
                    'id' => $user[0]->getOriginal()['id'],
                    'email' => $user[0]->getOriginal()['email'],
                ];
                $refreshToken = JWT::encode($refreshTokenClaims, env('JWT_SECRET'), 'HS256');
                RefreshToken::create(['token' => $refreshToken]);

                $response = [
                    'status' => 'success',
                    'message' => 'login success',
                    'data' => [
                        'access_token' => $accessToken,
                        'refresh_token' => $refreshToken
                    ],
                ];
                return response()->json($response, 201);
            } else {
                throw new Exception('wrong email or password');
            }
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

    public function renewToken(Request $request)
    {
        /**
         * TODO
         * - verify refresh token in the database ? next : invalid refresh token
         * - verify the refresh token secret, match ? create new acces token : invalid refresh token
         * - create new access token
         * - return new access token
         */
    }

    public function logout(Request $request)
    {
        /**
         * TODO
         * - verify refresh token in the database ? next : invalid refresh token
         * - delete refresh token in the database
         */
    }
}
