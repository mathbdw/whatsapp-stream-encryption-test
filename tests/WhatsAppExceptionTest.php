<?php

namespace WhatsApp\StreamEncryption\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WhatsApp\StreamEncryption\Exceptions\WhatsAppException;

#[CoversClass(WhatsAppException::class)]class WhatsAppExceptionTest extends TestCase
{
    public function testInstance()
    {
        $instance = new WhatsAppException('message');

        $this->assertInstanceOf(WhatsAppException::class, $instance);
    }
}