<?php

namespace WhatsApp\StreamEncryption\Exceptions;

use Throwable;

class InvalidArgumentEncrypteException extends \InvalidArgumentException
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf("Invalid argument when encrypting: %s", $message);

        parent::__construct($message, $code, $previous);
    }
}