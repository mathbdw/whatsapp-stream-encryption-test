<?php

namespace WhatsApp\Stream\Encryption;

use WhatsApp\Stream\Encryption\Exceptions\WhatsAppException;

class Stream
{
    const BATCH_SIZE_ENCRYPT = 8192;
    //BATCH_SIZE_DECRYPT +26, pad PKCS7 16b + HMAC 10b
    const BATCH_SIZE_DECRYPT = self::BATCH_SIZE_ENCRYPT + 16 + 10;
    public string $file;
    public BaseStream $streamIn;
    public BaseStream $streamOut;

    public function __construct(string $fileInName, string $fileOutName)
    {
        $this->setStreamIn($fileInName);
        $this->setStreamOut($fileOutName);
        $this->file = $fileOutName;
    }

    public function validateInStream(): bool
    {
        if (!$this->streamIn) {
            return false;
        }

        if (!$this->streamIn->isReadable()) {
            return false;
        }

        if($this->streamIn->getSize() === null || $this->streamIn->getSize() === 0) {
            return false;
        }

        return true;
    }
    public function validateOtStream(): bool
    {
        if (!$this->streamOut) {
            return false;
        }

        if (!$this->streamOut->isWritable()) {
            return false;
        }

        return true;
    }

    public function setStreamIn(string $fileName): void
    {
        $this->streamIn = new BaseStream(fopen($fileName, 'rb'));
    }

    public function setStreamOut(string $fileName): void
    {
        $this->streamOut = new BaseStream(fopen($fileName, 'wb'));
    }

    function readFileChunked(int $size): \Generator
    {
        try {
            // to acquire a shared lock (reader)
            $this->streamIn->lock(LOCK_SH);

            while (!$this->streamIn->eof()) {
                $chunk = $this->streamIn->read($size);
                if ($chunk === false) {
                    throw new WhatsAppException("Error while reading chunk");
                }
                yield $chunk;
            }
        } finally {
            //to release a lock (shared or exclusive)
            $this->streamIn->lock(LOCK_UN);
            $this->streamIn->close();
        }
    }

    function writeFileChunked(callable $callback, iterable $chunks): void
    {
        try {
            //to acquire an exclusive lock (writer)
            $this->streamIn->lock(LOCK_EX);
            foreach ($chunks as $chunk) {
                $data = $callback($chunk);
                $bytesWritten = $this->streamOut->write($data);
                if ($bytesWritten === false) {
                    throw new WhatsAppException("Error while writing chunk");
                }
                $this->streamOut->flush();
            }
        } finally {
            //to release a lock (shared or exclusive)
            $this->streamOut->lock(LOCK_UN);
            $this->streamOut->close();
        }
    }
}