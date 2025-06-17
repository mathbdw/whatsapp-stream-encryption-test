<?php

namespace WhatsApp\StreamEncryption\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WhatsApp\StreamEncryption\Algorithms\WhatsAppV1\Cbc;
use WhatsApp\StreamEncryption\Algorithms\WhatsAppV1\CbcDecrypt;
use WhatsApp\StreamEncryption\Exceptions\WhatsAppException;

#[CoversClass(CbcDecrypt::class)]
#[CoversClass(WhatsAppException::class)]
class CbcDecryptTest extends TestCase
{
    public function testExecuteException()
    {
        $cbcDecrypt = new CbcDecrypt();
        $randMediaKey = random_bytes(32);
        $tempFileMediaKey = tempnam(sys_get_temp_dir(), 'testMediaKey');
        file_put_contents($tempFileMediaKey, $randMediaKey);
        $cbcDecrypt->validateMediaKey($tempFileMediaKey);
        $cbcDecrypt->setAppInfo(Cbc::MEDIA_TYPE_AUDIO);
        $cbcDecrypt->setMediaKeyExpanded();

        $this->expectException(WhatsAppException::class);
        $this->expectExceptionMessage('Decryption failed - error:1C80006B:Provider routines::wrong final block length');
        $this->expectExceptionCode(0);
        $cbcDecrypt->execute('data');
    }

    public function testExecute()
    {
        $cbcDecrypt = new CbcDecrypt();
        $randMediaKey = random_bytes(32);
        $tempFileMediaKey = tempnam(sys_get_temp_dir(), 'testMediaKey');
        file_put_contents($tempFileMediaKey, $randMediaKey);
        $cbcDecrypt->validateMediaKey($tempFileMediaKey);
        $cbcDecrypt->setAppInfo(Cbc::MEDIA_TYPE_AUDIO);
        $cbcDecrypt->setMediaKeyExpanded();
        $iv = $cbcDecrypt->mediaKeyExpanded['iv'];

        $res = $cbcDecrypt->execute('DataDataDataData');

        $this->assertEquals(16, strlen($res));
        $this->assertNotEquals($iv, $cbcDecrypt->mediaKeyExpanded['iv']);
    }

    public function testExecuteOpenSslException()
    {
        $cbcDecrypt = new CbcDecrypt();
        $randMediaKey = random_bytes(32);
        $tempFileMediaKey = tempnam(sys_get_temp_dir(), 'testMediaKey');
        file_put_contents($tempFileMediaKey, $randMediaKey);
        $cbcDecrypt->validateMediaKey($tempFileMediaKey);
        $cbcDecrypt->setAppInfo(Cbc::MEDIA_TYPE_AUDIO);
        $cbcDecrypt->setMediaKeyExpanded();

        $this->expectException(WhatsAppException::class);
        $this->expectExceptionMessage('Decryption failed - error:1C80006B:Provider routines::wrong final block length');
        $this->expectExceptionCode(0);
        $cbcDecrypt->execute('DataDataDataData', true);
    }

    public function testExecuteHMACException()
    {
        $cbcDecrypt = new CbcDecrypt();
        $randMediaKey = random_bytes(32);
        $tempFileMediaKey = tempnam(sys_get_temp_dir(), 'testMediaKey');
        file_put_contents($tempFileMediaKey, $randMediaKey);
        $cbcDecrypt->validateMediaKey($tempFileMediaKey);
        $cbcDecrypt->setAppInfo(Cbc::MEDIA_TYPE_AUDIO);
        $cbcDecrypt->setMediaKeyExpanded();

        $this->expectException(WhatsAppException::class);
        $this->expectExceptionMessage('MAC verification failed');
        $this->expectExceptionCode(0);
        $cbcDecrypt->execute('DataDataDataDataHMACString', true);
    }
}