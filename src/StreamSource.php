<?php

namespace WhatsApp\StreamEncryption;

use GuzzleHttp\Psr7\StreamDecoratorTrait;
use Psr\Http\Message\StreamInterface;
use WhatsApp\StreamEncryption\Algorithms\CipherMethod;

/**
 * Source Stream decorator for Stream
 *
 * @see CipherMethod Method Encription
 */
class StreamSource implements StreamInterface
{
    use StreamDecoratorTrait;

    private StreamInterface $stream;

    protected CipherMethod $algo;

    /**
     * @param StreamInterface $stream
     * @param CipherMethod    $algo
     */
    public function __construct(StreamInterface $stream, CipherMethod $algo)
    {
        $this->stream = $stream;
        $this->algo = $algo;
    }

    /**
     * Returns encryption chunk
     *
     * @param $length
     *
     * @return string
     */
    public function read($length): string
    {
        $chunk = $this->stream->read($length);

        return $this->algo->execute($chunk, $this->stream->eof());
    }
}