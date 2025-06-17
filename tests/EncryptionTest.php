<?php

namespace WhatsApp\StreamEncryption\Tests;

use GuzzleHttp\Psr7\Stream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use WhatsApp\StreamEncryption\Algorithms\WhatsAppV1\CbcEncrypt;
use WhatsApp\StreamEncryption\Encryption;
use WhatsApp\StreamEncryption\EncryptWhatsAppStream;
use WhatsApp\StreamEncryption\StreamSource;

#[CoversClass(Encryption::class)]
#[CoversClass(StreamSource::class)]
#[CoversClass(EncryptWhatsAppStream::class)]
class EncryptionTest extends TestCase
{
    private string|false $tempFileRead;
    private string|false $tempFileWrite;

    public function testValidateStreamSourceEmpty()
    {
        $encryption = new EncryptWhatsAppStream($this->tempFileRead, $this->tempFileWrite);
        $res = $encryption->validateStreamSource();

        $this->assertFalse($res);
    }

    public function testValidateStreamSourceOnlyWrite()
    {
        $encryption = new Encryption($this->tempFileRead, $this->tempFileWrite);

        $reflection = new ReflectionClass($encryption);
        $propertyStreamDestination = $reflection->getProperty('streamSource');
        $propertyStreamDestination->setValue($encryption, new Stream(fopen($this->tempFileRead, 'wb')));
        $res = $encryption->validateStreamSource();

        $this->assertFalse($res);
    }

    public function testValidateStreamSourceZero()
    {
        $encryption = new Encryption($this->tempFileRead, $this->tempFileWrite);

        $reflection = new ReflectionClass($encryption);
        $propertyStreamDestination = $reflection->getProperty('streamSource');
        $propertyStreamDestination->setValue($encryption, new Stream(fopen($this->tempFileRead, 'rb')));
        $res = $encryption->validateStreamSource();

        $this->assertFalse($res);
    }

    public function testValidateStreamSourceApply()
    {
        $encryption = new Encryption($this->tempFileRead, $this->tempFileWrite);

        $reflection = new ReflectionClass($encryption);
        $propertyStreamDestination = $reflection->getProperty('streamSource');
        file_put_contents($this->tempFileRead, 'data');
        $propertyStreamDestination->setValue($encryption, new Stream(fopen($this->tempFileRead, 'rb')));
        $res = $encryption->validateStreamSource();

        $this->assertTrue($res);
    }

    public function testValidateStreamDestinationEmpty()
    {
        $encryption = new EncryptWhatsAppStream($this->tempFileRead, $this->tempFileWrite);
        $res = $encryption->validateStreamDestination();

        $this->assertFalse($res);
    }

    public function testValidateStreamDestinationOnlyRead()
    {
        $encryption = new Encryption($this->tempFileRead, $this->tempFileWrite);

        $reflection = new ReflectionClass($encryption);
        $propertyStreamDestination = $reflection->getProperty('streamDestination');
        $propertyStreamDestination->setValue($encryption, new Stream(fopen($this->tempFileRead, 'rb')));
        $res = $encryption->validateStreamDestination();

        $this->assertFalse($res);
    }

    public function testValidateStreamDestinationApply()
    {
        $encryption = new Encryption($this->tempFileRead, $this->tempFileWrite);

        $reflection = new ReflectionClass($encryption);
        $propertyStreamDestination = $reflection->getProperty('streamDestination');
        $propertyStreamDestination->setValue($encryption, new Stream(fopen($this->tempFileRead, 'wb')));
        $res = $encryption->validateStreamDestination();

        $this->assertTrue($res);
    }

    public function testClose()
    {
        $encryption = new EncryptWhatsAppStream($this->tempFileRead, $this->tempFileWrite);
        $algo = new CbcEncrypt();
        $encryption->setStreamSource($algo);
        $encryption->setStreamDestination();
        $encryption->closeStreams();

        $reflection = new \ReflectionClass($encryption);
        $propertyStreamSource = $reflection->getProperty('streamSource');
        $this->assertTrue(!is_resource($propertyStreamSource->getValue($encryption)));

        $propertyStreamSource = $reflection->getProperty('streamDestination');
        $this->assertTrue(!is_resource($propertyStreamSource->getValue($encryption)));
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