<?php

namespace WhatsApp\StreamEncryption;

use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7\Utils;
use WhatsApp\StreamEncryption\Algorithms\CipherMethod;
use WhatsApp\StreamEncryption\Algorithms\WhatsAppV1\CbcDecrypt;
use WhatsApp\StreamEncryption\Exceptions\WhatsAppException;

class DecryptWhatsAppStream extends Encryption implements EncryptionInterface
{
    /**
     * @var string Path the Media Key for CipherMethod
     */
    private string $mediaKey;

    /**
     * @var string Type file for CipherMethod
     */
    private string $appInfo;

    /**
     * Initialize the CipherMethod and set the data for it. Write the StreamDestination according to this CipherMethod.
     *
     * @return void
     */
    public function index(): void
    {
        $algo = new CbcDecrypt();

        $algo->validateMediaKey($this->mediaKey);
        $algo->setAppInfo($this->appInfo);
        $algo->setMediaKeyExpanded();

        $this->setStreamSource($algo);
        $this->setStreamDestination();
        if (!$this->validateStreamSource() || !$this->validateStreamDestination()) {
            throw new WhatsAppException('Invalid streams.');
        }

        Utils::copyToStream($this->streamSource, $this->streamDestination);
        $this->closeStreams();
    }

    /**
     * @param string $filenameKey
     *
     * @return void
     */
    public function exec(string $filenameKey)
    {
        $this->setMediaKey($filenameKey);
        $this->index();
    }

    /**
     * Sets the MediaKey for CipherMethod
     *
     * @param string $mediaKey
     *
     * @return void
     */
    public function setMediaKey(string $mediaKey): void
    {
        $this->mediaKey = $mediaKey;
    }

    /**
     * Sets the MediaKey for CipherMethod
     *
     * @param string $appInfo
     *
     * @return void
     */
    public function setAppInfo(string $appInfo): void
    {
        $this->appInfo = $appInfo;
    }

    public function setStreamSource(CipherMethod $algo): void
    {
        $stream = new Stream(fopen($this->pathSource, 'rb'));
        $this->streamSource = new StreamSource($stream, $algo);
    }

    public function setStreamDestination(): void
    {
        $this->streamDestination = new Stream(fopen($this->pathDestination, 'wb'));
    }
}