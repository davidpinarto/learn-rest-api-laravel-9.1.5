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
                return response()->json(['message' => 'Missing token on the Bearer'], 401);
            }

            $credentials = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));

            $request->userData = [
                'id' => $credentials->id,
                'email' => $credentials->email
            ];
            // var_dump($request->userData);

            return $next($request);
        } catch (Exception $e) {
            return response()->json(['message' => 'Token is invalid or expired'], 401);
        }
    }
}
