<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

class VerifyJwtToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = $request->bearerToken();  // string of the token
            // var_dump($token);

            if (!$token) {
                $response = [
                    'status' => 'fail',
                    'message' => 'Missing token on the Bearer'
                ];
                return response()->json($response, 401);
            }

            $credentials = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));

            $request->userData = [
                'id' => $credentials->id,
                'email' => $credentials->email
            ];
            // var_dump($request->userData);

            return $next($request);
        } catch (Exception $e) {
            $response = [
                'status' => 'fail',
                'message' => 'Token is invalid or expired'
            ];
            return response()->json($response, 401);
        }
    }
}
