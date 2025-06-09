<?php

namespace WhatsApp\Stream\Encryption;

use WhatsApp\Stream\Encryption\Algorithms\WhatsAppV1\WhatsAppDecrypt;
use WhatsApp\Stream\Encryption\Exceptions\InvalidArgumentEncrypteException;
use WhatsApp\Stream\Encryption\Exceptions\WhatsAppException;

class DecryptStream extends \WhatsApp\Stream\Encryption\Stream
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
    protected function index(string $typeAppInfo, string $filenameKey) : void
    {
        if (!$this->validateInStream() || !$this->validateOtStream()) {
            throw new InvalidArgumentEncrypteException("Streams is not valid");
        }

        $appAlgo = new WhatsAppDecrypt();

        if(!$appAlgo->validateMediaKey($filenameKey)){
            return;
        }

        $appAlgo->setAppInfo($typeAppInfo);
        $appAlgo->setMediaKeyExpanded();

        $chunks = $this->readFileChunked(self::BATCH_SIZE_DECRYPT);
        $this->writeFileChunked([$appAlgo, 'index'], $chunks);
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