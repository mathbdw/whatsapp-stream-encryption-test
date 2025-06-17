<?php

namespace WhatsApp\StreamEncryption\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use WhatsApp\StreamEncryption\Algorithms\WhatsAppV1\Cbc;
use WhatsApp\StreamEncryption\Algorithms\WhatsAppV1\CbcEncrypt;
use WhatsApp\StreamEncryption\EncryptWhatsAppStream;
use WhatsApp\StreamEncryption\Exceptions\WhatsAppException;
use WhatsApp\StreamEncryption\StreamSource;

#[CoversClass(EncryptWhatsAppStream::class)]
#[CoversClass(StreamSource::class)]
#[CoversClass(Cbc::class)]
#[CoversClass(CbcEncrypt::class)]
#[CoversClass(WhatsAppException::class)]
class EncryptWhatsAppStreamTest extends TestCase
{
    private string|false $tempFileRead;
    private string|false $tempFileWrite;

    public function testExecExceptionStreams()
    {
        $enWatsApp = new EncryptWhatsAppStream($this->tempFileRead, $this->tempFileWrite);
        $enWatsApp->setMediaKey('');
        $enWatsApp->setAppInfo(Cbc::MEDIA_TYPE_AUDIO);

        $this->expectException(WhatsAppException::class);
        $this->expectExceptionMessage('Invalid streams.');
        $this->expectExceptionCode(0);
        $enWatsApp->exec();
    }

    public function testExecEmptyFileKey()
    {
        file_put_contents($this->tempFileRead, 'DataDataDataData');
        $enWatsApp = new EncryptWhatsAppStream($this->tempFileRead, $this->tempFileWrite);
        $enWatsApp->setMediaKey('');
        $enWatsApp->setAppInfo(Cbc::MEDIA_TYPE_AUDIO);
        $algo = new CbcEncrypt();
        $enWatsApp->setStreamSource($algo);
        $enWatsApp->setStreamDestination();

        $enWatsApp->exec();

        $reflection = new ReflectionClass($enWatsApp);
        $propertyEncryption = $reflection->getProperty('mediaKey');
        $mediaKey = $propertyEncryption->getValue($enWatsApp);
        $this->assertEmpty($mediaKey);

        $this->assertEquals(32, strlen(file_get_contents(sys_get_temp_dir().'/AUDIO.key')));
        $this->assertNotEmpty(file_get_contents($this->tempFileWrite));
    }

    public function testExecNotEmptyFileKey()
    {
        file_put_contents($this->tempFileRead, 'DataDataDataData');
        $enWatsApp = new EncryptWhatsAppStream($this->tempFileRead, $this->tempFileWrite);
        $enWatsApp->setMediaKey('');
        $enWatsApp->setAppInfo(Cbc::MEDIA_TYPE_AUDIO);
        $algo = new CbcEncrypt();
        $enWatsApp->setStreamSource($algo);
        $enWatsApp->setStreamDestination();

        $fileKey = tempnam(sys_get_temp_dir(), 'testKey');
        file_put_contents($fileKey, random_bytes(32));
        $enWatsApp->exec($fileKey);

        $reflection = new ReflectionClass($enWatsApp);
        $propertyEncryption = $reflection->getProperty('mediaKey');
        $mediaKey = $propertyEncryption->getValue($enWatsApp);
        $this->assertNotEmpty($mediaKey);
    }

    public function testSetMediaKey()
    {
        $encryption = new EncryptWhatsAppStream($this->tempFileRead, $this->tempFileWrite);
        $encryption->setMediaKey('Test MediaKey');

        $reflection = new ReflectionClass($encryption);
        $propertyEncryption = $reflection->getProperty('mediaKey');
        $mediaKey = $propertyEncryption->getValue($encryption);

        $this->assertEquals('Test MediaKey', $mediaKey);
    }

    public function testSetAppInfo()
    {
        $encryption = new EncryptWhatsAppStream($this->tempFileRead, $this->tempFileWrite);
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
        $encryption = new EncryptWhatsAppStream($this->tempFileRead, $this->tempFileWrite);
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
        $encryption = new EncryptWhatsAppStream($this->tempFileRead, $this->tempFileWrite);
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