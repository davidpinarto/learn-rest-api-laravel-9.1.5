<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class UnauthorizedException extends Exception
{
    protected $message;

    public function __construct($message = "Unauthorized")
    {
        parent::__construct($message, Response::HTTP_UNAUTHORIZED);
    }

    public function render($request)
    {
        return response()->json([
            'error' => 'Unauthorized',
            'message' => $this->getMessage(),
        ], Response::HTTP_UNAUTHORIZED);
    }
}
