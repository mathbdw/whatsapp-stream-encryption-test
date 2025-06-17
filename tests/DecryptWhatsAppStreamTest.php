<?php

namespace WhatsApp\StreamEncryption\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Random\RandomException;
use ReflectionClass;
use ReflectionException;
use WhatsApp\StreamEncryption\Algorithms\WhatsAppV1\Cbc;
use WhatsApp\StreamEncryption\Algorithms\WhatsAppV1\CbcDecrypt;
use WhatsApp\StreamEncryption\Algorithms\WhatsAppV1\CbcEncrypt;
use WhatsApp\StreamEncryption\DecryptWhatsAppStream;
use WhatsApp\StreamEncryption\Exceptions\WhatsAppException;
use WhatsApp\StreamEncryption\StreamSource;

#[CoversClass(DecryptWhatsAppStream::class)]
#[CoversClass(StreamSource::class)]
#[CoversClass(Cbc::class)]
#[CoversClass(CbcEncrypt::class)]
#[CoversClass(CbcDecrypt::class)]
#[CoversClass(WhatsAppException::class)]
class DecryptWhatsAppStreamTest extends TestCase
{
    private string|false $tempFileRead;
    private string|false $tempFileWrite;

    public function testExecExceptionStreams()
    {
        $deWatsApp = new DecryptWhatsAppStream($this->tempFileRead, $this->tempFileWrite);
        $deWatsApp->setAppInfo(Cbc::MEDIA_TYPE_AUDIO);
        $fileKey = tempnam(sys_get_temp_dir(), 'testKey');
        file_put_contents($fileKey, random_bytes(32));

        $this->expectException(WhatsAppException::class);
        $this->expectExceptionMessage('Invalid streams.');
        $this->expectExceptionCode(0);
        $deWatsApp->exec($fileKey);
    }

    /**
     * @throws Exception
     * @throws RandomException
     */
    public function testExecExceptionMac()
    {
        file_put_contents($this->tempFileRead, 'DataDataDataDataHMACString');
        $deWatsApp = new DecryptWhatsAppStream($this->tempFileRead, $this->tempFileWrite);
        $deWatsApp->setAppInfo(Cbc::MEDIA_TYPE_AUDIO);

        $fileKey = tempnam(sys_get_temp_dir(), 'testKey');
        file_put_contents($fileKey, random_bytes(32));

        $this->expectException(WhatsAppException::class);
        $this->expectExceptionMessage('Failed to encryption stream data: MAC verification failed');
        $this->expectExceptionCode(0);

        $deWatsApp->exec($fileKey);
    }

    /**
     * @throws Exception
     * @throws RandomException
     */
    public function testExecExceptionFileContent()
    {
        file_put_contents($this->tempFileRead, 'DataDataDataData');
        $deWatsApp = new DecryptWhatsAppStream($this->tempFileRead, $this->tempFileWrite);
        $deWatsApp->setAppInfo(Cbc::MEDIA_TYPE_AUDIO);

        $fileKey = tempnam(sys_get_temp_dir(), 'testKey');
        file_put_contents($fileKey, random_bytes(32));

        $this->expectException(WhatsAppException::class);
        $this->expectExceptionMessage('Failed to encryption stream data: Decryption failed - error:1C80006B:Provider routines::wrong final block length');
        $this->expectExceptionCode(0);

        $deWatsApp->exec($fileKey);
    }

    public function testSetMediaKey()
    {
        $encryption = new DecryptWhatsAppStream($this->tempFileRead, $this->tempFileWrite);
        $encryption->setMediaKey('Test MediaKey');

        $reflection = new ReflectionClass($encryption);
        $propertyEncryption = $reflection->getProperty('mediaKey');
        $mediaKey = $propertyEncryption->getValue($encryption);

        $this->assertEquals('Test MediaKey', $mediaKey);
    }

    public function testSetAppInfo()
    {
        $encryption = new DecryptWhatsAppStream($this->tempFileRead, $this->tempFileWrite);
        $encryption->setAppInfo('Test Info');

        $reflection = new ReflectionClass($encryption);
        $propertyEncryption = $reflection->getProperty('appInfo');
        $appInfo = $propertyEncryption->getValue($encryption);

        $this->assertEquals('Test Info', $appInfo);
    }

    /**
     * @throws ReflectionException
     */
    public function testSetStreamSource()
    {
        $encryption = new DecryptWhatsAppStream($this->tempFileRead, $this->tempFileWrite);
        $algo = new CbcEncrypt();
        $encryption->setStreamSource($algo);

        $reflection = new ReflectionClass($encryption);
        $propertyStreamSource = $reflection->getProperty('streamSource');
        $streamSource = $propertyStreamSource->getValue($encryption);

        $reflectionSource = new ReflectionClass($streamSource);
        $propertySourceStream = $reflectionSource->getProperty('stream');
        $stream = $propertySourceStream->getValue($streamSource);

        $reflectionSourceStream = new ReflectionClass($stream);
        $propertyStream = $reflectionSourceStream->getProperty('stream');

        $this->assertTrue(is_resource($propertyStream->getValue($stream)));
    }

    /**
     * @throws ReflectionException
     */
    public function testSetStreamDestination()
    {
        $encryption = new DecryptWhatsAppStream($this->tempFileRead, $this->tempFileWrite);
        $encryption->setStreamDestination();

        $reflection = new ReflectionClass($encryption);
        $propertyStreamDestination = $reflection->getProperty('streamDestination');
        $streamDestination = $propertyStreamDestination->getValue($encryption);

        $reflectionDestination = new ReflectionClass($streamDestination);
        $propertyStream = $reflectionDestination->getProperty('stream');

        $this->assertTrue(is_resource($propertyStream->getValue($streamDestination)));
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