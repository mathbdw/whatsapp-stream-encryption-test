<?php

namespace WhatsApp\StreamEncryption\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WhatsApp\StreamEncryption\Exceptions\InvalidArgumentException;

#[CoversClass(InvalidArgumentException::class)]class InvalidArgumentExceptionTest extends TestCase
{
    public function testInstance()
    {
        $instance = new InvalidArgumentException('message');

        $this->assertInstanceOf(InvalidArgumentException::class, $instance);
    }
}