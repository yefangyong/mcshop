<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class BusinessException extends Exception
{
    public function __construct(array $codeResponse, Throwable $previous = null)
    {
        list($code, $message) = $codeResponse;
        parent::__construct($message, $code, $previous);
    }
}
