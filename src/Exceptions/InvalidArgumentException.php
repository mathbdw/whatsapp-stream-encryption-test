<?php

namespace WhatsApp\StreamEncryption\Exceptions;

use Throwable;

class InvalidArgumentException extends \InvalidArgumentException
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf("Invalid argument: %s", $message);

        parent::__construct($message, $code, $previous);
    }
}