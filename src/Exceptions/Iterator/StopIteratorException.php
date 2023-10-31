<?php

namespace Sholokhov\Utils\Exceptions\Iterator;

use Exception;

use Throwable;

class StopIteratorException extends Exception
{
    public function __construct(string $message = "Collection empty", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}