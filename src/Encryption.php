<?php

namespace WhatsApp\StreamEncryption;

use Psr\Http\Message\StreamInterface;

class Encryption
{
    /**
     * @var string Path to the source
     */
    public string $pathSource;

    /**
     * @var string Path to the destination
     */
    public string $pathDestination;

    /**
     * @var StreamInterface
     */
    protected StreamInterface $streamSource;

    /**
     * @var StreamInterface
     */
    protected StreamInterface $streamDestination;

    /**
     * @param string $source Path to the source
     * @param string $destination Path to the destination
     */
    public function __construct(string $source, string $destination)
    {
        $this->pathSource = $source;
        $this->pathDestination = $destination;
    }

    public function validateStreamSource(): bool
    {
        if (!$this->streamSource) {
            return false;
        }

        if (!$this->streamSource->isReadable()) {
            return false;
        }

        if ($this->streamSource->getSize() === null || $this->streamSource->getSize() === 0) {
            return false;
        }

        return true;
    }

    public function validateStreamDestination(): bool
    {
        if (!$this->streamDestination) {
            return false;
        }

        if (!$this->streamDestination->isWritable()) {
            return false;
        }

        return true;
    }

    /**
     * @return void
     */
    public function closeStreams() : void
    {
        $this->streamSource->close();
        $this->streamDestination->close();
    }
}