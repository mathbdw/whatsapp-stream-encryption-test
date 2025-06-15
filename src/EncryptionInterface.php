<?php

namespace WhatsApp\StreamEncryption;

use WhatsApp\StreamEncryption\Algorithms\CipherMethod;

interface EncryptionInterface
{
    /**
     * Source Stream Validation
     *
     * @return bool
     */
    public function validateStreamSource(): bool;

    /**
     * Destination Stream Validation
     *
     * @return bool
     */
    public function validateStreamDestination(): bool;

    /**
     * Sets an instance of StreamSource
     *
     * @param CipherMethod $algo
     *
     * @return mixed
     */
    public function setStreamSource(CipherMethod $algo);

    /**
     *  Sets an instance of Stream
     *
     * @return mixed
     */
    public function setStreamDestination();

    /**
     * @return void
     */
    public function index(): void;
}