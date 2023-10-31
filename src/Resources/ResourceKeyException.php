<?php

namespace Sholokhov\Utils\Resources;

use Throwable;

use Sholokhov\Utils\Exceptions\KeyExistException;

class ResourceKeyException extends KeyExistException
{
    public function __construct(string $message = "Undefined resource key", ?Throwable $previous = null)
    {
        parent::__construct($message, 20, $previous);
    }
}