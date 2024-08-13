<?php

namespace App\Exceptions;

use App\Exceptions\ForbiddenException;
use App\Exceptions\InvariantException;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\NotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Throwable;
use UnexpectedValueException;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e): JsonResponse
    {
        if ($e instanceof ValidationException) {
            $response = [
                'status' => 'fail',
                'message' => $e->validator->errors()->first()
            ];
            return response()->json($response, 400);
        }

        if (
            $e instanceof InvariantException
            || $e instanceof QueryException
        ) {
            $response = [
                'status' => 'fail',
                'message' => $e->getMessage(),
            ];
            return response()->json($response, 400);
        }

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

        if ($e instanceof ForbiddenException) {
            $response = [
                'status' => 'fail',
                'message' => $e->getMessage(),
            ];
            return response()->json($response, 403);
        }

        if ($e instanceof NotFoundException) {
            $response = [
                'status' => 'fail',
                'message' => $e->getMessage(),
            ];
            return response()->json($response, 404);
        }

        $response = [
            'status' => 'fail',
            'message' => 'There is something error on our server',
        ];
        return response()->json($response, 500);
    }
}
