<?php

namespace WhatsApp\StreamEncryption\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WhatsApp\StreamEncryption\Algorithms\WhatsAppV1\Cbc;
use WhatsApp\StreamEncryption\Exceptions\WhatsAppException;
use WhatsApp\StreamEncryption\Models\EncryptWhatsAppStreamDocument;
use WhatsApp\StreamEncryption\StreamSource;

#[CoversClass(StreamSource::class)]
#[CoversClass(Cbc::class)]
#[CoversClass(WhatsAppException::class)]
#[CoversClass(EncryptWhatsAppStreamDocument::class)]
class EncryptWhatsAppStreamDocumentTest extends TestCase
{
    private string|false $tempFileRead;
    private string|false $tempFileWrite;

    public function testExecExceptionStreams()
    {
        $this->expectException(WhatsAppException::class);
        $this->expectExceptionMessage('Invalid streams.');
        $this->expectExceptionCode(0);

        $instance = new EncryptWhatsAppStreamDocument($this->tempFileRead, $this->tempFileWrite);
        $instance->exec();
    }

    protected function setUp(): void
    {
        $this->tempFileRead = tempnam(sys_get_temp_dir(), 'testRead');
        $this->tempFileWrite = tempnam(sys_get_temp_dir(), 'testWrite');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tempFileRead)) {
            unlink($this->tempFileRead);
        }

        if (file_exists($this->tempFileWrite)) {
            unlink($this->tempFileWrite);
        }
    }
}