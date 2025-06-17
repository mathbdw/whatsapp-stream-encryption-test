<?php

namespace WhatsApp\StreamEncryption\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WhatsApp\StreamEncryption\Algorithms\WhatsAppV1\Cbc;
use WhatsApp\StreamEncryption\Exceptions\InvalidArgumentException;
use ReflectionClass;

#[CoversClass(Cbc::class)]
#[CoversClass(InvalidArgumentException::class)]
class CbcTest extends TestCase
{
    public function testSetAppInfo()
    {
        $cbc = new Cbc();
        $cbc->setAppInfo("App info");

        $this->assertEquals('App info', $cbc->appInfo);
    }

    public function testGenerateMediaKey()
    {
        $cbc = new Cbc();
        $cbc->generateMediaKey();

        $this->assertEquals(32, strlen($cbc->mediaKey));
    }

    public function testValidateMediaKeyExceptionEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid argument: The filename key does not exist.');
        $this->expectExceptionCode(0);
        $cbc = new Cbc();
        $cbc->validateMediaKey('sdfdf');
    }

    public function testValidateMediaKeyExceptionKeyMedia()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid argument: Media key must be 32 bytes.');
        $this->expectExceptionCode(0);

        $cbc = new Cbc();
        $tempFileMediaKey = tempnam(sys_get_temp_dir(), 'testMediaKey');
        $cbc->validateMediaKey($tempFileMediaKey);
    }

    public function testValidateMediaKeyApply()
    {
        $randMediaKey = random_bytes(32);
        $cbc = new Cbc();
        $tempFileMediaKey = tempnam(sys_get_temp_dir(), 'testMediaKey');
        file_put_contents($tempFileMediaKey, $randMediaKey);
        $cbc->validateMediaKey($tempFileMediaKey);

        $this->assertEquals($randMediaKey, $cbc->mediaKey);
    }

    public function testSetMediaKeyExpanded()
    {
        $randMediaKey = random_bytes(32);
        $cbc = new Cbc();
        $tempFileMediaKey = tempnam(sys_get_temp_dir(), 'testMediaKey');
        file_put_contents($tempFileMediaKey, $randMediaKey);
        $cbc->validateMediaKey($tempFileMediaKey);
        $cbc->setAppInfo(Cbc::MEDIA_TYPE_AUDIO);
        $cbc->setMediaKeyExpanded();

        $this->assertArrayHasKey('iv', $cbc->mediaKeyExpanded);
        $this->assertEquals(16, strlen($cbc->mediaKeyExpanded['iv']));
        $this->assertArrayHasKey('cipherKey', $cbc->mediaKeyExpanded);
        $this->assertEquals(32, strlen($cbc->mediaKeyExpanded['cipherKey']));
        $this->assertArrayHasKey('macKey', $cbc->mediaKeyExpanded);
        $this->assertEquals(32, strlen($cbc->mediaKeyExpanded['macKey']));
        $this->assertArrayHasKey('refKey', $cbc->mediaKeyExpanded);
        $this->assertEquals(32, strlen($cbc->mediaKeyExpanded['refKey']));
    }

    public function testGetTypes()
    {
        $types = Cbc::getTypes();

        $this->assertEquals(4, count($types));
        $this->assertArrayHasKey(Cbc::MEDIA_TYPE_AUDIO, $types);
        $this->assertArrayHasKey(Cbc::MEDIA_TYPE_VIDEO, $types);
        $this->assertArrayHasKey(Cbc::MEDIA_TYPE_IMAGE, $types);
        $this->assertArrayHasKey(Cbc::MEDIA_TYPE_DOCUMENT, $types);
    }

    /**
     * @return void
     * @throws \Random\RandomException
     * @throws \ReflectionException
     */
    public function testHashUpdate()
    {
        $cbc = new Cbc();
        $cbc->setAppInfo(Cbc::MEDIA_TYPE_AUDIO);
        $cbc->generateMediaKey();
        $cbc->setMediaKeyExpanded();

        $reflection = new ReflectionClass($cbc);
        $method = $reflection->getMethod('hashUpdate');
        $method->invokeArgs($cbc, ['Test ']);
        $method->invokeArgs($cbc, ['hash.']);

        $propertyCtxHash = $reflection->getProperty('ctxHash');
        $propertyCtxHash = $propertyCtxHash->getValue($cbc);
        $hash = hash_final($propertyCtxHash);

        // Ожидаемый хеш строки "Hello, world!" в sha256
        $expectedHash = hash_hmac(Cbc::HASH_ALGO, $cbc->mediaKeyExpanded['iv'] . 'Test hash.', $cbc->mediaKeyExpanded['macKey']);
        $this->assertEquals($expectedHash, $hash);
    }

    /**
     * @return void
     * @throws \Random\RandomException
     * @throws \ReflectionException
     */
    public function testGetHash()
    {
        $randMediaKey = random_bytes(32);
        $cbc = new Cbc();
        $tempFileMediaKey = tempnam(sys_get_temp_dir(), 'testMediaKey');
        file_put_contents($tempFileMediaKey, $randMediaKey);
        $cbc->validateMediaKey($tempFileMediaKey);
        $cbc->setAppInfo(Cbc::MEDIA_TYPE_AUDIO);
        $cbc->setMediaKeyExpanded();


        $reflection = new ReflectionClass($cbc);
        $method = $reflection->getMethod('getHash');

        $result = $method->invokeArgs($cbc, []);
        $this->assertEquals(10, strlen($result));
    }
}