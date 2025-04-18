<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class EventServiceException extends Exception
{
    public function __construct(
        string $message,
        int $code = Response::HTTP_INTERNAL_SERVER_ERROR,
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}