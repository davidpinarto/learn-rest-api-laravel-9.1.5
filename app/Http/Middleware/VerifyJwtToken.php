<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use UnexpectedValueException;
use Firebase\JWT\SignatureInvalidException;
use App\Exceptions\UnauthorizedException;

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

            if (!$token) {
                throw new UnauthorizedException('Missing token on the Bearer');
            }

            $credentials = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));

            $request->userData = [
                'id' => $credentials->id,
                'email' => $credentials->email
            ];

            return $next($request);
        } catch (Exception $e) {
            if (
                $e instanceof UnauthorizedException
                || $e instanceof SignatureInvalidException
                || $e instanceof UnexpectedValueException  // ketika user mengisi nilai sembarang pada Bearer token
            ) {
                $response = [
                    'status' => 'fail',
                    'message' => $e->getMessage(),
                ];
                return response()->json($response, 401);
            }

            if ($e instanceof ExpiredException) {
                $response = [
                    'status' => 'fail',
                    'message' => 'Token is invalid or expired'
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
}
