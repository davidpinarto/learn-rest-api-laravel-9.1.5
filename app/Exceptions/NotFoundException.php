<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class NotFoundException extends Exception
{
    protected $message;
    
    public function __construct($message = "Not Found")
    {
        parent::__construct($message, Response::HTTP_NOT_FOUND);
    }

    public function render($request)
    {
        return response()->json([
            'error' => 'Not Found',
            'message' => $this->getMessage(),
        ], Response::HTTP_NOT_FOUND);
    }
}
