<?php

namespace WhatsApp\Stream\Encryption\Exceptions;

use Throwable;

class WhatsAppException extends \RuntimeException
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf("Failed to encryption stream data: %s", $message);

        parent::__construct($message, $code, $previous);
    }
}