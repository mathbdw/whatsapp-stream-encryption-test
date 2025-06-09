<?php

namespace WhatsApp\Stream\Encryption;

use WhatsApp\Stream\Encryption\Algorithms\WhatsAppV1\WhatsAppEncrypt;
use WhatsApp\Stream\Encryption\Exceptions\InvalidArgumentEncrypteException;
use WhatsApp\Stream\Encryption\Exceptions\WhatsAppException;

class EncryptStream extends \WhatsApp\Stream\Encryption\Stream
{

    /**
     * Encrypt data
     *
     * @param string $typeAppInfo
     * @param string $filenameKey
     *
     * @return void
     * @throws \Random\RandomException
     */
    protected function index(string $typeAppInfo, string $filenameKey = '') : void
    {
        if (!$this->validateInStream() || !$this->validateOtStream()) {
            throw new InvalidArgumentEncrypteException("Streams is not valid");
        }

        $appAlgo = new WhatsAppEncrypt();

        if(!empty($filenameKey)) {
            $appAlgo->validateMediaKey($filenameKey);
        } else {
            $appAlgo->generateMediaKey();
        }
        $appAlgo->setAppInfo($typeAppInfo);
        $appAlgo->setMediaKeyExpanded();

        $chunks = $this->readFileChunked(self::BATCH_SIZE_ENCRYPT);
        $this->writeFileChunked([$appAlgo, 'index'], $chunks);

        if(empty($filenameKey)) {
            $this->saveKey($appAlgo->mediaKey);
        }
    }

    /**
     * Save the mediaKey in the same path as streamOut
     *
     * @param string $dataKey
     *
     * @return void
     */
    private function saveKey(string $dataKey) : void
    {
        $info = pathinfo($this->file);
        $res = file_put_contents($info['dirname'] . '/' . $info['filename'] . '.key', $dataKey);

        if($res === false) {
            throw new WhatsAppException("Failed to save mediaKey");
        }
    }
}