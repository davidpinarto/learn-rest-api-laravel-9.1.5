<?php

namespace App\Http\Middleware;

use App\Exceptions\InvariantException;
use Closure;
use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use UnexpectedValueException;
use Firebase\JWT\SignatureInvalidException;
use App\Exceptions\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Throwable;

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
        $accessToken = $request->bearerToken();  // string of the token

        if (!$accessToken) {
            throw new UnauthorizedException('Missing token on the Bearer');
        }

        $secretKey = new Key(env('JWT_SECRET'), 'HS256');
        ['id' => $userId, 'email' => $userEmail] = JWT::decode($accessToken, $secretKey);

        $request->userData = [
            'id' => $userId,
            'email' => $userEmail
        ];

        return $next($request);
    }
}
