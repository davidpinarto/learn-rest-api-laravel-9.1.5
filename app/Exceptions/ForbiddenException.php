<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class ForbiddenException extends Exception
{
    protected $message;

    public function __construct($message = "Forbidden")
    {
        parent::__construct($message, Response::HTTP_FORBIDDEN);
    }

    public function render($request)
    {
        return response()->json([
            'error' => 'Forbidden',
            'message' => $this->getMessage(),
        ], Response::HTTP_FORBIDDEN);
    }
}
