<?php

namespace WhatsApp\StreamEncryption\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WhatsApp\StreamEncryption\Algorithms\WhatsAppV1\Cbc;
use WhatsApp\StreamEncryption\Algorithms\WhatsAppV1\CbcEncrypt;
use WhatsApp\StreamEncryption\Exceptions\WhatsAppException;

#[CoversClass(CbcEncrypt::class)]
#[CoversClass(WhatsAppException::class)]
class CbcEncryptTest extends TestCase
{
    public function testExecuteException()
    {
        $cbcEncrypt = new CbcEncrypt();
        $randMediaKey = random_bytes(32);
        $tempFileMediaKey = tempnam(sys_get_temp_dir(), 'testMediaKey');
        file_put_contents($tempFileMediaKey, $randMediaKey);
        $cbcEncrypt->validateMediaKey($tempFileMediaKey);
        $cbcEncrypt->setAppInfo(Cbc::MEDIA_TYPE_AUDIO);
        $cbcEncrypt->setMediaKeyExpanded();

        $this->expectException(WhatsAppException::class);
        $this->expectExceptionMessage('Encryption failed - error:1C80006B:Provider routines::wrong final block length');
        $this->expectExceptionCode(0);
        $cbcEncrypt->execute('data');
    }

    public function testExecute()
    {
        $cbcEncrypt = new CbcEncrypt();
        $randMediaKey = random_bytes(32);
        $tempFileMediaKey = tempnam(sys_get_temp_dir(), 'testMediaKey');
        file_put_contents($tempFileMediaKey, $randMediaKey);
        $cbcEncrypt->validateMediaKey($tempFileMediaKey);
        $cbcEncrypt->setAppInfo(Cbc::MEDIA_TYPE_AUDIO);
        $cbcEncrypt->setMediaKeyExpanded();
        $iv = $cbcEncrypt->mediaKeyExpanded['iv'];

        $res = $cbcEncrypt->execute('DataDataDataData');

        $this->assertEquals(16, strlen($res));
        $this->assertNotEquals($iv, $cbcEncrypt->mediaKeyExpanded['iv']);
    }

    public function testExecuteEof()
    {
        $cbcEncrypt = new CbcEncrypt();
        $randMediaKey = random_bytes(32);
        $tempFileMediaKey = tempnam(sys_get_temp_dir(), 'testMediaKey');
        file_put_contents($tempFileMediaKey, $randMediaKey);
        $cbcEncrypt->validateMediaKey($tempFileMediaKey);
        $cbcEncrypt->setAppInfo(Cbc::MEDIA_TYPE_AUDIO);
        $cbcEncrypt->setMediaKeyExpanded();
        $iv = $cbcEncrypt->mediaKeyExpanded['iv'];

        $res = $cbcEncrypt->execute('DataDataDataData', true);

        $this->assertEquals(42, strlen($res));
    }
}